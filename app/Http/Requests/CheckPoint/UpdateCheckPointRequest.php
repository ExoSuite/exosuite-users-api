<?php declare(strict_types = 1);

namespace App\Http\Requests\CheckPoint;

use App\Rules\CheckPointUpdateTypeRule;
use App\Rules\PolygonRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCheckPointRequest
 *
 * @property \App\Models\Run $run
 * @property \App\Models\CheckPoint $checkpoint
 * @package App\Http\Requests\CheckPoint
 */
class UpdateCheckPointRequest extends FormRequest
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
            'type' => ['required', 'string', new CheckPointUpdateTypeRule($this->run, $this->checkpoint)],
            'location' => ['required', new PolygonRule],
        ];
    }
}
