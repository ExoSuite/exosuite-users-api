<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRunRequest;
use App\Http\Requests\UpdateUserRunRequest;
use App\Models\Run;
use App\Models\User;
use App\Models\UserRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserRunController
 *
 * @package App\Http\Controllers
 */
class UserRunController extends Controller
{
    public const GET_PER_PAGE = 15;

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(?User $user, Run $run): JsonResponse
    {
        return $this->ok($run->userRuns()->latest()->paginate(self::GET_PER_PAGE));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\CreateUserRunRequest $request
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRunRequest $request, Run $run): JsonResponse
    {
        $data = $request->validated();
        $data['final_time'] = 0;
        $data['user_id'] = Auth::user()->id;
        $data['run_id'] = $run->id;

        $user_run = UserRun::create($data);

        return $this->created($user_run);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\User|null $user
     * @param \App\Models\Run $run
     * @param \App\Models\UserRun $userRun
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(?User $user, Run $run, UserRun $userRun): JsonResponse
    {
        $user_run = UserRun::findOrFail($userRun->id);
        $user_run['times'] = $userRun->times()->get();

        return $this->ok($user_run);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\UpdateUserRunRequest $request
     * @param \App\Models\Run $run
     * @param \App\Models\UserRun $userRun
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRunRequest $request, Run $run, UserRun $userRun): JsonResponse
    {
        $data = $request->validated();
        $last_time = $userRun->times()->get()->last();
        $last_time_timestamp = $last_time->current_time;
        $first_time = $userRun->times()->get()->first();
        $first_time_timestamp = $first_time->current_time;
        $total_time = $last_time_timestamp - $first_time_timestamp;
        $data['final_time'] = $total_time;
        $userRun->update($data);

        return $this->ok($userRun);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Run $run
     * @param \App\Models\UserRun $userRun
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Run $run, UserRun $userRun): JsonResponse
    {
        UserRun::whereId($userRun->id)->delete();

        return $this->noContent();
    }
}
