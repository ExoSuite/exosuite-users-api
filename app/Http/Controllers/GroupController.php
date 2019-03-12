<?php declare(strict_types = 1);

namespace App\Http\Controllers;

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

    /** @var array<string<self, string>> */
    public $updateFunctions;

    public function __construct()
    {
        $this->updateFunctions = [
            'delete_user' => [$this, 'deleteUser'],
            'add_user' => [$this, 'addUser'],
            'add_user_as_admin' => [$this, 'addUserAsAdmin'],
            'update_user_rights' => [$this, 'updateUserRights'],
            'update_group_name' => [$this, 'updateGroupName'],
        ];
    }

    /**
     * @param array<string> $data
     * @param \App\Models\Group $group
     * @return \App\Models\Group
     */
    public function deleteUser(array $data, Group $group): Group
    {
        $current_user = Auth::user();
        $user_id = $data['user_id'];
        /** @var string $message */
        $message = str_replace(
            [':group_name', ':user_name'],
            [$group->name, "{$current_user->first_name} {$current_user->last_name}"],
            trans('notification.expelled_from_group')
        );

        $users = static::collectionFilterWithExcept(
            $group->users()->get(),
            'id',
            $user_id,
            true
        );

        Notification::send($users, new ExpelledFromGroupNotification($message, $group));
        $group->groupMembers()->whereUserId($user_id)->delete();

        return $group;
    }

    /**
     * @param array<string> $data
     * @param \App\Models\Group $group
     * @return \App\Models\Group
     */
    public function addUser(array $data, Group $group): Group
    {
        $user_id = $data['user_id'];
        $group->groupMembers()->create(['user_id' => $user_id]);

        return $group;
    }

    /**
     * @param array<string> $data
     * @param \App\Models\Group $group
     * @return \App\Models\Group
     */
    public function addUserAsAdmin(array $data, Group $group): Group
    {
        $user_id = $data['user_id'];
        $group->groupMembers()->create(['user_id' => $user_id, 'is_admin' => true]);

        return $group;
    }

    /**
     * @param array<string> $data
     * @param \App\Models\Group $group
     * @return \App\Models\Group
     */
    public function updateUserRights(array $data, Group $group): Group
    {
        $user_id = $data['user_id'];
        $is_admin = $data['is_admin'];
        $group->groupMembers()->whereUserId($user_id)->update(['is_admin' => $is_admin]);

        return $group;
    }

    /**
     * @param array<string> $data
     * @param \App\Models\Group $group
     * @return \App\Models\Group
     */
    public function updateGroupName(array $data, Group $group): Group
    {
        $name = $data['name'];
        $group->name = $name;
        $group->save();

        return $group;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Group\CreateGroupRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateGroupRequest $request): JsonResponse
    {
        if ($request->exists('name')) {
            $name = $request->get('name');
        } else {
            /** @var \Faker\Generator $faker */
            $faker = Faker::create(app()->getLocale());
            $name = $faker->city;
        }

        $group = Group::create(['name' => $name]);
        $members = collect();
        $current_user = Auth::user();
        $members->push(new GroupMember(['user_id' => $current_user->id, 'is_admin' => true]));

        foreach ($request->get('users') as $user_id) {
            $members->push(new GroupMember(['user_id' => $user_id]));
        }

        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');
        /** @var string $message */
        $message = str_replace(
            [':group_name', ':user_name'],
            [$group->name, "{$current_user->first_name} {$current_user->last_name}"],
            trans('notification.new_group')
        );
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
        $request->validated();
        $requestType = $request->get('request_type');
        $group->load('groupMembers');

        return $this->ok(call_user_func($this->updateFunctions[$requestType], $request->validated(), $group));
    }

    public function show(Group $group): JsonResponse
    {
        return $this->ok($group->load('groupMembers'));
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
        /** @var string $message */
        $message = str_replace(
            [':group_name', ':user_name'],
            [$group->name, "{$current_user->first_name} {$current_user->last_name}"],
            trans('notification.deleted_group')
        );
        $users = $group->users()->get()->filter(static function ($user) use ($current_user) {
            return $user->id !== $current_user->id;
        });

        Notification::send($users, new DeletedGroupNotification($message, $group));
        $group->delete();

        return $this->noContent();
    }
}
