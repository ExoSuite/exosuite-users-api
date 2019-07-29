<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 07/11/2018
 * Time: 18:20
 */

namespace App\Pivots;

/**
 * Class RoleUser
 *
 * @package App\Pivots
 * @property string $id
 * @property string $user_id
 * @property int $role_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pivots\RoleUser whereUserId($value)
 * @mixin \Eloquent
 */
class RoleUser extends UuidPivot
{

}
