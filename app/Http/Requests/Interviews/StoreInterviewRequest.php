<?php

namespace App\Http\Requests\Interviews;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewRequest extends FormRequest
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
            'date'     => 'required',
            'continue' => ['array'],
            'start'    => ['array'],
            'stop'     => ['array'],
            'best'     => ['array'],
            'worst'    => ['array'],
        ];
    }
}
