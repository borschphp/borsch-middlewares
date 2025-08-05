<?php

use Borsch\Middleware\MethodNotAllowedMiddleware;
use Borsch\Router\Contract\RouteResultInterface;
use Borsch\Http\{Factory\ResponseFactory, Response, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(MethodNotAllowedMiddleware::class);

beforeEach(function () {
    $this->middleware = new MethodNotAllowedMiddleware(new ResponseFactory());
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process request with allowed method', function () {
    $routeResult = $this->createMock(RouteResultInterface::class);
    $routeResult->expects($this->once())
        ->method('isMethodFailure')
        ->willReturn(false);

    $request = (new ServerRequest('GET', new Uri('/')))
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process request with method not allowed', function () {
    $routeResult = $this->createMock(RouteResultInterface::class);
    $routeResult->expects($this->once())
        ->method('isMethodFailure')
        ->willReturn(true);
    $routeResult->expects($this->once())
        ->method('getAllowedMethods')
        ->willReturn(['GET', 'POST']);

    $request = (new ServerRequest('GET', new Uri('/')))
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->getStatusCode())->toBe(405)
        ->and($response->getHeaderLine('Allow'))->toBe('GET,POST');
});
