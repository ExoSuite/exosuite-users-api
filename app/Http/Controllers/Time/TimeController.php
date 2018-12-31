<?php

namespace App\Http\Controllers;

use App\Http\Requests\Time\DeleteTimeRequest;
use App\Http\Requests\Time\GetTimeRequest;
use App\Http\Requests\Time\CreateTimeRequest;
use App\Http\Requests\Time\UpdateTimeRequest;
use App\Models\Time;
use Illuminate\Http\Request;


class TimeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateTimeRequest $request)
    {
        $data = $request->validated();
        $time = Time::create($data);

        return $this->created($time);
    }

    /**
     * Display the specified resource.
     *
     * @param GetTimeRequest $request
     * @param Uuid $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(GetTimeRequest $request, Uuid $id)
    {
        $time = Time::findOrFail($id);
        return $this->ok($time);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimeRequest $request, Uuid $id)
    {
        Time::whereId($id)->update($request->validated());
        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteTimeRequest $request
     * @param Uuid $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(DeleteTimeRequest $request, Uuid $id)
    {
        Time::whereId($id)->delete();
        return $this->noContent();
    }
}
