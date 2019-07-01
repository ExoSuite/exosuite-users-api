<?php declare(strict_types = 1);

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use App\Http\Requests\Run\CreateRunRequest;
use App\Http\Requests\Run\SearchRunRequest;
use App\Http\Requests\Run\UpdateRunRequest;
use App\Models\Run;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class RunController
 *
 * @package App\Http\Controllers\Run
 */
class RunController extends Controller
{
    public const GET_PER_PAGE = 15;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User|null $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(?User $user): JsonResponse
    {
        if (!$user->id) {
            $user = Auth::user();
        }

        return $this->ok($user->runs()->latest()->paginate(self::GET_PER_PAGE));
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

    public function show(?User $user, Run $run): JsonResponse
    {
        $run = Run::findOrFail($run->id);

        return $this->ok($run);
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

        return $this->ok($run);
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

    public function search(SearchRunRequest $request): JsonResponse
    {
        $runPage = Run::search($request->text);
    }
}
