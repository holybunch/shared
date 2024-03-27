<?php

namespace holybunch\shared\exceptions;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use InvalidArgumentException;

/**
 * Exception class used for handling shared exceptions in the application.
 *
 * @author holybunch
 */
class SharedException extends Exception
{
    /**
     * Constructs a new SharedException object with the provided previous exception.
     *
     * @param Exception $previous The previous exception.
     */
    public function __construct(
        Exception $previous
    ) {
        parent::__construct(
            $previous::class . ": " . $previous->getMessage(),
            ($previous->getCode() == 0) ? $this->code($previous) : $previous->getCode(),
            $previous
        );
    }

    /**
     * Generates appropriate HTTP status code based on the provided exception.
     *
     * @param Exception $e The exception.
     * @return int The HTTP status code.
     */
    private function code(Exception $e): int
    {
        switch ($e::class) {
            case InvalidArgumentException::class:
                return StatusCodeInterface::STATUS_BAD_REQUEST;
            default:
                return StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;
        }
    }
}
