<?php

namespace PHPlexus\Core\Entity;

abstract class EntityBuilder
{

    protected $attributes = [];

    public function set(string $name, $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function __call($method, $arguments)
    {
        // Check if the method starts with "set"
        if (strncmp($method, 'set', 3) === 0) {
            $attribute = lcfirst(substr($method, 3));
            if (isset($this->attributes[$attribute])) {
                $this->set($attribute, $arguments[0]);
                return $this;
            }
        }

        throw new \BadMethodCallException("The method $method does not exist.");
    }

    abstract public function build(): Entity;
}