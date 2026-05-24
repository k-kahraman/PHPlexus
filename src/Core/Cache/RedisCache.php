<?php

namespace PHPlexus\Core\Cache;

use Redis;

class RedisCache implements CacheInterface {
    private Redis $redis;

    public function __construct(?Redis $redis = null, string $host = '127.0.0.1', int $port = 6379, string $password = '') {
        if ($redis !== null) {
            $this->redis = $redis;
        } else {
            $this->redis = new Redis();
            $this->redis->connect($host, $port);
            if ($password !== '') {
                $this->redis->auth($password);
            }
        }
    }

    public function get(string $key): mixed {
        $value = $this->redis->get($key);
        if ($value === false) {
            return null;
        }
        $decoded = json_decode($value, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool {
        $serialized = is_string($value) ? $value : json_encode($value);
        if ($serialized === false) {
            return false;
        }
        return $this->redis->setex($key, $ttl, $serialized);
    }

    public function delete(string $key): bool {
        $result = $this->redis->del($key);
        return $result > 0;
    }

    public function clear(): bool {
        return $this->redis->flushDb();
    }
}
