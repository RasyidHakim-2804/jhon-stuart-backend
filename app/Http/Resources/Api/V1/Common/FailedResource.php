<?php

namespace App\Http\Resources\Api\V1\Common;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FailedResource extends JsonResource
{
    public static $wrap = null;

    public function __construct(
        private int $code       = JsonResponse::HTTP_BAD_REQUEST,
        private ?string $message = null)
    {
        parent::__construct((object)[]);
    }


    public function withResponse(Request $request, JsonResponse $response)
    {
        return $response->setStatusCode($this->code);
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => 'fail',
            $this->mergeWhen($this->message !== null, [
                'message' => $this->message
            ]),
        ];
    }
}