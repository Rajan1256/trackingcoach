<?php

namespace App\Http\Requests\Questions;

use App\Interfaces\QuestionInterface;
use Illuminate\Foundation\Http\FormRequest;

use function class_exists;

class StoreQuestionRequest extends FormRequest
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

    public function messages()
    {
        $type = $this->request->get('type');

        if (class_exists($type)) {
            /** @var QuestionInterface $type */
            $rules = $type::storeRules();
            $messages = [];

            foreach (array_keys($rules) as $rule) {
                $messages[$rule.'.required'] = __('validation.required',
                    ['attribute' => __('trackingcoach.questions.'.$rule)]);
            }

            return $messages;
        }

        return [
            'name' => 'name',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $type = $this->request->get('type');

        if (class_exists($type)) {
            /** @var QuestionInterface $type */
            return $type::storeRules();
        }

        return [
            'name' => 'required',
        ];
    }
}
