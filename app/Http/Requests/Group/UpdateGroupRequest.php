<?php declare(strict_types = 1);

namespace App\Http\Requests\Group;

use App\Enums\GroupRequestType;
use App\Rules\RequestTypeRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateGroupRequest
 *
 * @package App\Http\Requests\Group
 */
class UpdateGroupRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return mixed[]
     */
    public function rules(): array
    {
        $update_rights = GroupRequestType::UPDATE_USER_RIGHTS;
        $update_name = GroupRequestType::UPDATE_GROUP_NAME;

        return [
            'request_type' => ['required', new RequestTypeRule],
            'user_id' => "sometimes|uuid|exists:users,id|required_unless:request_type,{$update_name}",
            'is_admin' => "sometimes|boolean|required_if:request_type,{$update_rights}|required",
            'name' => "sometimes|string|max:100|required_if:request_type,{$update_name}",
        ];
    }
}
