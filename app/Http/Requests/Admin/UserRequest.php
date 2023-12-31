<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'photo.*'  => 'image|mimes:jpg,png,jpeg',
            'multiple_addresses.*.address' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Name is required",
            'name.max' => 'Name cannot be more than 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email must be unique',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'photo.mimes' => 'Please upload file in these formats only (jpg, jpeg, png)',
            'photo.max' => 'Image size cannot be more than 2048 bytes',
            'multiple_addresses.*.address.required' => 'Address is required',
        ];
    }
}
