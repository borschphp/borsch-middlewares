<?php

use AppTest\Mock\MockedListener;
use Borsch\Middleware\ErrorHandlerMiddleware;
use Psr\Http\Message\ResponseInterface;
use Borsch\Http\{Response, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(ErrorHandlerMiddleware::class);

beforeEach(function () {
    $this->middleware = new ErrorHandlerMiddleware(fn () => new Response\TextResponse('Error occurred'));
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Handle request successfully', function () {
    $response = new Response();

    $this->handler->expects($this->once())
        ->method('handle')
        ->willReturn($response);

    $request = new ServerRequest('GET', new Uri('/'));
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBe($response);
});

test('Handler request with exception', function () {
    $exception = new Exception('Test Exception', 500);

    $this->handler->expects($this->once())
        ->method('handle')
        ->willThrowException($exception);

    $formattedResponse = new Response();

    $request = new ServerRequest('GET', new Uri('/'));
    $result = $this->middleware->process($request, $this->handler);

    expect($result)->toBeInstanceOf(ResponseInterface::class);
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

    $request = new ServerRequest('GET', new Uri('/'));
    $this->middleware->process($request, $this->handler);
});
