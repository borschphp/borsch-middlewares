<?php

namespace Borsch\Formatter;

use Psr\Http\Message\ResponseInterface;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run as Whoops;

/**
 * Class HtmlFormatter
 * @package Borsch\Formatter
 */
class HtmlFormatter
{

    public function __construct(
        protected bool $is_production = true
    ) {}

    public function __invoke(ResponseInterface $response, Throwable $throwable): ResponseInterface
    {
        $body = $this->getWhoopsHandledException($throwable);

        $response->getBody()->write($body);

        return $response
            ->withHeader('Content-Type', 'text/html');
    }

    protected function getWhoopsHandledException(Throwable $throwable): string
    {
        $whoops = new Whoops();
        $whoops->allowQuit(false);
        $whoops->writeToOutput(false);
        $whoops->pushHandler(new PrettyPageHandler);

        return $whoops->handleException($throwable);
    }
}
