<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlayerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'nickname' => 'nullable|string|max:255|unique:players,nickname',
            'rating' => 'required|numeric|min:0|max:5',
            'avatar' => 'nullable|string', // base64 encoded image
            'mime_type' => 'required_with:avatar|string|in:image/jpeg,image/jpg,image/png,image/gif,image/webp',
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
            'name.required' => 'Player name is required',
            'surname.required' => 'Player surname is required',
            'rating.required' => 'Player rating is required',
            'rating.min' => 'Rating must be at least 0',
            'rating.max' => 'Rating cannot exceed 5',
            'nickname.unique' => 'This nickname is already taken',
            'mime_type.required_with' => 'Image type is required when avatar is provided',
            'mime_type.in' => 'Invalid image type. Supported formats: JPEG, PNG, GIF, WebP',
        ];
    }
}
