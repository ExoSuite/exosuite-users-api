<?php declare(strict_types=1);

namespace App\Http\Requests\CheckPoint;

use App\Http\Requests\Abstracts\RouteParamRequestUuidToId;
use App\Models\CheckPoint;
use Illuminate\Support\Facades\Auth;

class DeleteCheckPointRequest extends RouteParamRequestUuidToId
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
        return [];
    }
}
