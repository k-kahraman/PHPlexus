<?php

namespace PHPlexus\Tests\Core\Http;

use PHPlexus\Http\Request;
use PHPlexus\Http\Response;
use PHPUnit\Framework\TestCase;

class RequestResponseTest extends TestCase
{
    protected function setUp(): void
    {
        Request::setTrustedProxies([]);
    }

    public function testRawInputsArePreserved()
    {
        $server = ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/path?q=<script>'];
        $query = ['q' => '<script>'];
        $post = ['p' => '<b>hello</b>'];

        $request = new Request($server, $query, $post);

        $this->assertEquals('<script>', $request->getQueryParam('q'));
        $this->assertEquals('<b>hello</b>', $request->getPostParam('p'));
    }

    public function testJsonBodyParsing()
    {
        // Create temp file for bodySource mock
        $tempFile = tempnam(sys_get_temp_dir(), 'json_body');
        file_put_contents($tempFile, json_encode(['foo' => 'bar', 'nested' => ['a' => 1]]));

        $server = ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/api'];
        $headers = ['Content-Type' => 'application/json; charset=utf-8'];

        $request = new Request($server, [], [], $headers, $tempFile);

        $this->assertEquals('bar', $request->getPostParam('foo'));
        $this->assertEquals(['a' => 1], $request->getPostParam('nested'));

        unlink($tempFile);
    }

    public function testTrustedProxiesSecure()
    {
        // Untrusted proxy
        $server = [
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.195'
        ];
        $request = new Request($server);
        $this->assertFalse($request->isSecure());
        $this->assertEquals('10.0.0.1', $request->getClientIp());

        // Trusted proxy
        Request::setTrustedProxies(['10.0.0.1']);
        $request = new Request($server);
        $this->assertTrue($request->isSecure());
        $this->assertEquals('203.0.113.195', $request->getClientIp());
    }

    public function testResponseGetters()
    {
        $response = new Response();
        $response->setStatusCode(404)
                 ->setContent('Not Found')
                 ->addHeader('Content-Type', 'text/plain');

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getContent());
        $this->assertEquals('text/plain', $response->getHeader('Content-Type'));
    }
}
