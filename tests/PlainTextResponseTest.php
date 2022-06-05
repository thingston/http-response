<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Thingston\Http\Response\PlainTextResponse;

final class PlainTextResponseTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $response = new PlainTextResponse();

        $this->assertSame([PlainTextResponse::CONTENT_TYPE], $response->getHeader('content-type'));
    }
}
