<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\JsonResource;

class EventTranslationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'language'      => $this->language,
            'translation'   => $this->translation,
        ];
    }
}
