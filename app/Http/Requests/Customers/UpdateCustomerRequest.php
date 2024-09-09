<?php

namespace App\Http\Requests\Customers;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'days_per_week'           => 'required',
            'filled_auto_invite_time' => 'required|date_format:"H:i',
        ];
    }

    public function messages()
    {
        return [
            'filled_auto_invite_time.date_format' => __('The auto invite time needs hours and minutes in the following format: hh:mm'),
        ];
    }
}
