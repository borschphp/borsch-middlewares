<?php

namespace Borsch\Middleware;

use Psr\Http\Message\{ResponseFactoryInterface, ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

/**
 * Class TrailingSlashMiddleware
 * @package Borsch\Middleware
 */
class TrailingSlashMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected ResponseFactoryInterface $response_factory,
    ) {}

    /**
     * @inheritDoc
     * @link http://www.slimframework.com/docs/v4/cookbook/route-patterns.html
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && str_ends_with($path, '/')) {
            // Permanently redirect paths with a trailing slash to their non-trailing equivalent
            $uri = $uri->withPath(rtrim($path, '/'));

            if ($request->getMethod() == 'GET') {
                return $this->response_factory->createResponse(301)
                    ->withHeader('Location', (string)$uri);
            }

            $request = $request->withUri($uri);
        }

        return $handler->handle($request);
    }
}
