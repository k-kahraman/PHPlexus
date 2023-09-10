<?php

namespace PHPlexus\Core\Entity;

abstract class Record extends Entity
{
    public function with(array $changes): self
    {
        $new = clone $this;

        $reflectionClass = new \ReflectionClass($this);

        foreach ($changes as $key => $value) {
            if ($reflectionClass->hasProperty($key)) {
                $property = $reflectionClass->getProperty($key);
                $property->setAccessible(true); // Make the private property accessible
                $property->setValue($new, $value); // Set the value of the property on the new cloned object
            }
        }

        return $new;
    }

}