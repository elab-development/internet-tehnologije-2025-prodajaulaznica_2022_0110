<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketTypeUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'in:standard,premium,vip'],
            'price' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'capacity' => ['required', 'integer'],
        ];
    }
}
