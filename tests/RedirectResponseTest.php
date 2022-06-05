<?php

declare(strict_types=1);

namespace Thingston\Tests\Http\Response;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Thingston\Http\Response\RedirectResponse;

final class RedirectResponseTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        $uri = new Uri('http://example.org');
        $response = new RedirectResponse($uri);

        $this->assertSame([(string) $uri], $response->getHeader('location'));
        $this->assertSame(302, $response->getStatusCode());
    }

    public function testRedirectResponseFromUri(): void
    {
        $uri = new Uri('http://example.org');
        $response = RedirectResponse::fromUri($uri);

        $this->assertSame([(string) $uri], $response->getHeader('location'));
        $this->assertSame(302, $response->getStatusCode());
    }
}
