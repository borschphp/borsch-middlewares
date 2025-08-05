<?php

use Borsch\Middleware\BodyParserMiddleware;
use Borsch\Http\{ServerRequest, Stream, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(BodyParserMiddleware::class);

beforeEach(function () {
    $this->middleware = new BodyParserMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('URL encoded body parsing', function () {
    $body = new Stream(fopen('php://memory', 'wb+'));
    $body->write('key1=value1&key2=value2');
    $body->rewind();

    $request = (new ServerRequest('POST', new Uri('/'), $body))
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded');

    $this->handler
        ->expects($this->once())
        ->method('handle')
        ->with($this->callback(function ($request) {
            $parsedBody = $request->getParsedBody();
            return $parsedBody['key1'] === 'value1' && $parsedBody['key2'] === 'value2';
        }));

    $this->middleware->process($request, $this->handler);
});

test('JSON body parsing', function () {
    $body = new Stream(fopen('php://memory', 'r+'));
    $body->write(json_encode(['key1' => 'value1', 'key2' => 'value2']));
    $body->rewind();

    $request = (new ServerRequest('POST', new Uri('/'), $body))
        ->withHeader('Content-Type', 'application/json');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($this->callback(function ($request) {
            $parsedBody = $request->getParsedBody();
            return $parsedBody['key1'] === 'value1' && $parsedBody['key2'] === 'value2';
        }));

    $this->middleware->process($request, $this->handler);
});

test('XML body parsing', function () {
    $body = new Stream(fopen('php://memory', 'r+'));
    $body->write(json_encode(['key1' => 'value1', 'key2' => 'value2']));
    $body->rewind();

    $request = (new ServerRequest('POST', new Uri('/'), $body))
        ->withHeader('Content-Type', 'application/json');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($this->callback(function ($request) {
            $parsedBody = $request->getParsedBody();
            return $parsedBody['key1'] === 'value1' && $parsedBody['key2'] === 'value2';
        }));

    $this->middleware->process($request, $this->handler);
});

test('Non-body request', function () {
    $request = (new ServerRequest('POST', new Uri('/')))->withMethod('GET');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request);

    $this->middleware->process($request, $this->handler);
});
