<?php

namespace App\Exceptions;

use JetBrains\PhpStorm\Pure;
use Throwable;

class InvalidBrainException extends \InvalidArgumentException
{
    #[Pure]
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("The brain_id parameter is invalid or not set" . $message, $code, $previous);
    }
}
