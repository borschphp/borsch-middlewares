<?php

use Borsch\Middleware\ImplicitOptionsMiddleware;
use Borsch\Router\Contract\RouteInterface;
use Borsch\Router\Contract\RouteResultInterface;
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->middleware = new ImplicitOptionsMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process non OPTION request', function () {
    $request = (new ServerRequest())->withMethod('GET');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process OPTION request without RouteResult', function () {
    $request = (new ServerRequest())->withMethod('OPTIONS');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process OPTION request with matched route', function () {
    $routeResult = $this->createMock(RouteResultInterface::class);
    $routeResult->expects($this->once())
        ->method('getMatchedRoute')
        ->willReturn($this->createMock(RouteInterface::class));

    $request = (new ServerRequest())
        ->withMethod('OPTIONS')
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
