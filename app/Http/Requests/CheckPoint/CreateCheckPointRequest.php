<?php declare(strict_types=1);

namespace App\Http\Requests\CheckPoint;

use App\Rules\CheckPointTypeRule;
use App\Rules\PolygonRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateCheckPointRequest
 *
 * @package App\Http\Requests\CheckPoint
 */
class CreateCheckPointRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', new CheckPointTypeRule],
            'location' => ['required', new PolygonRule],
        ];
    }
}
