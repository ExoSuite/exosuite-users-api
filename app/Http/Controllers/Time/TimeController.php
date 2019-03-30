<?php declare(strict_types = 1);

namespace App\Http\Controllers\Time;

use App\Http\Controllers\Controller;
use App\Http\Requests\Time\CreateTimeRequest;
use App\Http\Requests\Time\UpdateTimeRequest;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class TimeController extends Controller
{

    public const GET_PER_PAGE = 15;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkPoint
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(?User $user, Run $run, CheckPoint $checkPoint): JsonResponse
    {
        return $this->ok($checkPoint->times()->paginate(self::GET_PER_PAGE));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Time\CreateTimeRequest $request
     *
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkPoint
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateTimeRequest $request, Run $run, CheckPoint $checkPoint): JsonResponse
    {
        $data = $request->validated();
        $data['run_id'] = $run->id;
        $data['check_point_id'] = $checkPoint->id;
        $time = Time::create($data);

        return $this->created($time);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Time\UpdateTimeRequest $request
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkPoint
     * @param \App\Models\Time $time
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTimeRequest $request, Run $run, CheckPoint $checkPoint, Time $time): JsonResponse
    {
        $time->update($request->validated());

        return $this->ok($time);
    }

    public function show(?User $user, Run $run, CheckPoint $checkPoint, Time $time): JsonResponse
    {
        $time = Time::findOrFail($time->id);

        return $this->ok($time);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Run $run
     * @param \App\Models\CheckPoint $checkPoint
     * @param \App\Models\Time $time
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run, CheckPoint $checkPoint, Time $time): JsonResponse
    {
        $time->delete();

        return $this->noContent();
    }
}
