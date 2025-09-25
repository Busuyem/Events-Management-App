<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'organizer' || $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'type'     => 'required|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
