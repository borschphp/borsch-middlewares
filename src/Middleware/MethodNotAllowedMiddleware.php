<?php

namespace Borsch\Middleware;

use Borsch\Router\Contract\RouteResultInterface;
use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

/**
 * Class MethodNotAllowedMiddleware
 * @package Borsch\Middleware
 */
class MethodNotAllowedMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected ResponseFactoryInterface $response_factory,
    ) {}

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route_result = $request->getAttribute(RouteResultInterface::class);
        if (!$route_result || !$route_result->isMethodFailure()) {
            return $handler->handle($request);
        }

        return $this->response_factory->createResponse(405)
            ->withHeader('Allow', implode(',', $route_result->getAllowedMethods()));
    }
}
