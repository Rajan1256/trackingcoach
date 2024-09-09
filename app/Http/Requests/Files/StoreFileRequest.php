<?php

namespace App\Http\Requests\Files;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
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
            'files.*' => 'file|max:'.(1024 * 128),
        ];
    }

    public function messages()
    {
        return [
            'files.*.max' => __('Files can not exceed 128MB in size.'),
        ];
    }
}
