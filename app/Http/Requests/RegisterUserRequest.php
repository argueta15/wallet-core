<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     title="RegisterUserRequest",
 *     description="Register User Request",
 *     required={"name", "email", "password"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="John Doe",
 *         description="Name of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         example="test@test.com",
 *        description="Email of the user"
 *    ),
 *    @OA\Property(
 *       property="password",
 *      type="string",
 *     example="password",
 *   description="Password of the user"
 * )
 * )
 */
class RegisterUserRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name is required.',
            'email.required' => 'The email is required.',
            'email.email' => 'Please enter a valid email address.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 8 characters long.',
        ];
    }
}
