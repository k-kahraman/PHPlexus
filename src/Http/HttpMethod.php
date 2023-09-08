<?php
namespace PHPlexus\Http;

final class HttpMethod
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PATCH = 'PATCH';
    const OPTIONS = 'OPTIONS';
    const HEAD = 'HEAD';

    private function __construct()
    {
        // Prevent instantiation
    }
}