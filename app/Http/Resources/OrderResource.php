<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'status' => $this->getStatus(),
            'quantity' => number_format($this->quantity, 2),
            'profit' => number_format($this->profit, 2),
            'type' => $this->type,
        ]);
    }
}
