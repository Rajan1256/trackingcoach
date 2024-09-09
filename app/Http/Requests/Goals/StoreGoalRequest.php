<?php

namespace App\Http\Requests\Goals;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
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
            'name.en' => 'required_without_all:name.nl,name.es',
            'name.nl' => 'required_without_all:name.en,name.es',
            'name.es' => 'required_without_all:name.en,name.nl',
        ];
    }
}
