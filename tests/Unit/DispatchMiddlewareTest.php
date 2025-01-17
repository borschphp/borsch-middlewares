<?php

use Borsch\Middleware\DispatchMiddleware;
use Borsch\Router\RouteResultInterface;
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->middleware = new DispatchMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('RouteResult is processed', function () {
    $routeResult = $this->createMock(RouteResultInterface::class);
    $routeResult->expects($this->once())
        ->method('process')
        ->willReturn(new Response());

    $request = (new ServerRequest())
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Handler is called when no RouteResult', function () {
    $routeResult = $this->createMock(RouteResultInterface::class);
    $routeResult->expects($this->once())
        ->method('process')
        ->willReturn(new Response());

    $request = (new ServerRequest())
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
