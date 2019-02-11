<?php

namespace App\Http\Controllers\CheckPoint;

use App\Http\Controllers\Controller;
use App\Models\CheckPoint;
use App\Http\Requests\CheckPoint\CreateCheckPointRequest;
use App\Http\Requests\CheckPoint\UpdateCheckPointRequest;
use App\Models\Run;

/**
 * Class CheckPointController
 * @package App\Http\Controllers\CheckPoint
 */
class CheckPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateCheckPointRequest $request
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Run $run, CreateCheckPointRequest $request)
    {
        $data = $request->validated();
        //$data['location'] = "test";
        //dd($run->id);
        $data['run_id'] = $run->id;
        $checkpoint = CheckPoint::create($data);

        return $this->created($checkpoint);
    }

    /**
     * Display the specified resource.
     *
     * @param Run $run
     * @param CheckPoint $checkPointParam
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Run $run, CheckPoint $checkPointParam)
    {
        $checkpoint = CheckPoint::findOrFail($checkPointParam->id);
        return $this->ok($checkpoint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCheckPointRequest $request
     * @param CheckPoint $checkpoint
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCheckPointRequest $request, CheckPoint $checkpoint)
    {
        CheckPoint::whereId($checkpoint->id)->update($request->validated());
        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Run $run
     * @param CheckPoint $checkpoint
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run, CheckPoint $checkpoint)
    {
        CheckPoint::whereId($checkpoint->id)->delete();
        return $this->noContent();
    }
}
