<?php

namespace Borsch\Middleware;

use ErrorException;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Throwable;

/**
 * Class ErrorHandlerMiddleware
 * @package Borsch\Middleware
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{

    /** @var callable(Throwable $e, ServerRequestInterface $request): ResponseInterface */
    protected $error_handler;

    public function __construct(
        callable $error_handler,
        /** @var callable[] $listeners */
        protected array $listeners = []
    ) {
        $this->error_handler = $error_handler;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $this->setErrorHandler();

            $response = $handler->handle($request);
        } catch (Throwable $throwable) {
            $this->callListeners($throwable, $request);

            $response = call_user_func($this->error_handler, $throwable, $request);
        }

        restore_error_handler();

        return $response;
    }

    /**
     * @param callable $listener
     * @return void
     */
    public function addListener(callable $listener): void
    {
        if (!in_array($listener, $this->listeners, true)) {
            $this->listeners[] = $listener;
        }
    }

    /**
     * @return void
     * @throws ErrorException
     */
    protected function setErrorHandler(): void
    {
        set_error_handler(static function(int $errno, string $errstr, string $errfile, int $errline): bool {
            if (!(error_reporting() & $errno)) {
                // error_reporting does not include this error
                return true;
            }

            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
        });
    }

    /**
     * @param Throwable $throwable
     * @param ServerRequestInterface $request
     * @return void
     */
    protected function callListeners(Throwable $throwable, ServerRequestInterface $request): void
    {
        foreach ($this->listeners as $listener) {
            $listener($throwable, $request);
        }
    }
}
