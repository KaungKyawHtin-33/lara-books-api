<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
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
            'title'         => 'required|string|max:255|unique:books,title,' . $this->book->id,
            'description'   => 'required|string',
            'price'         => 'required|min:1|integer',
            'stock'         => 'required|min:0|integer',
            'image_path'    => 'file',
            'language'      => 'required|string|max:255',
            'publisher_id'  => 'required|exists:publishers,id',
            'genre_id'      => 'required|exists:genres,id',
            'created_by'    => 'required|exists:users,id',
            'updated_by'    => 'required|exists:users,id',
            'authors'       => 'required|array',
            'authors.*'     => 'integer|exists:authors,id',
            'categories'    => 'required|array',
            'categories.*'  => 'integer|exists:categories,id'
        ];
    }
}
