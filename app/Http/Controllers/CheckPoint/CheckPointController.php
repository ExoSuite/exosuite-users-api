<?php declare(strict_types = 1);

namespace App\Http\Controllers\CheckPoint;

use App\Enums\CheckPointType;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckPoint\CreateCheckPointRequest;
use App\Http\Requests\CheckPoint\UpdateCheckPointRequest;
use App\Models\CheckPoint;
use App\Models\Run;
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
    public const GET_PER_PAGE = 15;

    /**
     * @param array<mixed> $location
     * @return \Phaza\LaravelPostgis\Geometries\Polygon
     */
    public static function createPolygonFromArray(array $location): Polygon
    {
        $points = collect($location)->map(static function ($point) {
            return new Point($point[1], $point[0]);
        });

        return new Polygon([new LineString($points->toArray())]);
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
        return $this->ok($run->checkpoints()->latest()->paginate(self::GET_PER_PAGE));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CheckPoint\CreateCheckPointRequest $request
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function store(CreateCheckPointRequest $request, Run $run): JsonResponse
    {
        $data = $request->validated();
        /** @var \App\Enums\CheckPointType $checkpointType */
        $checkpointType = CheckPointType::getInstance($data['type']);

        if ($checkpointType->isArrivalOrDefault()) {
            $last_checkpoint = $run->checkpoints()->orderBy('created_at', 'desc')->first();
            $data['previous_checkpoint_id'] = $last_checkpoint->id;
        } else {
            $data['previous_checkpoint_id'] = null;
        }

        $data['location'] = self::createPolygonFromArray($request['location']);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(?User $user, Run $run, CheckPoint $checkPoint): JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \BenSampo\Enum\Exceptions\InvalidEnumMemberException
     */
    public function update(UpdateCheckPointRequest $request, Run $run, CheckPoint $checkpoint): JsonResponse
    {
        $data = $request->validated();
        /** @var \App\Enums\CheckPointType $checkpointType */
        $checkpointType = CheckPointType::getInstance($data['type']);

        $points = collect($request->get("location"))->map(static function ($point): Point {
            return new Point($point[1], $point[0]);
        });

        if ($checkpointType->isArrivalOrDefault()) {
            $last_checkpoint = $run->checkpoints()->latest()->first();
            $data['previous_checkpoint_id'] = $last_checkpoint->id;
        } else {
            $data['previous_checkpoint_id'] = null;
        }

        $data['location'] = new Polygon([new LineString($points->toArray())]);
        $checkpoint->update($data);

        return $this->ok($checkpoint);
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
        $checkpoint->delete();

        return $this->noContent();
    }
}
