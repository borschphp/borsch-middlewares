<?php

namespace Borsch\Middleware;

use Closure;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

/**
 * Class NotFoundHandlerMiddleware
 *
 * Generates a 404 Not Found response.
 *
 * @package Borsch\Middleware
 */
class NotFoundHandlerMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected Closure|ResponseInterface $response
    ) {}

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->response instanceof ResponseInterface) {
            return $this->response;
        }

        return call_user_func($this->response, $request, $handler);
    }
}
