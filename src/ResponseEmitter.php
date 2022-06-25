<?php

declare(strict_types=1);

namespace Thingston\Http\Response;

use Psr\Http\Message\ResponseInterface;

use function flush;
use function headers_sent;
use function preg_match;
use function strlen;

class ResponseEmitter implements ResponseEmitterInterface
{
    public function __construct(private int $maxBufferLength = self::MAX_BUFFER_SIZE)
    {
        $this->maxBufferLength = $maxBufferLength;
    }

    public function emit(ResponseInterface $response): void
    {
        if (false === headers_sent()) {
            $this->emitHeaders($response);
            $this->emitStatus($response);
        }

        if ($response->hasHeader('Content-Disposition')) {
            $this->emitBodyDisposition($response);
            return;
        }

        if ($response->hasHeader('Content-Range')) {
            $this->emitBodyRange($response);
            return;
        }

        $this->emitBody($response);
    }

    private function emitHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $name => $values) {
            $replace = strtolower($name) !== 'set-cookie';

            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), $replace);
            }
        }
    }

    private function emitStatus(ResponseInterface $response): void
    {
        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ), true, $response->getStatusCode());
    }

    private function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }

    private function emitBodyDisposition(ResponseInterface $response): void
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        if (false === $body->isReadable()) {
            echo $body;
            return;
        }

        while (false === $body->eof()) {
            echo $body->read($this->maxBufferLength);
        }
    }

    private function emitBodyRange(ResponseInterface $response): void
    {
        flush();

        $body = $response->getBody();

        if (false === $body->isReadable()) {
            echo $body;
            return;
        }

        $range = $this->parseContentRange($response->getHeaderLine('Content-Range'));

        if (false === isset($range[0]) || 'bytes' !== $range[0]) {
            $this->emitBodyDisposition($response);
            return;
        }

        $first = (int) $range[1];
        $last = (int) $range[2];

        $length = $last - $first + 1;

        if ($body->isSeekable()) {
            $body->seek($first);
        }

        $remaining = $length;

        while ($remaining >= $this->maxBufferLength && false === $body->eof()) {
            $contents = $body->read($this->maxBufferLength);
            $remaining -= strlen($contents);

            echo $contents;
        }

        if ($remaining > 0 && false === $body->eof()) {
            echo $body->read($remaining);
        }
    }

    /**
     * @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
     * @return array<string|int>
     */
    private function parseContentRange(string $header): array
    {
        $pattern = '/(?P<unit>[\w]+)\s+(?P<first>\d+)-(?P<last>\d+)\/(?P<length>\d+|\*)/';
        $matches = [];

        if (false === (bool) preg_match($pattern, $header, $matches)) {
            return [];
        }

        return [
            $matches['unit'],
            (int) $matches['first'],
            (int) $matches['last'],
            $matches['length'] === '*' ? '*' : (int) $matches['length'],
        ];
    }
}
