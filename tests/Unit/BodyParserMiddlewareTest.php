<?php

use Borsch\Middleware\BodyParserMiddleware;
use Laminas\Diactoros\{ServerRequest, Stream};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->middleware = new BodyParserMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('URL encoded body parsing', function () {
    $body = new Stream('php://memory', 'wb+');
    $body->write('key1=value1&key2=value2');
    $body->rewind();

    $request = (new ServerRequest())
        ->withMethod('POST')
        ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
        ->withBody($body);

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
    $body = new Stream('php://memory', 'r+');
    $body->write(json_encode(['key1' => 'value1', 'key2' => 'value2']));
    $body->rewind();

    $request = (new ServerRequest())
        ->withMethod('POST')
        ->withHeader('Content-Type', 'application/json')
        ->withBody($body);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($this->callback(function ($request) {
            $parsedBody = $request->getParsedBody();
            return $parsedBody['key1'] === 'value1' && $parsedBody['key2'] === 'value2';
        }));

    $this->middleware->process($request, $this->handler);
});

test('XML body parsing', function () {
    $body = new Stream('php://memory', 'r+');
    $body->write(json_encode(['key1' => 'value1', 'key2' => 'value2']));
    $body->rewind();

    $request = (new ServerRequest())
        ->withMethod('POST')
        ->withHeader('Content-Type', 'application/json')
        ->withBody($body);

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($this->callback(function ($request) {
            $parsedBody = $request->getParsedBody();
            return $parsedBody['key1'] === 'value1' && $parsedBody['key2'] === 'value2';
        }));

    $this->middleware->process($request, $this->handler);
});

test('Non-body request', function () {
    $request = (new ServerRequest())->withMethod('GET');

    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request);

    $this->middleware->process($request, $this->handler);
});
