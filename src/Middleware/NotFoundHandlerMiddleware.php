<?php

namespace Borsch\Middleware;

use Borsch\Exception\ProblemDetailsException;
use Laminas\Diactoros\Response\HtmlResponse;
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

    public function __construct() {}

    /**
     * @inheritDoc
     * @throws ProblemDetailsException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (str_starts_with($request->getUri()->getPath(), '/api/')) {
            throw new ProblemDetailsException(
                sprintf('No resource found for "%s"', $request->getUri()->getPath()),
                404
            );
        }

        return new HtmlResponse('Not Found', 404);
    }
}
