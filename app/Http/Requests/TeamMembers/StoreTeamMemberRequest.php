<?php

namespace App\Http\Requests\TeamMembers;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamMemberRequest extends FormRequest
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
            'first_name'                                     => 'required',
            'last_name'                                      => 'required',
            'email'                                          => 'required|email',
            'phone'                                          => 'required',
            'locale'                                         => 'required',
            'date_format'                                    => 'required',
            'timezone'                                       => 'required',
            'preferred_notification_methods.daily_invites'   => 'required',
            'preferred_notification_methods.weekly_reports'  => 'required',
            'preferred_notification_methods.monthly_reports' => 'required',
            'roles'                                          => 'required',
        ];
    }
}
