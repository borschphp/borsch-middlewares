<?php

use Borsch\Middleware\NotFoundHandlerMiddleware;
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process with direct response', function () {
    $response = new Response();
    $this->middleware = new NotFoundHandlerMiddleware($response);

    $request = new ServerRequest();
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($response);
});

test('Process with closure response', function () {
    $response = new Response();
    $this->middleware = new NotFoundHandlerMiddleware(function () use ($response) {
        return $response;
    });

    $request = new ServerRequest();
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($response);
});
