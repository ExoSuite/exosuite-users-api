<?php declare(strict_types = 1);

namespace App\Http\Controllers\CheckPoint;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckPoint\CreateCheckPointRequest;
use App\Http\Requests\CheckPoint\UpdateCheckPointRequest;
use App\Models\CheckPoint;
use App\Models\Run;
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
     * @return void
     */
    public function index(): void
    {
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
        $points = collect($request->get("location"))->map(static function ($point) {
            return new Point($point[1], $point[0]);
        });
        $data['location'] = new Polygon([new LineString($points->toArray())]);
        $data['run_id'] = $run->id;
        //dd($run->id, $data);
        $checkpoint = CheckPoint::create($data);

        return $this->created($checkpoint);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CheckPoint $checkPointParam
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CheckPoint $checkPointParam, Run $run): JsonResponse
    {
        $checkpoint = CheckPoint::findOrFail($checkPointParam->id);

        return $this->ok($checkpoint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\CheckPoint\UpdateCheckPointRequest $request
     * @param \App\Models\CheckPoint $checkpoint
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCheckPointRequest $request, CheckPoint $checkpoint): JsonResponse
    {
        CheckPoint::whereId($checkpoint->id)->update($request->validated());

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
