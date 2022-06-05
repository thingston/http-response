<?php

declare(strict_types=1);

namespace Thingston\Http\Response;

use Psr\Http\Message\UriInterface;

class RedirectResponse extends AbstractResponse
{
    public const CONTENT_TYPE = 'text/plain';

    /**
     * @param string|UriInterface $uri
     * @param int $status
     * @param array<string, string|string[]> $headers
     * @param string $version
     * @param string|null $reason
     */
    public function __construct(
        string|UriInterface $uri,
        int $status = 302,
        array $headers = [],
        string $version = '1.1',
        ?string $reason = null
    ) {
        $mergedHeaders = array_merge($headers, ['location' => (string) $uri]);
        parent::__construct(null, $status, $mergedHeaders, $version, $reason);
    }

    /**
     * @param UriInterface|string $uri
     * @param int $status
     * @param array<string, string|string[]> $headers
     * @return self
     */
    public static function fromUri(UriInterface|string $uri, int $status = 302, array $headers = []): self
    {
        return new self((string) $uri, $status, $headers);
    }
}
