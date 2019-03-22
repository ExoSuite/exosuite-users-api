<?php declare(strict_types = 1);

namespace App\Http\Controllers\CheckPoint;

use App\Enums\CheckPointType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckPoint\CreateCheckPointRequest;
use App\Http\Requests\CheckPoint\UpdateCheckPointRequest;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\Polygon;

/**
 * Class CheckPointController
 *
 * @package App\Http\Controllers\CheckPoint
 */
class CheckPointController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Run $run
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(?User $user, Run $run): JsonResponse
    {
        return $this->ok($run->checkpoints()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CheckPoint\CreateCheckPointRequest $request
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCheckPointRequest $request, Run $run): JsonResponse
    {
        $data = $request->validated();
        if ($data['type'] === CheckPointType::START) {
            $checkpoints = $run->checkpoints()->get()->toArray();

            foreach ($checkpoints as $checkpt) {
                if ($checkpt['type'] === CheckPointType::START) {
                    return $this->badRequest("You can't have more than one checkpoint of type start");
                }
            }
        }

        $points = collect($request->get("location"))->map(static function ($point) {
            return new Point($point[1], $point[0]);
        });

        if ($data['type'] === CheckPointType::ARRIVAL or $data['type'] === CheckPointType::DEFAULT) {
            $last_checkpoint = $run->checkpoints()->orderBy('created_at', 'desc')->first();
            $data['previous_checkpoint_id'] = $last_checkpoint->id;
        } else
            $data['previous_checkpoint_id'] = null;

        $data['location'] = new Polygon([new LineString($points->toArray())]);
        $data['run_id'] = $run->id;
        $checkpoint = CheckPoint::create($data);

        return $this->created($checkpoint);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkPoint
     * @param \App\Models\Time $time
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(?User $user, Run $run, CheckPoint $checkPoint, Time $time): JsonResponse
    {
        $checkpoint = CheckPoint::findOrFail($checkPoint->id);

        return $this->ok($checkpoint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CheckPoint\UpdateCheckPointRequest $request
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkpoint
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCheckPointRequest $request, Run $run, CheckPoint $checkpoint): JsonResponse
    {
        $data = $request->validated();

        if ($data['type'] === CheckPointType::START) {
            $checkpoints = $run->checkpoints()->get()->toArray();
            foreach ($checkpoints as $checkpt) {
                if ($checkpt['id'] !== $checkpoint->id) {
                    if ($checkpt['type'] === CheckPointType::START) {
                        return $this->badRequest("You can't have more than one checkpoint of type start");
                    }
                }
            }
        }
        $points = collect($request->get("location"))->map(static function ($point) {
            return new Point($point[1], $point[0]);
        });
        if ($data['type'] === CheckPointType::ARRIVAL or $data['type'] === CheckPointType::DEFAULT) {
            $last_checkpoint = $run->checkpoints()->orderBy('created_at', 'desc')->first();
            $data['previous_checkpoint_id'] = $last_checkpoint->id;
        } else {
            $data['previous_checkpoint_id'] = null;
        }

        $data['location'] = new Polygon([new LineString($points->toArray())]);
        $checkpoint->update($data);

        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkpoint
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run, CheckPoint $checkpoint): JsonResponse
    {
        CheckPoint::whereId($checkpoint->id)->delete();

        return $this->noContent();
    }
}
