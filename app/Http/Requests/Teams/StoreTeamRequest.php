<?php

namespace App\Http\Requests\Teams;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
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
            'company'                 => 'required',
            'name'                    => 'required',
            'settings.reply_to_email' => 'required',
            'settings.signature_line' => 'nullable',
            'settings.color'          => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'fqdn'                    => 'required|unique:teams,fqdn',
            'timezone'                => 'required',
            'plan'                    => 'required',
            'logo'                    => 'nullable',
            'first_name'              => 'required',
            'last_name'               => 'required',
            'email'                   => 'required|email',
            'unlimited_members'       => 'nullable',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge(['fqdn' => $this->get('fqdn').'.'.config('app.domain')]);
    }
}
