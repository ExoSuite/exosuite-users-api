<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfileRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param UserProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserProfileRequest $request)
    {
        UserProfile::create($request->validated());

        return $this->created();
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        return $this->ok(UserProfile::whereId(Auth::id())->first());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserProfileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserProfileRequest $request)
    {
        $data = array_except($request->validated(), ['id']);
        UserProfile::whereId(Auth::id())->update($data);

        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }
}
