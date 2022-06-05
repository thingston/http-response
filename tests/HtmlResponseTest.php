<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Response;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Thingston\Http\Exception\InternalServerErrorException;
use Thingston\Http\Response\HtmlResponse;

final class HtmlResponseTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $response = new HtmlResponse();

        $this->assertSame([HtmlResponse::CONTENT_TYPE], $response->getHeader('content-type'));
    }

    public function testFromDomConstructor(): void
    {
        $dom = new DOMDocument();
        $dom->loadHTML('<html><head><title>Test</title></head><body><h1>Test</h1></body></html>');

        $response = HtmlResponse::fromDOM($dom);

        $this->assertSame($dom->saveHTML(), $response->getBody()->getContents());
    }

    public function testFromDomError(): void
    {
        $dom = $this->createConfiguredMock(DOMDocument::class, [
            'saveHTML' => false,
        ]);

        $this->expectException(InternalServerErrorException::class);
        HtmlResponse::fromDOM($dom);
    }
}
