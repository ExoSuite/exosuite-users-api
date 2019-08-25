<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateRecordRequest;
use App\Http\Requests\UpdateRecordRequest;
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
     * @param array<string> $data
     * @param \App\Models\Record $record
     * @return \App\Models\Record
     */
    public function checkForUpdates(array $data, Record $record): Record
    {
        $user_run = UserRun::findOrFail($data['user_run_id']);
        $curr_times = $user_run->times()->get();
        $last_time = $curr_times->last();
        $last_time_timestamp = $last_time->current_time;
        $first_time = $curr_times->first();
        $first_time_timestamp = $first_time->current_time;
        $total_time = $last_time_timestamp - $first_time_timestamp;

        if ($record->best_time === -1 || $record->best_time > $total_time) {
            $record->best_time = $total_time;
            $record->best_time_user_run_id = $data['user_run_id'];
        }

        $curr_segments = [];

        for ($i = 0; $i !== count($curr_times) - 1; $i++) {
            $time = $curr_times[$i + 1]->current_time - $curr_times[$i]->current_time;
            array_push($curr_segments, $time);
        }

        $final_segments_data = explode(",", $record->best_segments);

        for ($i = 0; $i !== count($final_segments_data); $i++) {
            if ((int) $final_segments_data[$i] <= $curr_segments[$i] && (int) $final_segments_data[$i] !== -1) {
                continue;
            }

            $final_segments_data[$i] = $curr_segments[$i];
        }

        $record->best_segments = implode(",", $final_segments_data);
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
        $cp_nbr = $run->checkpoints()->count() - 1;
        $array = array_fill(0, $cp_nbr, -1);
        $segments_str = implode(",", $array);

        return $this->created(
            Record::create([
                "run_id" => $run->id,
                "best_time" => -1,
                "best_time_user_run_id" => $start_user_run_id,
                "sum_of_best" => -1,
                "user_id" => Auth::user()->id,
                "best_segments" => $segments_str,
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
        $data = $run->record()->latest()->get();

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
        UserRun::whereId($record->id)->delete();

        return $this->noContent();
    }
}
