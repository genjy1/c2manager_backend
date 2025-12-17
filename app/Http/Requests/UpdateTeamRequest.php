<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255|unique:teams,name,' . $this->route('id'),
            'description' => 'nullable|string',
            'country' => 'nullable|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:10',
            'logo_url' => 'nullable|url|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Team name is required',
            'name.unique' => 'A team with this name already exists',
            'rating.numeric' => 'Rating must be a number',
            'rating.min' => 'Rating must be at least 0',
            'rating.max' => 'Rating cannot exceed 10',
            'logo_url.url' => 'Logo URL must be a valid URL',
        ];
    }
}
