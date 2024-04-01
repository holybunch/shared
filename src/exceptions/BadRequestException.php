<?php

namespace holybunch\shared\exceptions;

use Exception;
use Fig\Http\Message\StatusCodeInterface;
use Throwable;

/**
 * Exception thrown when a requested resource is not found.
 *
 * @author holybunch
 */
class BadRequestException extends Exception
{
    /**
     * BadRequestException constructor.
     *
     * @param string $message The error message.
     * @param int $code The HTTP status code for not found (default: 404).
     * @param Throwable|null $previous The previous exception, if available (default: null).
     */
    public function __construct(
        $message = "",
        $code = StatusCodeInterface::STATUS_BAD_REQUEST,
        Throwable $previous = null
    ) {
        parent::__construct("Bad Request: " . $message, $code, $previous);
    }
}
