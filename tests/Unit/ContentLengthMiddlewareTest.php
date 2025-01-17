<?php

use Borsch\Middleware\ContentLengthMiddleware;
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->middleware = new ContentLengthMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Content-Length header is added to the response', function () {
    $content = 'Hello, World!';

    $response = new Response();
    $response->getBody()->write('Hello, World!');

    $this->handler
        ->expects($this->once())
        ->method('handle')
        ->willReturn($response);

    $response = $this->middleware->process(new ServerRequest(), $this->handler);

    expect($response->hasHeader('Content-Length'))->toBeTrue()
        ->and((int)$response->getHeaderLine('Content-Length'))->toBe(strlen($content));
});

test('Content-Length header is not overwritten', function () {
    $response = (new Response())->withHeader('Content-Length', 42);
    $response->getBody()->write('Hello, World!');

    $this->handler
        ->expects($this->once())
        ->method('handle')
        ->willReturn($response);

    $response = $this->middleware->process(new ServerRequest(), $this->handler);

    expect($response->hasHeader('Content-Length'))->toBeTrue()
        ->and((int)$response->getHeaderLine('Content-Length'))->toBe(42);
});

test('Content-Length is not set for empty body', function () {
    $response = new Response();

    $this->handler->expects($this->once())
        ->method('handle')
        ->willReturn($response);

    $request = new ServerRequest();
    $response = $this->middleware->process($request, $this->handler);

    expect($response->hasHeader('Content-Length'))->toBeTrue()
        ->and((int)$response->getHeaderLine('Content-Length'))->toBe(0);
});
