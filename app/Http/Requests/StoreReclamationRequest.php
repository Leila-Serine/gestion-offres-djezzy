<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReclamationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'objet' => [
                'required',
                'string',
                'max:150',
            ],

            'message' => [
                'required',
                'string',
                'max:5000',
            ],
        ];
    }
}