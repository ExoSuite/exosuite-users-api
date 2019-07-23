<?php declare(strict_types = 1);

namespace App\Http\Requests\Time;

use App\Rules\TimeRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateTimeRequest
 *
 * @property \App\Models\Run $run
 * @property \App\Models\CheckPoint $checkpoint
 * @package App\Http\Requests\Time
 */
class CreateTimeRequest extends FormRequest
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
            'current_time' => ['required', 'integer', new TimeRule($this->run, $this->checkpoint)],
            'user_run_id' => 'required|uuid|exists:user_runs,id',
        ];
    }
}
