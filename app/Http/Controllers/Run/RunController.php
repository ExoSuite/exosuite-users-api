<?php

namespace App\Http\Controllers\Run;

use App\Http\Requests\Run\CreateRunRequest;
use App\Http\Requests\Run\GetRunRequest;
use App\Http\Requests\Run\UpdateRunRequest;
use App\Models\Run;
use Webpatser\Uuid\Uuid;
use App\Http\Controllers\Controller;

/**
 * Class RunController
 * @package App\Http\Controllers\Run
 */
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
        $run = Run::create($data);

        return $this->created($run);
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
     * @param UpdateRunRequest $request
     * @param Uuid $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRunRequest $request, Uuid $id)
    {
        Run::whereId($id)->update($request->validated());
        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        return $this->noContent();
    }
}
