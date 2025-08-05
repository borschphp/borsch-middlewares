<?php

use Borsch\Middleware\NotFoundHandlerMiddleware;
use Borsch\Http\{Response, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(NotFoundHandlerMiddleware::class);

beforeEach(function () {
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process with direct response', function () {
    $response = new Response();
    $this->middleware = new NotFoundHandlerMiddleware($response);

    $request = new ServerRequest('GET', new Uri('/'));
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($response);
});

test('Process with closure response', function () {
    $response = new Response();
    $this->middleware = new NotFoundHandlerMiddleware(function () use ($response) {
        return $response;
    });

    $request = new ServerRequest('GET', new Uri('/'));
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($response);
});
