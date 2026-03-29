<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class PSRFStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('bforms create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_id' => [
                'required',
            ], 
            'recipient' => [
                'required',
            ], 
            'activity_name' => [
                'required',
            ], 
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        session()->flash('message_error', 'Please fill up the form before saving.');

        parent::failedValidation($validator);
    }
}
