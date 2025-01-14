<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;

class EventImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'image'     => 'storage/upload/' . $this->imagePath() . '/' . $this->image,
        ];
    }
}
