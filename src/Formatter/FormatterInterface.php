<?php

namespace Borsch\Formatter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

interface FormatterInterface
{

    /**
     * Returns a formatted response.
     *
     * @param ResponseInterface $response
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    public function format(ResponseInterface $response, Throwable $throwable, RequestInterface $request): ResponseInterface;
}
