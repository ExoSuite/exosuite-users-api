<?php declare(strict_types=1);

namespace App\Http\Requests\CheckPoint;

use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use App\Models\CheckPoint;
use App\Rules\CheckPointTypeRule;
use Illuminate\Support\Facades\Auth;

class UpdateCheckPointRequest extends RouteParamRequestUuidToId
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $checkpoint = CheckPoint::whereId($this->id());

        return $checkpoint->firstOrFail()->creator_id === Auth::id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', new CheckPointTypeRule],
            'location' => 'required|polygon|max:255',
            'run_id' => 'required|uuid|exists:run,id',
        ];
    }
}
