<?php

namespace Borsch\Formatter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

/**
 * Class JsonFormatter
 *
 * Returns an RFC 7807 ProblemDetails-like response.
 *
 * @package Borsch\Formatter
 * @see https://datatracker.ietf.org/doc/html/rfc7807
 */
class JsonFormatter implements FormatterInterface
{

    public function format(ResponseInterface $response, Throwable $throwable, RequestInterface $request): ResponseInterface
    {
        $response
            ->getBody()
            ->write(
                json_encode(
                    array_filter([
                        'type' => $this->getRFCSection($response->getStatusCode()),
                        'title' => $response->getReasonPhrase(),
                        'status' => $response->getStatusCode(),
                        'detail' => $throwable->getMessage(),
                        'instance' => $request->getUri()->getPath(),
                        'traceId' => $request->hasHeader('X-Trace-ID') ?
                            $request->getHeader('X-Trace-ID')[0] :
                            null
                    ])
                )
            );

        return $response->withHeader('Content-Type', 'application/json');
    }

    protected function getRFCSection(int $status_code): string
    {
        return match ($status_code) {
            400 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.1',
            401 => 'https://datatracker.ietf.org/doc/html/rfc7235#section-3.1',
            402 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.2',
            403 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.3',
            404 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.4',
            405 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.5',
            406 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.6',
            407 => 'https://datatracker.ietf.org/doc/html/rfc7235#section-3.2',
            408 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.7',
            409 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.8',
            410 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.9',
            411 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.10',
            413 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.11',
            414 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.12',
            415 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.13',
            417 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.14',
            426 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.5.15',
            500 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.1',
            501 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.2',
            502 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.3',
            503 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.4',
            504 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.5',
            505 => 'https://datatracker.ietf.org/doc/html/rfc7231#section-6.6.6',
            default => 'https://datatracker.ietf.org/doc/html/rfc7235'
        };
    }
}
