<?php

namespace App\Http\Requests\Timeouts\Customers;

use Illuminate\Foundation\Http\FormRequest;

class StoreTimeoutRequest extends FormRequest
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
            'start' => 'required|date',
            'end'   => 'required|date',
        ];
    }
}
