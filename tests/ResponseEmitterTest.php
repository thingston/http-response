<?php

declare(strict_types=1);

namespace Thingstom\Tests\Http\Response;

use GuzzleHttp\Psr7\Response;
use Thingston\Http\Response\ResponseEmitter;
use PHPUnit\Framework\TestCase;

final class ResponseEmitterTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testEmitHeaders(): void
    {
        $response = new Response(200, [
            'date' => date('r'),
            'host' => 'localhost',
            'content-type' => 'text/plain',
        ]);

        ob_start();

        (new ResponseEmitter())->emit($response);

        ob_end_clean();

        $this->assertCount(count($response->getHeaders()), xdebug_get_headers());
        $this->assertSame($response->getStatusCode(), http_response_code());
    }

    /**
     * @runInSeparateProcess
     */
    public function testContentDisposition(): void
    {
        $response = new Response(200, [
            'content-disposition' => 'inline',
            'content-type' => 'text/plain',
        ]);

        $contents = bin2hex(random_bytes(rand(5000, 10000)));
        $response->getBody()->write($contents);

        ob_start();

        (new ResponseEmitter())->emit($response);
        $this->assertSame($contents, ob_get_contents());

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testContentRangeInvalid(): void
    {
        $response = new Response(200, [
            'content-range' => 'kgs 10-20/100',
            'content-type' => 'text/plain',
        ]);

        $contents = bin2hex(random_bytes(rand(5000, 10000)));
        $response->getBody()->write($contents);

        ob_start();

        (new ResponseEmitter())->emit($response);
        $this->assertSame($contents, ob_get_contents());

        ob_end_clean();
    }

    /**
     * @runInSeparateProcess
     */
    public function testContentRange(): void
    {
        $first = rand(0, 100);
        $last = rand(ResponseEmitter::MAX_BUFFER_SIZE + 1, ResponseEmitter::MAX_BUFFER_SIZE * 2 + 1);

        $response = new Response(200, [
            'content-range' => sprintf('bytes %d-%d/*', $first, $last),
            'content-type' => 'text/plain',
        ]);

        $contents = bin2hex(random_bytes(rand($last, $last * 2)));
        $response->getBody()->write($contents);

        ob_start();

        (new ResponseEmitter())->emit($response);
        $this->assertSame(substr($contents, $first, $last - $first + 1), ob_get_contents());

        ob_end_clean();
    }
}
