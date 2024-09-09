<?php

namespace App\Http\Requests\Supporters;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupporterRequest extends FormRequest
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
            'first_name'          => 'required',
            'last_name'           => 'required',
            'relationship'        => 'required',
            'locale'              => 'required',
            'notification_method' => 'required',
            'email'               => 'required_if:notification_method,mail,both',
            'phone'               => 'required_if:notification_method,sms,both',
        ];
    }
}
