<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class GroupRequestType extends Enum
{
    const DELETE_USER = "delete_user";
    const ADD_USER = "add_user";
    const ADD_USER_AS_ADMIN = "add_user_as_admin";
    const UPDATE_USER_RIGHTS = "update_user_rights";
}
