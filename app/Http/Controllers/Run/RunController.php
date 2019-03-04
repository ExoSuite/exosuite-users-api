<?php declare(strict_types = 1);

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use App\Http\Requests\Run\CreateRunRequest;
use App\Http\Requests\Run\UpdateRunRequest;
use App\Models\Run;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class RunController
 *
 * @package App\Http\Controllers\Run
 */
class RunController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->ok(Auth::user()->runs()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Run\CreateRunRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRunRequest $request): JsonResponse
    {
        $data = $request->validated();
        $run = Run::create($data);

        return $this->created($run);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Run\UpdateRunRequest $request
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRunRequest $request, Run $run): JsonResponse
    {
        $run->update($request->validated());

        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run): JsonResponse
    {
        $run->delete();

        return $this->noContent();
    }
}
