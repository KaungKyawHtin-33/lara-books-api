<?php

namespace App\Http\Requests\Publisher;

use Illuminate\Foundation\Http\FormRequest;

class StorePublisherRequest extends FormRequest
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
            'name'          => 'required|string|max:255|unique:publishers,name',
            'address'       => 'required|string',
            'website'       => 'string',
            'email'         => 'required|email',
            'created_by'    => 'required|exists:users,id',
            'updated_by'    => 'required|exists:users,id'
        ];
    }
}