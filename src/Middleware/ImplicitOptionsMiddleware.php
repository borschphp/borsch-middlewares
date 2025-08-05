<?php

namespace Borsch\Middleware;

use Borsch\Router\Contract\RouteResultInterface;
use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use function implode;

/**
 * Class ImplicitOptionsMiddleware
 * @package Borsch\Middleware
 */
class ImplicitOptionsMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected ResponseFactoryInterface $response_factory,
    ) {}

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strtoupper($request->getMethod()) != 'OPTIONS') {
            return $handler->handle($request);
        }

        $result = $request->getAttribute(RouteResultInterface::class);
        if (!$result) {
            return $handler->handle($request);
        }

        if ($result->isFailure() && !$result->isMethodFailure()) {
            return $handler->handle($request);
        }

        if ($result->getMatchedRoute()) {
            return $handler->handle($request);
        }

        $response = $this->response_factory->createResponse();

        return $response->withHeader(
            'Allow',
            implode(', ', $result->getAllowedMethods())
        );
    }
}
