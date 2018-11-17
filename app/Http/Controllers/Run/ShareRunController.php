<?php

namespace App\Http\Controllers\Run;

use App\Http\Requests\Run\CreateShareRunRequest;
use App\Http\Resources\SharedRunCollection;
use App\Models\Run;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ShareRunController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->ok(
            new SharedRunCollection(
                Auth::user()->sharedRuns()->get()
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateShareRunRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateShareRunRequest $request)
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // url /run/share/{run}/{share} DELETE
    }
}
