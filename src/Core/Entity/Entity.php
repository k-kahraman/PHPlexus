<?php

namespace PHPlexus\Core\Entity;

abstract class Entity {
    private static array $classPropertiesCache = [];
    private static array $classHydrators = [];

    public function __construct(mixed ...$args) {
        $className = static::class;

        if (!isset(self::$classPropertiesCache[$className])) {
            // Retrieve all class properties using a scoped closure
            $getProperties = \Closure::bind(static function() use ($className): array {
                return array_keys(get_class_vars($className));
            }, null, $className);

            $props = $getProperties();
            // Filter out static or internal properties belonging to Entity
            $props = array_filter($props, static function(string $name): bool {
                return $name !== 'classPropertiesCache' && $name !== 'classHydrators';
            });
            self::$classPropertiesCache[$className] = array_values($props);

            // Create a fast, reflection-free hydrator closure
            self::$classHydrators[$className] = \Closure::bind(static function(object $object, array $properties, array $args): void {
                if (count($args) === 1 && is_array($args[0])) {
                    $attributes = $args[0];
                    foreach ($properties as $propName) {
                        if (array_key_exists($propName, $attributes)) {
                            $object->$propName = $attributes[$propName];
                        }
                    }
                } else {
                    $count = count($properties);
                    for ($i = 0; $i < $count; $i++) {
                        if (array_key_exists($i, $args)) {
                            $propName = $properties[$i];
                            $object->$propName = $args[$i];
                        }
                    }
                }
            }, null, $className);
        }

        self::$classHydrators[$className]($this, self::$classPropertiesCache[$className], $args);
    }
}