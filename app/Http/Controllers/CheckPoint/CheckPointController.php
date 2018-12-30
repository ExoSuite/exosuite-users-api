<?php

namespace App\Http\Controllers;

use App\Models\CheckPoint;
use App\Http\Requests\CheckPoint\DeleteCheckPointRequest;
use App\Http\Requests\CheckPoint\GetCheckPointRequest;
use App\Http\Requests\CheckPoint\CreateCheckPointRequest;

use Illuminate\Http\Request;

class CheckPointController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateCheckPointRequest $request)
    {
        $data = $request->validated();
        $checkpoint = CheckPoint::create($data);

        return $this->created($checkpoint);
    }

    /**
     * Display the specified resource.
     *
     * @param GetCheckPointRequest $request
     * @param Uuid $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(GetCheckPointRequest $request, Uuid $id)
    {
        $checkpoint = Run::findOrFail($id);
        return $this->ok($checkpoint);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        CheckPoint::whereId($id)->update($request->validated());
        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DeleteCheckPointRequest $request
     * @param Uuid $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(DeleteCheckPointRequest $request, Uuid $id)
    {
        CheckPoint::whereId($id)->delete();
        return $this->noContent();
    }
}
