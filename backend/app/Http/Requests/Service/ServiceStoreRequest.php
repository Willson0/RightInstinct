<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class ServiceStoreRequest extends FormRequest
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
            "description" => "required|string",
            "city_id" => "required|integer|exists:cities,id",
            "type_id" => "required|integer|exists:service_types,id",
            "price" => "required|integer|min:-1",
            "pictures" => "required|array|min:1",
            "pictures.*" => "required|file",
            "link" => "string",
        ];
    }
}
