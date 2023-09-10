<?php
namespace PHPlexus\Tests\Core\DI;

use PHPlexus\DI\Container;
use PHPUnit\Framework\TestCase;

class CircularA
{
    public function __construct(CircularB $b)
    {
    }
}

class CircularB
{
    public function __construct(CircularA $a)
    {
    }
}

class ServiceWithDefaultPrimitive
{
    public function __construct($value = 'default')
    {
    }
}

class ContainerTest extends TestCase
{
    public function testBindAndMake()
    {
        $container = new Container();
        $container->bind('service', \stdClass::class);

        $object = $container->make('service');

        $this->assertInstanceOf(\stdClass::class, $object);
    }

    public function testSingletonBinding()
    {
        $container = new Container();
        $container->singleton('service', \stdClass::class);

        $firstInstance = $container->make('service');
        $secondInstance = $container->make('service');

        $this->assertSame($firstInstance, $secondInstance);
    }

    public function testInstanceBinding()
    {
        $container = new Container();
        $std = new \stdClass();
        $container->instance('service', $std);

        $this->assertSame($std, $container->make('service'));
    }

    public function testExtension()
    {
        $container = new Container();
        $container->bind('service', \stdClass::class);

        $container->extend('service', function ($service, $container) {
            $service->extended = true;
            return $service;
        });

        $object = $container->make('service');
        $this->assertTrue($object->extended);
    }

    public function testResolutionException()
    {
        $this->expectException(\PHPlexus\DI\ResolutionException::class);

        $container = new Container();
        $container->make('nonexistent');
    }

    public function testCircularDependencyDetection()
    {
        $this->expectException(\PHPlexus\DI\CircularDependencyException::class);

        $container = new Container();
        $container->bind(CircularA::class, CircularA::class);
        $container->bind(CircularB::class, CircularB::class);
        $container->make(CircularA::class);
    }

    public function testDefaultPrimitiveValueResolution()
    {
        $container = new Container();
        $container->bind(ServiceWithDefaultPrimitive::class, ServiceWithDefaultPrimitive::class);
        $object = $container->make(ServiceWithDefaultPrimitive::class);
        $this->assertInstanceOf(ServiceWithDefaultPrimitive::class, $object);
    }

    public function testExtendOnNonBoundService()
    {
        $this->expectException(\PHPlexus\DI\BindingNotFoundException::class);

        $container = new Container();
        $container->extend(\stdClass::class, function ($service, $container) {
            $service->extended = true;
            return $service;
        });
        $object = $container->make(\stdClass::class);
    }


    public function testMultipleExtensions()
    {
        $container = new Container();
        $container->bind('service', \stdClass::class);

        $container->extend('service', function ($service, $container) {
            $service->extensionA = true;
            return $service;
        });

        $container->extend('service', function ($service, $container) {
            $service->extensionB = true;
            return $service;
        });

        $object = $container->make('service');
        $this->assertTrue($object->extensionA);
        $this->assertTrue($object->extensionB);
    }

}