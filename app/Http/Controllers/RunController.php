<?php

namespace App\Http\Controllers;

use App\Http\Requests\Run\CreateRunRequest;
use App\Http\Requests\Run\GetRunRequest;
use App\Models\Run;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

class RunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->ok(Run::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRunRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRunRequest $request)
    {
        $data = $request->validated();
        $data = Run::create($data);

        return $this->created($data);
    }

    /**
     * Display the specified resource.
     *
     * @param GetRunRequest $request
     * @param Uuid $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(GetRunRequest $request, Uuid $id)
    {
        $run = Run::findOrFail($id);
        return $this->ok($run);
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
