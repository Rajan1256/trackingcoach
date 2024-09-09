<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class AcceptInviteRequest extends FormRequest
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
            'phone'                                          => 'required',
            'locale'                                         => 'required',
            'date_format'                                    => 'required',
            'timezone'                                       => 'required',
            'preferred_notification_methods.daily_invites'   => 'required',
            'preferred_notification_methods.weekly_reports'  => 'required',
            'preferred_notification_methods.monthly_reports' => 'required',
            'password'                                       => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
