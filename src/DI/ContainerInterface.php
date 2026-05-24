<?php
namespace PHPlexus\DI;

use Psr\Container\ContainerInterface as PsrContainerInterface;

interface ContainerInterface extends PsrContainerInterface {
    public function bind(string $abstract, mixed $concrete = null): void;
    public function singleton(string $abstract, mixed $concrete = null): void;
    public function instance(string $abstract, mixed $instance): void;
    public function extend(string $abstract, \Closure $closure): void;
    public function make(string $abstract): mixed;
}
