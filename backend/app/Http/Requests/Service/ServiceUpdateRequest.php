<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class ServiceUpdateRequest extends FormRequest
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
            "description" => "string",
            "city_id" => "integer|exists:cities,id",
            "type_id" => "integer|exists:service_types,id",
            "price" => "integer|min:0",
            "pictures" => "array|min:1",
            "pictures.*" => "file",
            "delete_pictures" => "array",
            "delete_pictures.*" => "integer|exists:pictures,id",
            "number_main_picture" => "integer|min:0",
        ];
    }
}
