<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class PostUpdateRequest extends FormRequest
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
            "title" => "string",
            "age" => "integer|between:1,360",
            "gender" => "boolean",
            "breed_id" => "integer|exists:breeds,id",
            "city_id" => "integer|exists:cities,id",
            "price" => "integer|min:-1",
            "category_id" => "integer|exists:categories,id",
            "description" => "string",
            "rewards" => "string",
            "pictures" => "array",
            "pictures_meta" => "array",
//            "pictures.*" => "file",
            "delete_pictures" => "array",
            "delete_pictures.*" => "integer|exists:pictures,id",
            "number_main_picture" => "integer|min:0",
            "link" => "string",
            "mother" => "string",
            "father" => "string",
            "nursery" => "string",
        ];
    }
}
