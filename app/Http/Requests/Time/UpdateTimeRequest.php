<?php declare(strict_types = 1);

namespace App\Http\Requests\Time;

use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;

class UpdateTimeRequest extends RouteParamRequestUuidToId
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
            'run_id' => 'required|uuid|exists:run,id',
            'interval' => 'required|unsignedTinyInteger|max:255',
            'checkpoint_id' => 'required|uuid|exist:check_points,id',
        ];
    }
}
