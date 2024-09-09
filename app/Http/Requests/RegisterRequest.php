<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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

    public function messages()
    {
        return [
            'password.regex' => __('The password must contain at least one lowercase letter, one uppercase letter, one digit and one special character (@$!%*#?&).'),
            'fqdn.unique'    => __('The domain name has already been taken'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => [
                'required', 'confirmed',
                'string',
                'min:8',              // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
            'company'    => 'required|string|max:255',
            'fqdn'       => 'required|string|max:255|unique:teams,fqdn',
            'timezone'   => 'required|string|max:255',
            'logo'       => 'file',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['fqdn' => preg_replace('/[^A-Za-z0-9\-]/', '', $this->get('fqdn')).'.'.config('app.domain')]);
    }
}
