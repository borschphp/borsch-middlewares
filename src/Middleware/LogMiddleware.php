<?php

namespace Borsch\Middleware;

use Monolog\Logger;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

/**
 * Class LogMiddleware
 * @package Borsch\Middleware
 */
class LogMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected Logger $logger
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // You can use this middleware to log request, response or any other information.
        // For now, does nothing.
        return $handler->handle($request);
    }
}
