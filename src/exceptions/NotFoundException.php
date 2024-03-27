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
class NotFoundException extends Exception
{
    /**
     * NotFoundException constructor.
     *
     * @param string $message The error message.
     * @param int $code The HTTP status code for not found (default: 404).
     * @param Throwable|null $previous The previous exception, if available (default: null).
     */
    public function __construct(
        $message = "",
        $code = StatusCodeInterface::STATUS_NOT_FOUND,
        Throwable $previous = null
    ) {
        parent::__construct("Not Found: " . $message, $code, $previous);
    }
}
