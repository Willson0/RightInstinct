<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AuthStoreRequest extends FormRequest
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
            "notification" => "boolean",
            "city_id" => "integer|exists:cities,id",
            "fullname" => [
                "nullable",
                "string",
                "regex:/^[A-ZА-ЯЁ][a-zа-яё]+(?: [A-ZА-ЯЁ][a-zа-яё]+){0,2}$/u",
            ],
            "phone" => [
                "nullable",
                "string",
                "regex:/^(\+7|8)[\s-]?\(?\d{3}\)?[\s-]?\d{3}[\s-]?\d{2}[\s-]?\d{2}$/u",
            ],
            "email" => [
                "nullable",
                "string",
                "regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/u"
            ]
        ];
    }
}
