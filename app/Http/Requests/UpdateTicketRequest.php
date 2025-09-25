<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'organizer' || $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'type'     => 'sometimes|string|max:50',
            'price'    => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:1',
        ];
    }
}
