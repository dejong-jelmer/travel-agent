<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class CampaignAlreadySentException extends Exception
{
    public function __construct(string $message = "Newsletter campaign is already send or queued.", int $code = 0, Throwable|null $previous = null)
    {
        return parent::__construct($message, $code, $previous);
    }
}
