<?php

use Borsch\Middleware\TrailingSlashMiddleware;
use Borsch\Http\{Factory\ResponseFactory, Response, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(TrailingSlashMiddleware::class);

beforeEach(function () {
    $this->middleware = new TrailingSlashMiddleware(new ResponseFactory());
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process with trailing slash', function () {
    $request = (new ServerRequest('GET', new Uri('http://example.com/path/')));

    $response = $this->middleware->process($request, $this->handler);

    expect($response->getHeaderLine('Location'))->toBe('http://example.com/path')
        ->and($response->getStatusCode())->toBe(301);
});

test('Process without trailing slash', function () {
    $request = (new ServerRequest('GET', new Uri('/')))->withUri(new Uri('/path'));

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process root path', function () {
    $request = (new ServerRequest('GET', new Uri('/')))->withUri(new Uri('/'));

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
