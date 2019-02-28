<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Enums\GroupRequestType;
use App\Http\Requests\Group\CreateGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Models\Group;
use App\Models\GroupMember;
use App\Notifications\DeletedGroupNotification;
use App\Notifications\ExpelledFromGroupNotification;
use App\Notifications\NewGroupNotification;
use Faker\Factory as Faker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use function app;
use function array_push;
use function collect;
use function str_replace;
use function trans;

/**
 * Class GroupController
 *
 * @package \App\Http\Controllers
 */
class GroupController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Group\CreateGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateGroupRequest $request): JsonResponse
    {
        if ($request->exists("name")) {
            $name = $request->get("name");
        } else {
            /** @var \Faker\Generator $faker */
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
        $users = $users->filter(static function ($user) use ($current_user) {
            return $user->id !== $current_user->id;
        });
        Notification::send($users, new NewGroupNotification($message, $group));

        return $this->created($group);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Group\UpdateGroupRequest $request
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGroupRequest $request, Group $group): JsonResponse
    {
        $current_user = Auth::user();
        $user_id = $request->get("user_id");
        $requestType = $request->get("request_type");

        if ($requestType === GroupRequestType::ADD_USER) {
            $group->groupMembers()->create(["user_id" => $user_id]);
        } elseif (GroupRequestType::ADD_USER_AS_ADMIN === $requestType) {
            $group->groupMembers()->create(["user_id" => $user_id, "is_admin" => true]);
        } elseif (GroupRequestType::UPDATE_USER_RIGHTS === $requestType) {
            $group->groupMembers()->whereUserId($user_id)->update(["is_admin" => $request->get("is_admin")]);
        } elseif (GroupRequestType::DELETE_USER === $requestType) {
            $message = str_replace([":group_name", ":user_name"], [$group->name, "{$current_user->first_name} {$current_user->last_name}"], trans("notification.expelled_from_group"));
            $users = $group->users()->get();
            $users = $users->filter(static function ($user) use ($user_id) {
                return $user->id === $user_id;
            });
            Notification::send($users, new ExpelledFromGroupNotification($message, $group->toArray()));
            $group->groupMembers()->whereUserId($request->get("user_id"))->delete();
        } elseif (GroupRequestType::UPDATE_GROUP_NAME === $requestType) {
            $group->name = $request->get('name');
            $group->save();
        }

        return $this->ok($group);
    }

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Group $group): JsonResponse
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
     * @param \App\Models\Group $group
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Group $group): JsonResponse
    {
        $current_user = Auth::user();
        $message = str_replace(
            [":group_name", ":user_name"],
            [$group->name, "{$current_user->first_name} {$current_user->last_name}"],
            trans("notification.deleted_group")
        );
        $users = $group->users()->get();
        $users = $users->filter(static function ($user) use ($current_user) {
            return $user->id !== $current_user->id;
        });

        Notification::send($users, new DeletedGroupNotification($message, $group));
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
