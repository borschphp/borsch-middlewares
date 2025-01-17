<?php

use Borsch\Middleware\TrailingSlashMiddleware;
use Laminas\Diactoros\{Response, Response\RedirectResponse, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->middleware = new TrailingSlashMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process with trailing slash', function () {
    $request = (new ServerRequest())->withUri(new Uri('/path/'));

    $response = $this->middleware->process($request, $this->handler);

    expect($response->getHeaderLine('Location'))->toBe('/path')
        ->and($response->getStatusCode())->toBe(301)
        ->and($response)->toBeInstanceOf(RedirectResponse::class);
});

test('Process without trailing slash', function () {
    $request = (new ServerRequest())->withUri(new Uri('/path'));

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process root path', function () {
    $request = (new ServerRequest())->withUri(new Uri('/'));

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
