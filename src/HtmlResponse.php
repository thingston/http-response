<?php

declare(strict_types=1);

namespace Thingston\Http\Response;

use DOMDocument;
use Thingston\Http\Exception\InternalServerErrorException;

class HtmlResponse extends AbstractResponse
{
    public const CONTENT_TYPE = 'text/html';

    /**
     * @param DOMDocument $dom
     * @param int $status
     * @param array<string, string|string[]> $headers
     * @return self
     */
    public static function fromDOM(DOMDocument $dom, int $status = 200, array $headers = []): self
    {
        if (false === $body = $dom->saveHTML()) {
            $headers['content-type'] = self::CONTENT_TYPE;
            throw new InternalServerErrorException('Invalid DOM document.', [], $headers);
        }

        return new self($body, $status, $headers);
    }
}
