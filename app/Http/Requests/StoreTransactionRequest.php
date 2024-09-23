<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *    schema="StoreTransactionRequest",
 *   type="object",
 *  required={"amount", "description", "type"},
 * @OA\Property(property="amount", type="number"),
 * @OA\Property(property="description", type="string"),
 * @OA\Property(property="type", type="string")
 * )
 */
class StoreTransactionRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'description.required' => 'The description field is required.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'type.required' => 'The type field is required.',
            'type.in' => 'The type must be either income or expense.',
        ];
    }
}
