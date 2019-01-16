<?php

namespace App\Http\Controllers;

use App\Enums\GroupRequestType;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\DestroyGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Notifications\DeletedGroupNotification;
use App\Notifications\ExpelledFromGroupNotification;
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
        $current_user = Auth::user();
        $members->push(new GroupMember(["user_id" => $current_user->id, "is_admin" => true]));
        foreach ($request->get("users") as $user_id) {
            $members->push(new GroupMember(["user_id" => $user_id]));
        }
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");
        $message = str_replace([":group_name", ":user_name"], [$group->name, "{$current_user->first_name} {$current_user->last_name}"], trans("notification.new_group"));
        $users = $group->users()->get();
        $users = $users->filter(function ($user) use ($current_user) {
            return $user->id !== $current_user->id;
        });
        Notification::send($users, new NewGroupNotification($message, $group->toArray()));
        return $this->created($group);
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
        $current_user = Auth::user();
        $user_id = $request->get("user_id");
        $requestType = $request->get("request_type");
        if ($requestType === GroupRequestType::ADD_USER) {
            $group->groupMembers()->create(["user_id" => $user_id]);
        } else if ($requestType === GroupRequestType::ADD_USER_AS_ADMIN) {
            $group->groupMembers()->create(["user_id" => $user_id, "is_admin" => true]);
        } else if ($requestType === GroupRequestType::UPDATE_USER_RIGHTS) {
            $group->groupMembers()->whereUserId($user_id)->update(["is_admin" => $request->get("is_admin")]);
        } else if ($requestType === GroupRequestType::DELETE_USER) {
            $message = str_replace([":group_name", ":user_name"], [$group->name, "{$current_user->first_name} {$current_user->last_name}"], trans("notification.expelled_from_group"));
            $users = $group->users()->get();
            $users = $users->filter(function ($user) use ($user_id) {
                return $user->id === $user_id;
            });
            Notification::send($users, new ExpelledFromGroupNotification($message, $group->toArray()));
            $group->groupMembers()->whereUserId($request->get("user_id"))->delete();
        } else if ($requestType === GroupRequestType::UPDATE_GROUP_NAME) {
            $group->name = $request->get('name');
            $group->save();
        }
        return $this->ok($group);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Group $group)
    {
        $data = [];
        $data['group_name'] = $group->name;
        $group_members = $group->groupMembers()->get();
        $members = [];
        foreach ($group_members as $member) {
            array_push($members, $member);
        }
        $data['group_members'] = $members;
        return $this->ok($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Group $group
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Group $group)
    {
        $current_user = Auth::user();
        $message = str_replace([":group_name", ":user_name"], [$group->name, "{$current_user->first_name} {$current_user->last_name}"], trans("notification.deleted_group"));
        $users = $group->users()->get();
        $users = $users->filter(function ($user) use ($current_user) {
            return $user->id !== $current_user->id;
        });
        //dd($users);
        Notification::send($users, new DeletedGroupNotification($message, $group->toArray()));
        //dd($users);
        $members = $group->groupMembers()->get();
        foreach ($members as $member) {
            $member->delete();
        }
        $group_messages = $group->messages()->get();
        foreach ($group_messages as $group_message) {
            $group_message->delete();
        }
        $group->delete();
        return $this->noContent();
    }
}
