<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Response;

use PHPUnit\Framework\TestCase;
use Thingston\Http\Exception\InternalServerErrorException;
use Thingston\Http\Response\JsonResponse;

final class JsonResponseTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $response = new JsonResponse();

        $this->assertSame([JsonResponse::CONTENT_TYPE], $response->getHeader('content-type'));
    }

    public function testJsonResponseFromData(): void
    {
        $data = ['foo' => 'bar'];
        $response = JsonResponse::fromData($data);

        $this->assertSame([JsonResponse::CONTENT_TYPE], $response->getHeader('content-type'));
        $this->assertSame(json_encode($data), $response->getBody()->getContents());
    }

    public function testFromDataError(): void
    {
        $this->expectException(InternalServerErrorException::class);
        JsonResponse::fromData([NAN]);
    }
}
