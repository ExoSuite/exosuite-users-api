<?php declare(strict_types = 1);

namespace App\Http\Requests\Time;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        /*$time = Time::whereId($this->id());

        return $time->firstOrFail()->creator_id === Auth::id();*/
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
            'interval' => 'required|unsignedTinyInteger|max:255',
        ];
    }
}
