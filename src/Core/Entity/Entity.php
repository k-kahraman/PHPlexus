<?php

namespace PHPlexus\Core\Entity;

abstract class Entity
{
    public function __construct(...$args)
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);

        // If a single array argument is passed
        if (func_num_args() == 1 && is_array($args[0])) {
            $attributes = $args[0];
            foreach ($properties as $property) {
                $propName = $property->getName();
                if (isset($attributes[$propName])) {
                    $property->setAccessible(true);
                    $property->setValue($this, $attributes[$propName]);
                }
            }
        } else {
            // Initialize properties based on order
            for ($i = 0; $i < count($properties); $i++) {
                if (isset($args[$i])) {
                    $propName = $properties[$i]->getName();
                    $properties[$i]->setAccessible(true);
                    $properties[$i]->setValue($this, $args[$i]);
                }
            }
        }
    }
}