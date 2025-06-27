<?php

namespace App\Purchase\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class InvalidPurchaseException extends HttpException
{
    public function __construct(string $message = "Bad request", Throwable $previous = null)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous);
    }
}