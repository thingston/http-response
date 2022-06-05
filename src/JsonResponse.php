<?php

declare(strict_types=1);

namespace Thingston\Http\Response;

use Thingston\Http\Exception\InternalServerErrorException;

use function json_encode;

class JsonResponse extends AbstractResponse
{
    public const CONTENT_TYPE = 'application/json';

    /**
     * @param array<mixed>|object $data
     * @param int $status
     * @param array<string, string|string[]> $headers
     * @return self
     */
    public static function fromData(array|object $data, int $status = 200, array $headers = []): self
    {
        if (false === $body = json_encode($data)) {
            $headers['content-type'] = self::CONTENT_TYPE;
            throw new InternalServerErrorException('Unable to encode data to JSON.', [], $headers);
        }

        return new self($body, $status, $headers);
    }
}
