<?php

namespace PHPlexus\DI;

use Exception;
use ReflectionClass;
use ReflectionException;

class BindingNotFoundException extends Exception
{
}
class CircularDependencyException extends Exception
{
}
class ResolutionException extends Exception
{
}

class Container implements ContainerInterface
{

    private $bindings = [];
    private $instances = [];
    private $resolving = [];
    private $extensions = [];
    private $resolved = [];

    public function bind(string $abstract, $concrete = null): void
    {
        $this->bindings[$abstract] = $concrete ?: $abstract;
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        if (!array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = 'uninitialized';
        }
    }

    public function instance(string $abstract, $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    public function extend(string $abstract, \Closure $closure): void
    {
        $this->extensions[$abstract][] = $closure;
    }

    public function make(string $abstract)
    {
        if (array_key_exists($abstract, $this->resolved)) {
            return $this->resolved[$abstract];
        }

        $object = $this->resolve($abstract);

        if (isset($this->extensions[$abstract])) {
            foreach ($this->extensions[$abstract] as $extension) {
                $object = $extension($object, $this);
            }
        }

        $this->resolved[$abstract] = $object;

        return $object;
    }

    private function resolve(string $abstract)
    {
        if (array_key_exists($abstract, $this->instances) && $this->instances[$abstract] !== 'uninitialized') {
            return $this->instances[$abstract];
        }

        if (!isset($this->bindings[$abstract])) {
            if (isset($this->extensions[$abstract])) {
                throw new BindingNotFoundException("No binding found for $abstract");
            }
            throw new ResolutionException("No binding found for $abstract");
        }



        $concrete = $this->bindings[$abstract];

        $object = ($concrete instanceof \Closure) ? $concrete($this) : $this->instantiate($concrete);

        if (array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function instantiate(string $className)
    {
        if (in_array($className, $this->resolving)) {
            throw new CircularDependencyException("Circular dependency detected: " . implode(' -> ', $this->resolving) . " -> $className");
        }

        $this->resolving[] = $className;

        try {
            $reflector = new ReflectionClass($className);

            if (!$reflector->isInstantiable()) {
                throw new ResolutionException("[$className] is not instantiable.");
            }

            $constructor = $reflector->getConstructor();

            if (is_null($constructor)) {
                array_pop($this->resolving);
                return new $className;
            }

            $dependencies = [];
            foreach ($constructor->getParameters() as $parameter) {
                $dependency = $parameter->getType();

                if ($dependency === null) {
                    if (!$parameter->isDefaultValueAvailable()) {
                        throw new ResolutionException("Primitive parameter without a default value: {$parameter->getName()} in class $className is not supported.");
                    }
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    $dependencies[] = $this->make($dependency->getName());
                }
            }

            array_pop($this->resolving);
            return $reflector->newInstanceArgs($dependencies);

        } catch (ReflectionException $e) {
            array_pop($this->resolving); // Ensure cleanup if instantiation fails
            throw new ResolutionException("Failed resolving $className: " . $e->getMessage());
        }
    }
}