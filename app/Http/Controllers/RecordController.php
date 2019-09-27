<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateRecordRequest;
use App\Http\Requests\UpdateRecordRequest;
use App\Models\CheckPoint;
use App\Models\Record;
use App\Models\Run;
use App\Models\User;
use App\Models\UserRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class RecordController
 *
 * @package App\Http\Controllers
 */
class RecordController extends Controller
{

    /**
     * @param float $degrees
     * @return float|int
     */
    public function degreesToRadians(float $degrees)
    {
        return $degrees * pi() / 180;
    }

    /**
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float|int
     */
    public function distanceInKmBetweenEarthCoordinates(float $lat1, float $lon1, float $lat2, float $lon2)
    {
        $earthRadiusKm = 6371;

        $dLat = $this->degreesToRadians($lat2 - $lat1);
        $dLon = $this->degreesToRadians($lon2 - $lon1);

        $lat1 = $this->degreesToRadians($lat1);
        $lat2 = $this->degreesToRadians($lat2);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            sin($dLon / 2) * sin($dLon / 2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * @param array<string> $data
     * @param \App\Models\Record $record
     * @return \App\Models\Record
     */
    public function checkForUpdates(array $data, Record $record): Record
    {
        $user_run = UserRun::findOrFail($data['user_run_id']);
        $curr_times = $user_run->times()->get();
        $distances = Run::findOrFail($user_run->run)->checkpoints()->get();
        $last_time = $curr_times->last();
        $last_time_timestamp = $last_time->current_time;
        $first_time = $curr_times->first();
        $first_time_timestamp = $first_time->current_time;
        $total_time = $last_time_timestamp - $first_time_timestamp;

        $curr_segments = [];
        $km_between_cps = [];
        $speed_between_cps = [];

        for ($i = 0; $i !== count($curr_times) - 1; $i++) {

            $time = $curr_times[$i + 1]->current_time - $curr_times[$i]->current_time;
            array_push($curr_segments, $time);
        }

        $final_segments_data = $record->best_segments;
        $record->total_distance = 0;
        $record->sum_of_best = 0;

        for ($i = 0; $i !== count($final_segments_data); $i++) {
            $dist1 = CheckPoint::findOrFail($distances[$i]['id'])
                ->getLocation()->jsonSerialize()->getCoordinates()[0][$i];
            $dist2 = CheckPoint::findOrFail($distances[$i]['id'])
                ->getLocation()->jsonSerialize()->getCoordinates()[0][$i + 1];
            $segment_distance = $this->distanceInKmBetweenEarthCoordinates(
                $dist1[0],
                $dist1[1],
                $dist2[0],
                $dist2[1]
            );
            array_push($km_between_cps, $segment_distance);

            if ($final_segments_data[$i] <= $curr_segments[$i] && $final_segments_data[$i] !== -1) {
                $segment_speed = $segment_distance / ($final_segments_data[$i] / 3600);
                array_push($speed_between_cps, $segment_speed);
                $record->sum_of_best += $final_segments_data[$i];
                $record->total_distance += $segment_distance;

                continue;
            }

            $final_segments_data[$i] = $curr_segments[$i];
            $segment_speed = $segment_distance / ($final_segments_data[$i] / 3600);
            array_push($speed_between_cps, $segment_speed);
            $record->sum_of_best += $final_segments_data[$i];
            $record->total_distance += $segment_distance;
        }

        if ($record->best_time === -1 || $record->best_time > $total_time) {
            $record->best_time = $total_time;
            $record->best_time_user_run_id = $data['user_run_id'];
            $record->average_speed_on_best_time = $record->total_distance / ($record->best_time / 3600);
        }

        $record->best_segments = $final_segments_data;
        $record->distance_between_cps = $km_between_cps;
        $record->best_speed_between_cps = $speed_between_cps;
        $record->save();

        return $record;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateRecordRequest $request
     * @param \App\Models\Run $run
     * @param \App\Models\Record $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRecordRequest $request, Run $run, Record $record): JsonResponse
    {
        $data = $request->validated();

        return $this->ok($this->checkForUpdates($data, $record));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateRecordRequest $request
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRecordRequest $request, Run $run): JsonResponse
    {
        $request->validated();
        $start_user_run_id = $request->get('user_run_id');
        $cps = $run->checkpoints()->get();
        $cp_count = count($cps);
        $array = array_fill(0, $cp_count - 1, -1);

        return $this->created(
            Record::create([
                "run_id" => $run->id,
                "best_time" => -1,
                "best_time_user_run_id" => $start_user_run_id,
                "sum_of_best" => -1,
                "user_id" => Auth::user()->id,
                "best_segments" => $array,
                'total_distance' => -1,
                'average_speed_on_best_time' => -1,
                'distance_between_cps' => $array,
                'best_speed_between_cps' => $array,
            ])
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(?User $user, Run $run): JsonResponse
    {
        $data = $run->record()->first();

        return $this->ok($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Run $run
     * @param \App\Models\Record $record
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run, Record $record): JsonResponse
    {
        $record->delete();

        return $this->noContent();
    }
}
