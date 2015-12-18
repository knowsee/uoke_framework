<?php declare(strict_types = 1);
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class Factory_Cache {

    private static $cacheInstance = array();

    const CACHE_NAME_MEMCACHED = 1;
    const CACHE_NAME_APCU = 2;
    const CACHE_NAME_REDIS = 3;

    public static function getInstance(int $type) : object {
        if (isset(self::$cacheInstance[$type])) {
            return self::$cacheInstance[$type];
        }
        switch ($type) {
            case self::CACHE_NAME_MEMCACHED:
                self::$cacheInstance[self::CACHE_NAME_MEMCACHED] = new Cache_Memcached(CONFIG('cache/memcached'));
                break;
            case self::CACHE_NAME_APCU:
                self::$cacheInstance[self::CACHE_NAME_APCU] = new Cache_Apcu();
                break;
            case self::CACHE_NAME_REDIS:
                self::$cacheInstance[self::CACHE_NAME_REDIS] = new Cache_Redis(CONFIG('cache/redis'));
                break;
        }
        return self::$cacheInstance[$type];
    }

}
