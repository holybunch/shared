<?php

namespace exceptions;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Throwable;

class GoogleAPIException extends Exception
{
    public function __construct(
        Throwable $previous = null
    ) {
        parent::__construct(
            "google API: " . $previous->getMessage(),
            ($previous->getCode() == 0) ? StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR : $previous->getCode(),
            $previous
        );
    }
}
