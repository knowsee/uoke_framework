<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
/**
 * Get Post Ajax 等输入特性处理类
 *
 * @author chengyi
 */
class withUoke {

    private $varpost = array();
    public $error_msg = '';

    public function __construct() {
        
    }

    /*
     *  外部数据提交优化验证逻辑
     *
     *  @return array
     */

    public function webSetInit($do, $arrays, $var1, $var2, $var3) {
        $getArray = explode('|', $do);
        $return = $amergeArray = $tempArray = array();
        foreach ($getArray as $key => $value) {
            if (in_array($value, array('get', 'post'))) {
                if ($value == 'get') {
                    $newValue = $_GET;
                } else {
                    $newValue = $_POST;
                }
                $amergeArray = array_merge($amergeArray, $newValue);
            } elseif ($value == 'cookies') {
                return $this->cookies($arrays, $var1, $var2, $var3);
            } elseif ($value == 'file') {
                $tempArray = $_FILES;
            } elseif ($value == 'checkallow') {
                return $this->checkallow($arrays, $var1, $var2, $var3);
            } elseif ($value == 'ajax') {
                if (parent::$var['ajax'] == TRUE) {
                    $amergeArray = array_merge($amergeArray, $_REQUEST);
                }
            } elseif ($value == 'decookies') {
                $this->decookies($arrays);
            }
        }
        if ($amergeArray) {
            $return = $this->clean($amergeArray, $var1);
        }
        $this->varpost = array_merge_recursive($return, $tempArray);
        return $this->varpost;
    }

    public function cookies($var, $value, $life = '86400', $check = 'dstripslashes') {
        if (is_array($var)) {
            foreach ($var as $key => $values) {
                $arrays[$values] = $this->cookies($values, '', $life);
            }
            return $arrays;
        } else {
            if ($value) {
                return $this->_cookie($var, $value, $life, $check);
            } else {
                return $this->_cookie($var, $value, $life, $check);
            }
        }
    }

    public function decookies($name) {
        $config = array(
            'prefix' => CONFIG('cookiepre') . substr(md5(CONFIG('cookiepath') . '|' . CONFIG('cookiedomain')), 0, 4) . '_',
            'expire' => CONFIG('cookietime'),
            'path' => CONFIG('cookiepath'),
            'domain' => CONFIG('cookiedomain'),
        );
        if (is_array($name)) {
            foreach ($name as $key => $values) {
                $this->decookies($values);
            }
        } else {
            $var = $config['prefix'] . $name;
            setcookie($var, NULL, time() - 3600, $config['path'], $config['domain'], false, false);
        }
    }

    private function clean($values, $check = '') {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                if (!is_array($value)) {
                    if (is_array($check)) {
                        foreach ($check as $k => $v) {
                            $checkname = 'd' . $v;
                            $newstring[addslashes($key)] = $checkname($value);
                        }
                    } elseif ($check) {
                        $checkname = 'd' . $check;
                        $newstring[addslashes($key)] = $checkname($value);
                    }
                } else {
                    $newstring[addslashes($key)] = $this->clean($value, $check);
                }
            }
        } else {
            if (is_array($check)) {
                foreach ($check as $k => $v) {
                    $checkname = 'd' . $v;
                    $newstring = $checkname($values);
                }
            } else {
                $checkname = 'd' . $check;
                $newstring = $checkname($values);
            }
        }
        return $newstring;
    }

    private function _get($var = '') {
        return $var ? $_GET[$var] : $_GET;
    }

    private function _post($var = '') {
        return $var ? $_POST[$var] : $_POST;
    }

    private function _ajax($var = '') {
        if (parent::$var['ajax'] == TRUE) {
            return $var ? $_REQUEST[$var] : $_REQUEST;
        }
    }

    private function _file($var = '') {
        return $var ? $_FILES[$var] : $_FILES;
    }

    private function _cookie($name, $value = '', $life = '', $check = '', $other = array('httponly' => FALSE)) {
        $check = !$check ? 'htmlspecialchars' : $check;
        $config = array(
            'prefix' => CONFIG('cookiepre') . substr(md5(CONFIG('cookiepath') . '|' . CONFIG('cookiedomain')), 0, 4) . '_',
            'expire' => CONFIG('cookietime'),
            'path' => CONFIG('cookiepath'),
            'domain' => CONFIG('cookiedomain'),
        );
        if ($name && $value) {
            $var = $config['prefix'] . $name;
            $_COOKIE[$var] = $value;
            if ($value == '' || $life < 0) {
                $value = '';
                $life = -1;
            }
            if (is_null($life)) {
                $life = NULL;
            } else {
                $life = $life > 0 ? TIMESTAMP + $life : ($life < 0 ? TIMESTAMP - 31536000 : 0);
            }
            $path = $other['httponly'] && PHP_VERSION < '5.2.0' ? $config['path'] . '; HttpOnly' : $config['path'];
            $secure = $_SERVER['SERVER_PORT'] == 443 ? 1 : 0;
            return setcookie($var, $value, $life, $path, $config['domain'], $secure, $other['httponly']);
        } elseif ($name && !is_null($value)) {
            $prelength = strlen($config['prefix']);
            foreach ($_COOKIE as $key => $val) {
                if (substr($key, 0, $prelength) == $config['prefix']) {
                    $val = $check($val);
                    $cookies[substr($key, $prelength)] = $val;
                }
            }
            return isset($cookies[$name]) ? $cookies[$name] : NULL;
        }
    }

    public function checkallow($submit = '', $allowget = FALSE) {
        if (!$allowget) {
            if (($_SERVER['REQUEST_METHOD'] == 'POST' && $this->varpost['token'] == QHASH && empty($_SERVER['HTTP_X_FLASH_VERSION']) && (empty($_SERVER['HTTP_REFERER']) || preg_replace("/https?:\/\/([^\:\/]+).*/i", "\\1", $_SERVER['HTTP_REFERER']) == preg_replace("/([^\:]+).*/", "\\1", $_SERVER['HTTP_HOST'])))) {
                return true;
            } else {
                return false;
            }
        } elseif ($allowget) {
            if ($this->varpost['token'] == QHASH || $this->varpost['token'] == QHASH) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

}

?>
