<?php

namespace PHPlexus\DI;

use Exception;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

class BindingNotFoundException extends Exception implements NotFoundExceptionInterface {}

class CircularDependencyException extends Exception implements ContainerExceptionInterface {}

class ResolutionException extends Exception implements ContainerExceptionInterface {}

class Container implements ContainerInterface {
    private array $bindings = [];
    private array $instances = [];
    private array $resolving = [];
    private array $extensions = [];
    private array $resolved = [];
    private array $reflectionCache = [];
    private bool $isLocked = false;

    public function bind(string $abstract, mixed $concrete = null): void {
        if ($this->isLocked) {
            throw new ResolutionException("Container is locked. Cannot bind '$abstract' after resolution has started.");
        }
        $this->bindings[$abstract] = $concrete ?: $abstract;
    }

    public function singleton(string $abstract, mixed $concrete = null): void {
        if ($this->isLocked) {
            throw new ResolutionException("Container is locked. Cannot bind singleton '$abstract' after resolution has started.");
        }
        $this->bind($abstract, $concrete);
        if (!array_key_exists($abstract, $this->instances)) {
            $this->instances[$abstract] = 'uninitialized';
        }
    }

    public function instance(string $abstract, mixed $instance): void {
        if ($this->isLocked) {
            throw new ResolutionException("Container is locked. Cannot bind instance '$abstract' after resolution has started.");
        }
        $this->instances[$abstract] = $instance;
    }

    public function extend(string $abstract, \Closure $closure): void {
        if ($this->isLocked) {
            throw new ResolutionException("Container is locked. Cannot extend '$abstract' after resolution has started.");
        }
        $this->extensions[$abstract][] = $closure;
    }

    public function lock(): void {
        $this->isLocked = true;
    }

    public function make(string $abstract): mixed {
        $this->lock();

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

    public function get(string $id): mixed {
        try {
            return $this->make($id);
        } catch (BindingNotFoundException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ResolutionException("Error resolving service '$id': " . $e->getMessage(), 0, $e);
        }
    }

    public function has(string $id): bool {
        return isset($this->bindings[$id]) || array_key_exists($id, $this->instances);
    }

    private function resolve(string $abstract): mixed {
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

    private function instantiate(string $className): object {
        if (in_array($className, $this->resolving)) {
            throw new CircularDependencyException("Circular dependency detected: " . implode(' -> ', $this->resolving) . " -> $className");
        }

        $this->resolving[] = $className;

        try {
            if (isset($this->reflectionCache[$className])) {
                [$isInstantiable, $constructorParams] = $this->reflectionCache[$className];
            } else {
                $reflector = new ReflectionClass($className);
                $isInstantiable = $reflector->isInstantiable();
                $constructorParams = null;

                if ($isInstantiable) {
                    $constructor = $reflector->getConstructor();
                    if ($constructor !== null) {
                        $constructorParams = [];
                        foreach ($constructor->getParameters() as $parameter) {
                            $type = $parameter->getType();
                            $constructorParams[] = [
                                'name' => $parameter->getName(),
                                'hasDefault' => $parameter->isDefaultValueAvailable(),
                                'default' => $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null,
                                'typeName' => ($type instanceof ReflectionNamedType && !$type->isBuiltin()) ? $type->getName() : null
                            ];
                        }
                    }
                }
                $this->reflectionCache[$className] = [$isInstantiable, $constructorParams];
            }

            if (!$isInstantiable) {
                throw new ResolutionException("[$className] is not instantiable.");
            }

            if ($constructorParams === null) {
                array_pop($this->resolving);
                return new $className();
            }

            $dependencies = [];
            foreach ($constructorParams as $paramInfo) {
                if ($paramInfo['typeName'] !== null) {
                    $dependencies[] = $this->make($paramInfo['typeName']);
                } else {
                    if (!$paramInfo['hasDefault']) {
                        throw new ResolutionException("Primitive parameter without a default value: {$paramInfo['name']} in class $className is not supported.");
                    }
                    $dependencies[] = $paramInfo['default'];
                }
            }

            array_pop($this->resolving);
            return new $className(...$dependencies);

        } catch (ReflectionException $e) {
            array_pop($this->resolving);
            throw new ResolutionException("Failed resolving $className: " . $e->getMessage(), 0, $e);
        }
    }
}