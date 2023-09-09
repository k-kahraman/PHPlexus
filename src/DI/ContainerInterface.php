<?php
namespace PHPlexus\DI;

interface ContainerInterface
{
    public function bind(string $abstract, $concrete): void;
    public function singleton(string $abstract, $concrete): void;
    public function instance(string $abstract, $instance): void;
    public function extend(string $abstract, \Closure $closure): void;
    public function make(string $abstract);
}
