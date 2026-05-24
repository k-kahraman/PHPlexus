<?php

namespace PHPlexus\Core\Entity;

abstract class EntityBuilder {
    protected array $attributes = [];

    public function set(string $name, mixed $value): self {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function __call(string $method, array $arguments): self {
        if (str_starts_with($method, 'set')) {
            $attribute = lcfirst(substr($method, 3));
            $this->set($attribute, $arguments[0]);
            return $this;
        }

        throw new \BadMethodCallException("The method $method does not exist.");
    }

    abstract public function build(): Entity;
}