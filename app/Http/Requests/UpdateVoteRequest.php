<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', 'in:1,-1'],
        ];
    }

    public function messages(): array
    {
        return [
            'value.required' => 'Vote value is required.',
            'value.in' => 'Vote must be either 1 (like) or -1 (dislike).',
        ];
    }
}
