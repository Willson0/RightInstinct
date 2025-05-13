<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostStoreRequest extends FormRequest
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
            "title" => "required|string",
            "age" => "required|integer|between:1,360",
            "gender" => "required|boolean",
            "breed_id" => "required|integer|exists:breeds,id",
            "city_id" => "required|integer|exists:cities,id",
            "price" => "required|integer|min:0",
            "category_id" => "required|integer|exists:categories,id",
            "description" => "required|string",
            "rewards" => "required|string",
            "pictures" => "required|array|min:1",
            "pictures.*" => "required|file",
            "link" => "string",
        ];
    }
}
