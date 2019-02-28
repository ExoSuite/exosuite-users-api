<?php declare(strict_types = 1);

namespace App\Http\Controllers\Run;

use App\Http\Controllers\Controller;
use App\Http\Requests\Run\CreateShareRunRequest;
use App\Http\Requests\Run\GetShareRunRequest;
use App\Http\Resources\SharedRunCollection;
use App\Http\Resources\SharedRunResource;
use App\Models\Run;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webpatser\Uuid\Uuid;

/**
 * Class ShareRunController
 *
 * @package App\Http\Controllers\Run
 */
class ShareRunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $runs = Auth::user()->sharedRuns()->get();

        return $this->ok(
            new SharedRunCollection($runs)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Run\CreateShareRunRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateShareRunRequest $request): JsonResponse
    {
        $run = Run::whereId($request->get('id'))->first();
        $data = [];

        if ($request->has('user_id')) {
            $data['user_id'] = $request->get('user_id');
        }

        $share = $run->share()->create($data);

        return $this->created($share);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Http\Requests\Run\GetShareRunRequest $request
     * @param \Webpatser\Uuid\Uuid $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(GetShareRunRequest $request, Uuid $id): JsonResponse
    {
        return $this->ok(
            SharedRunResource::make(
                Auth::user()->sharedRuns()->whereId($id)
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return void
     */
    public function update(Request $request, int $id): void
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy(int $id): void
    {
        // url /run/share/{run}/{share} DELETE
    }
}
