<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Response;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use Thingston\Http\Exception\InternalServerErrorException;
use Thingston\Http\Response\XmlResponse;

final class XmlResponseTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $response = new XmlResponse();

        $this->assertSame([XmlResponse::CONTENT_TYPE], $response->getHeader('content-type'));
    }

    public function testFromDomConstructor(): void
    {
        $dom = new DOMDocument();
        $dom->loadXML('<foo>bar</foo>');

        $response = XmlResponse::fromDOM($dom);

        $this->assertSame($dom->saveXML(), $response->getBody()->getContents());
    }

    public function testFromDomError(): void
    {
        $dom = $this->createConfiguredMock(DOMDocument::class, [
            'saveXML' => false,
        ]);

        $this->expectException(InternalServerErrorException::class);
        XmlResponse::fromDOM($dom);
    }
}
