<?php

declare(strict_types=1);

namespace Thingston\Http\Response;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\StreamInterface;

abstract class AbstractResponse extends Response
{
    public const CONTENT_TYPE = 'text/html';

    /**
     * @param string|resource|StreamInterface|null $body
     * @param int $status
     * @param array<string, string|string[]> $headers
     * @param string $version
     * @param string|null $reason
     */
    public function __construct(
        $body = null,
        int $status = 200,
        array $headers = [],
        string $version = '1.1',
        ?string $reason = null
    ) {
        $mergedHeaders = array_merge(['content-type' => $this->getContentType()], $headers);
        parent::__construct($status, $mergedHeaders, $body, $version, $reason);
    }

    public function getContentType(): string
    {
        return static::CONTENT_TYPE;
    }
}
