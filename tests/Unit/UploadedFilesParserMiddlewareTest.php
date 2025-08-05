<?php

use Borsch\Middleware\UploadedFilesParserMiddleware;
use Psr\Http\Message\UploadedFileInterface;
use Borsch\Http\{Factory\StreamFactory, Factory\UploadedFileFactory, Response, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

covers(UploadedFilesParserMiddleware::class);

beforeEach(function () {
    $this->middleware = new UploadedFilesParserMiddleware(
        new UploadedFileFactory(),
        new StreamFactory()
    );
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process without uploaded files', function () {
    $_FILES = [];

    $request = new ServerRequest('GET', new Uri('/'));
    $this->handler->expects($this->once())
        ->method('handle')
        ->with($request)
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});

test('Process with single uploaded file', function () {
    $file = tempnam(sys_get_temp_dir(), 'test');

    $_FILES = [
        'file' => [
            'tmp_name' => $file,
            'size' => 123,
            'error' => UPLOAD_ERR_OK,
            'name' => 'test.txt',
            'type' => 'text/plain'
        ]
    ];

    $request = new ServerRequest('GET', new Uri('/'));
    $this->handler->expects($this->once())
        ->method('handle')
        ->with($this->callback(function (ServerRequest $request) {
            $uploadedFiles = $request->getUploadedFiles();
            $this->assertArrayHasKey('file', $uploadedFiles);
            $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['file']);
            return true;
        }))
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
