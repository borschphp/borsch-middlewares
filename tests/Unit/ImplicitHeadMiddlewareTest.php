<?php

use Borsch\Middleware\ImplicitHeadMiddleware;
use Borsch\Router\RouteResultInterface;
use Borsch\Router\RouterInterface;
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->router = $this->createMock(RouterInterface::class);
    $this->middleware = new ImplicitHeadMiddleware($this->router);
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process non HEAD request', function () {
    $request = (new ServerRequest())->withMethod('GET');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process HEAD request without RouteResult', function () {
    $request = (new ServerRequest())->withMethod('HEAD');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process HEAD request with matched route', function () {
    $routeResult = $this->createMock(RouteResultInterface::class);
    $routeResult->expects($this->once())
        ->method('getMatchedRoute')
        ->willReturn(true);

    $request = (new ServerRequest())
        ->withMethod('HEAD')
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
