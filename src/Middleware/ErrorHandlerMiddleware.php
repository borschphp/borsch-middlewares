<?php

namespace Borsch\Middleware;

use Borsch\Formatter\FormatterInterface;
use ErrorException;
use Laminas\Diactoros\Response;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Throwable;

/**
 * Class ErrorHandlerMiddleware
 * @package Borsch\Middleware
 */
class ErrorHandlerMiddleware implements MiddlewareInterface
{

    public function __construct(
        protected FormatterInterface $formatter,
        /** @var callable[] $listeners */
        protected array $listeners = []
    ) {}

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

            $response = $this->formatter->format(
                $this->getResponseWithStatusCode($throwable),
                $throwable,
                $request
            );
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

    /**
     * @param Throwable $throwable
     * @return ResponseInterface
     */
    protected function getResponseWithStatusCode(Throwable $throwable): ResponseInterface
    {
        $status_code = filter_var($throwable->getCode(), FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 400,
                'max_range' => 599
            ]
        ]) ?: 500;

        return new Response(status: $status_code);
    }
}
