<?php declare(strict_types = 1);

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use App\Http\Requests\Run\CreateRunRequest;
use App\Http\Requests\Run\DeleteRunRequest;
use App\Http\Requests\Run\GetRunRequest;
use App\Http\Requests\Run\UpdateRunRequest;
use App\Models\Run;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

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
    public function index(): JsonResponse
    {
        return $this->ok(Auth::user()->runs()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Run\CreateRunRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRunRequest $request): JsonResponse
    {
        $data = $request->validated();
        $run = Run::create($data);

        return $this->created($run);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Run\GetRunRequest $request
     * @param \Webpatser\Uuid\Uuid $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(GetRunRequest $request, Uuid $id): JsonResponse
    {
        $run = Run::findOrFail($id);

        return $this->ok($run);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Run\UpdateRunRequest $request
     * @param \Webpatser\Uuid\Uuid $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRunRequest $request, Uuid $id): JsonResponse
    {
        Run::whereId($id)->update($request->validated());

        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Http\Requests\Run\DeleteRunRequest $request
     * @param \Webpatser\Uuid\Uuid $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(DeleteRunRequest $request, Uuid $id): JsonResponse
    {
        Run::whereId($id)->delete();

        return $this->noContent();
    }
}
