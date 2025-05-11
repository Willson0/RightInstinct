<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
            "start_date" => "required|date",
            "end_date" => "required|date",
            "type_id" => "required|integer|exists:service_types,id",
            "details" => "required|string",
            "pictures" => "required|array|min:1",
            "pictures.*" => "required|file"
        ];
    }
}
