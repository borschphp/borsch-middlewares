<?php

use Borsch\Middleware\ImplicitOptionsMiddleware;
use Borsch\Router\Contract\{RouteInterface, RouteResultInterface};
use Borsch\Http\{Factory\ResponseFactory, Response, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(ImplicitOptionsMiddleware::class);

beforeEach(function () {
    $this->middleware = new ImplicitOptionsMiddleware(new ResponseFactory());
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process non OPTION request', function () {
    $request = (new ServerRequest('GET', new Uri('/')))->withMethod('GET');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process OPTION request without RouteResult', function () {
    $request = (new ServerRequest('GET', new Uri('/')))->withMethod('OPTIONS');

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

    $request = (new ServerRequest('GET', new Uri('/')))
        ->withMethod('OPTIONS')
        ->withAttribute(RouteResultInterface::class, $routeResult);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
