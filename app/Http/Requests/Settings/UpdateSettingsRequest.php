<?php

namespace App\Http\Requests\Settings;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
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
            'first_name'                                     => 'required',
            'last_name'                                      => 'required',
            'email'                                          => 'required|unique:users,email,'.Auth::user()->id,
            'phone'                                          => 'required',
            'locale'                                         => 'required',
            'date_format'                                    => 'required',
            'timezone'                                       => 'required',
            'preferred_notification_methods.daily_invites'   => 'required',
            'preferred_notification_methods.weekly_reports'  => 'required',
            'preferred_notification_methods.monthly_reports' => 'required',
            'password'                                       => [
                'nullable',
                'confirmed',
                'string',
                'min:8',              // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                'regex:/[@$!%*#?&]/', // must contain a special character
            ],
        ];
    }
}
