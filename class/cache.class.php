<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class cache {

    private $cache_config = array();
    private $cacheObj;
    private $cacheset = FALSE;
    private $pre = '';

    public function __construct() {
        $this->cache_config = CONFIG('cache/memory');
        if ($this->cache_config['type']) {
            $cache_name = strtolower('cache_' . $this->cache_config['type']);
            $this->cacheObj = $cache_name::getInstance();
            $this->cacheset = $this->cacheObj->check();
            if (!$this->cacheset) {
                debug("Can't not use this cache :" . $cache_name, '9999');
            }
        }
        $this->pre = SA . '_';
        return $this;
    }

    public function __call($modelName, $argArray) {
        if (method_exists($this->cacheObj, $modelName)) {
            return call_user_func_array(array($this->cacheObj, $modelName), $argArray);
        } else {
            debug("Can't not use this modelname :" . $modelName, '9999');
            return false;
        }
    }

    public function getInfo() {
        return '采用: ' . $this->cache_config['type'] . '缓存引擎.';
    }

    public function isGlobal() {
        $this->pre = '';
        return $this;
    }

    public function set($key, $value, $ttl = 3600) {
        if ($this->cacheset) {
            return $this->cacheObj->set($this->pre . $key, $value, $ttl);
        }
    }

    public function get($key) {
        if ($this->cacheset) {
            return $this->cacheObj->get($this->pre . $key);
        }
    }

    public function rm($key) {
        if ($this->cacheset) {
            return $this->cacheObj->rm($this->pre . $key);
        }
    }

    public function clear() {
        if ($this->cacheset) {
            return $this->cacheObj->clean();
        }
    }

}

?>
