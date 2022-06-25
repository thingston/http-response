<?php

declare(strict_types=1);

namespace Thingston\Http\Response;

use Psr\Http\Message\ResponseInterface;

interface ResponseEmitterInterface
{
    public const MAX_BUFFER_SIZE = 8192;

    public function emit(ResponseInterface $response): void;
}
