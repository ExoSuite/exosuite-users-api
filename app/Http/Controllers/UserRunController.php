<?php declare(strict_types = 1);

namespace App\Http\Controllers;

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
     * @param \App\Models\Run $run
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Run $run): JsonResponse
    {
        return $this->created(
            UserRun::create([
                "final_time" => 0,
                "user_id" => Auth::user()->id,
                "run_id" => $run->id,
            ])
        );
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
     * @param \App\Models\Run $run
     * @param \App\Models\UserRun $userRun
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Run $run, UserRun $userRun): JsonResponse
    {
        $curr_times = $userRun->times()->get();
        $last_time = $curr_times->last();
        $last_time_timestamp = $last_time->current_time;
        $first_time = $curr_times->first();
        $first_time_timestamp = $first_time->current_time;
        $total_time = $last_time_timestamp - $first_time_timestamp;
        $userRun->update([
            "final_time" => $total_time,
        ]);

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
