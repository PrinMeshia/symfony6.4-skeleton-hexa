<?php

namespace App\Application\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(description: "Error payload")]
class Error
{
    #[OA\Property(description: "Human readable error message")]
    public string $message;

    #[OA\Property(description: "Error code", example: "invalid_request")]
    public ?string $code = null;

    public function __construct(string $message, ?string $code = null)
    {
        $this->message = $message;
        $this->code = $code;
    }
}
