<?php

namespace Borsch\Middleware;

use Borsch\Router\Contract\RouteResultInterface;
use Laminas\Diactoros\Response;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

/**
 * Class MethodNotAllowedMiddleware
 * @package Borsch\Middleware
 */
class MethodNotAllowedMiddleware implements MiddlewareInterface
{

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route_result = $request->getAttribute(RouteResultInterface::class);
        if (!$route_result || !$route_result->isMethodFailure()) {
            return $handler->handle($request);
        }

        return new Response(status: 405, headers: [
            'Allow' => implode(',', $route_result->getAllowedMethods())
        ]);
    }
}
