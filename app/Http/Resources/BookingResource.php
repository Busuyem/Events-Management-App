<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'id'       => $this->id,
                'status'   => $this->status,
                'quantity' => $this->quantity,
                'user'     => $this->user?->name,
                'ticket'   => [
                    'id'    => $this->ticket?->id,
                    'type'  => $this->ticket?->type,
                    'price' => $this->ticket?->price,
                    'event' => $this->ticket?->event?->title,
                ],
                'payment'  => $this->payment ? [
                    'id'     => $this->payment->id,
                    'amount' => $this->payment->amount,
                    'status' => $this->payment->status,
                ] : null,
        ];
    }
}
