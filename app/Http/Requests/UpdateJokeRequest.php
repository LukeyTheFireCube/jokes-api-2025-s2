<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJokeRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:4', 'max:128'],
            'content' => ['nullable', 'string', 'max:5000'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', Rule::exists('categories', 'id')],
        ];
    }

    public function messages(): array
    {
        return [
            'categories.required' => 'Please select at least one category.',
            'categories.*.exists' => 'One or more selected categories are invalid.',
        ];
    }
}
