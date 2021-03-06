<?php declare(strict_types = 1);

namespace App\Http\Requests\Dashboard;

use App\Rules\RestrictionsTypeRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class ChangeRestrictionRequest
 *
 * @package App\Http\Requests\Dashboard
 */
class ChangeRestrictionRequest extends FormRequest
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
        return [
            'restriction_field' => "required|string|in:visibility,writing_restriction",
            'restriction_level' => ['required', new RestrictionsTypeRule],
        ];
    }
}
