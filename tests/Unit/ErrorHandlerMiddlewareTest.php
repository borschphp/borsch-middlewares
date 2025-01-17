<?php

use AppTest\Mock\MockedListener;
use Borsch\Formatter\FormatterInterface;
use Borsch\Middleware\ErrorHandlerMiddleware;
use Laminas\Diactoros\{Response, ServerRequest};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->formatter = $this->createMock(FormatterInterface::class);
    $this->middleware = new ErrorHandlerMiddleware($this->formatter);
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Handle request successfully', function () {
    $response = new Response();

    $this->handler->expects($this->once())
        ->method('handle')
        ->willReturn($response);

    $request = new ServerRequest();
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($response);
});

test('Handler request with exception', function () {
    $exception = new Exception('Test Exception', 500);

    $this->handler->expects($this->once())
        ->method('handle')
        ->willThrowException($exception);

    $formattedResponse = new Response();

    $this->formatter->expects($this->once())
        ->method('format')
        ->with($this->isInstanceOf(Response::class), $exception, $this->isInstanceOf(ServerRequest::class))
        ->willReturn($formattedResponse);

    $request = new ServerRequest();
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($formattedResponse);
});

test('Listeners are called on exception', function () {
    $exception = new \Exception('Test Exception', 500);
    $this->handler->expects($this->once())
        ->method('handle')
        ->willThrowException($exception);

    $listener = $this->createMock(MockedListener::class);

    $listener->expects($this->once())
        ->method('__invoke')
        ->with($exception, $this->isInstanceOf(ServerRequest::class));

    $this->middleware->addListener($listener);

    $formattedResponse = new Response();
    $this->formatter->expects($this->once())
        ->method('format')
        ->willReturn($formattedResponse);

    $request = new ServerRequest();
    $this->middleware->process($request, $this->handler);
});
