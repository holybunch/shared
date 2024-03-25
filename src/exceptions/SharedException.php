<?php

namespace holybunch\shared\exceptions;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;

class SharedException extends Exception
{
    public function __construct(
        Exception $previous = null
    ) {
        parent::__construct(
            $previous::class . ": " . $previous->getMessage(),
            ($previous->getCode() == 0) ? $this->code($previous) : $previous->getCode(),
            $previous
        );
    }

    private function code(Exception $e) {
        switch ($e::class) {
            case InvalidArgumentException::class:
                return StatusCodeInterface::STATUS_BAD_REQUEST;
            default:
                return StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        }
    }
}
