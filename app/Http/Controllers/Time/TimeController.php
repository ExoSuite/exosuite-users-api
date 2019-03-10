<?php declare(strict_types = 1);

namespace App\Http\Controllers\Time;

use App\Http\Controllers\Controller;
use App\Http\Requests\Time\CreateTimeRequest;
use App\Http\Requests\Time\DeleteTimeRequest;
use App\Http\Requests\Time\UpdateTimeRequest;
use App\Models\CheckPoint;
use App\Models\Time;
use Illuminate\Http\JsonResponse;

class TimeController extends Controller
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
     * @param \App\Http\Requests\Time\CreateTimeRequest $request
     * @param \App\Models\CheckPoint $checkpoint
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateTimeRequest $request, CheckPoint $checkpoint): JsonResponse
    {
        $data = $request->validated();
        $time = Time::create($data);

        return $this->created($time);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Time\UpdateTimeRequest $request
     * @param \App\Models\Time $time
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTimeRequest $request, Time $time): JsonResponse
    {
        $time->update();

        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Time\DeleteTimeRequest $request
     * @param \App\Models\Time $time
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(DeleteTimeRequest $request, Time $time): JsonResponse
    {
        $time->delete();

        return $this->noContent();
    }
}
