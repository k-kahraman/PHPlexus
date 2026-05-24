<?php

namespace PHPlexus\Core\Entity;

abstract class Record extends Entity {
    private static array $recordHydrators = [];

    public function with(array $changes): self {
        $new = clone $this;
        $className = static::class;

        if (!isset(self::$recordHydrators[$className])) {
            self::$recordHydrators[$className] = \Closure::bind(static function(object $object, array $changes): void {
                foreach ($changes as $key => $value) {
                    if (property_exists($object, $key)) {
                        $object->$key = $value;
                    }
                }
            }, null, $className);
        }

        self::$recordHydrators[$className]($new, $changes);
        return $new;
    }
}