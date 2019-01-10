<?php

namespace App\Http\Controllers;

use App\Enums\GroupRequestType;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Notifications\NewGroupNotification;
use Elasticsearch\Endpoints\Update;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Notification;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateGroupRequest $request)
    {
        if ($request->exists("name"))
            $name = $request->get("name");
        else {
            /** @var Generator $faker */
            $faker = Faker::create(app()->getLocale());
            $name = $faker->city;
        }

        $group = Group::create(["name" => $name]);
        $members = collect();
        foreach ($request->get("users") as $user_id) {
            $members->push(new GroupMember(["user_id" => $user_id]));
        }
        $current_user = Auth::user();
        $members->push(new GroupMember(["user_id" => $current_user->id, "is_admin" => true]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");
        $message = str_replace([":group_name", ":user_name"], [$group->name, "{$current_user->first_name} {$current_user->last_name}"], trans("notification.new_group"));
        $users = $group->users()->get();
        $users = $users->filter(function ($user) use ($current_user) {
            return $user->id != $current_user->id;
        });
        Notification::send($users, new NewGroupNotification($message, $group->toArray()));
        return $this->created($group);
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
     * @param UpdateGroupRequest $request
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $requestType = $request->get("request_type");
        if ($requestType === GroupRequestType::ADD_USER) {
            $group->groupMembers()->create(["user_id" => $request->get("user_id")]);
        } else if ($requestType === GroupRequestType::ADD_USER_AS_ADMIN) {
            $group->groupMembers()->create(["user_id" => $request->get("user_id"), "is_admin" => true]);
        } else if ($requestType === GroupRequestType::UPDATE_USER_RIGHTS) {
            $group->groupMembers()->whereUserId($request->get("user_id"))->update(["is_admin" => $request->get("is_admin")]);
        } else if ($requestType === GroupRequestType::DELETE_USER) {
            $group->groupMembers()->whereUserId($request->get("user_id"))->delete();
        }
        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
