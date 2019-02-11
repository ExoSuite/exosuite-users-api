<?php

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use App\Http\Requests\Run\CreateRunRequest;
use App\Http\Requests\Run\DeleteRunRequest;
use App\Http\Requests\Run\GetRunRequest;
use App\Http\Requests\Run\UpdateRunRequest;
use App\Models\Run;
use Illuminate\Support\Facades\Auth;

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
        return $this->ok(Auth::user()->runs()->get());
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
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Run $run)
    {
        $run = Run::findOrFail($run->id);
        return $this->ok($run);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRunRequest $request
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRunRequest $request, Run $run)
    {
        Run::whereId($run->id)->update($request->validated());
        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Run $run
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run)
    {
        Run::whereId($run->id)->delete();
        return $this->noContent();
    }
}
