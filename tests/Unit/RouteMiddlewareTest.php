<?php

use Borsch\Middleware\RouteMiddleware;
use Borsch\Router\{RouteInterface, RouteResult, RouteResultInterface, RouterInterface};
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->router = $this->createMock(RouterInterface::class);
    $this->middleware = new RouteMiddleware($this->router);
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process with matched route', function () {
    $routeResult = RouteResult::fromRouteSuccess($this->createMock(RouteInterface::class));

    $this->router->expects($this->once())
        ->method('match')
        ->willReturn($routeResult);

    $request = new ServerRequest();
    $request = $request->withAttribute(RouteResultInterface::class, $routeResult);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process with unmatched route', function () {
    $routeResult = RouteResult::fromRouteFailure(['POST']);

    $this->router->expects($this->once())
        ->method('match')
        ->willReturn($routeResult);

    $request = new ServerRequest();
    $request = $request->withAttribute(RouteResultInterface::class, $routeResult);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
