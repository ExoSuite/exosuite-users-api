<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckPoint\GetCheckPointRequest;
use App\Models\CheckPoint;
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
