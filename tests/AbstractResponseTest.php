<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Thingston\Http\Response\AbstractResponse;

final class AbstractResponseTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $response = new class () extends AbstractResponse {
        };

        $this->assertSame(200, $response->getStatusCode());
        $this->assertArrayHasKey('content-type', $response->getHeaders());
    }
}
