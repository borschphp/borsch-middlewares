<?php

use Borsch\Middleware\UploadedFilesParserMiddleware;
use Psr\Http\Message\UploadedFileInterface;
use Laminas\Diactoros\{Response, Response\RedirectResponse, ServerRequest, Uri};
use Psr\Http\Server\RequestHandlerInterface;

beforeEach(function () {
    $this->middleware = new UploadedFilesParserMiddleware();
    $this->handler = $this->createMock(RequestHandlerInterface::class);
});

test('Process without uploaded files', function () {
    $_FILES = [];

    $request = new ServerRequest();
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

    $request = new ServerRequest();
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

test('Process with multiple uploaded files', function () {
    $file1 = tempnam(sys_get_temp_dir(), 'test');
    $file2 = tempnam(sys_get_temp_dir(), 'test');

    $_FILES = [
        'files' => [
            'tmp_name' => [$file1, $file2],
            'size' => [123, 456],
            'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
            'name' => ['test1.txt', 'test2.txt'],
            'type' => ['text/plain', 'text/plain']
        ]
    ];

    $request = new ServerRequest();
    $this->handler->expects($this->once())
        ->method('handle')
        ->with($this->callback(function (ServerRequest $request) {
            $uploadedFiles = $request->getUploadedFiles();
            $this->assertArrayHasKey('files', $uploadedFiles);
            $this->assertIsArray($uploadedFiles['files']);
            $this->assertCount(2, $uploadedFiles['files']);
            $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['files'][0]);
            $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['files'][1]);
            return true;
        }))
        ->willReturn(new Response());

    $response = $this->middleware->process($request, $this->handler);

    expect($response)->toBeInstanceOf(Response::class);
});
