<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class app {

    public static $var = array();
    public static $obj = array();

    public static function run() {
        self::$var = array(
            'timestamp' => TIMESTAMP,
            'starttime' => RUNFIRSTTIME,
            'clientip' => self::_get_client_ip(),
            'referer' => '',
            'charset' => CHARSET,
            'gzip_open' => '',
            'safekey' => CONFIG('safekey'),
            'timenow' => todate(TIMESTAMP),
            'PHP_SELF' => htmlspecialchars(self::_get_script_url()),
            'static_url' => CONFIG('static/url'),
            'cookies' => array()
        );
        self::$var['basescript'] = SA;
        self::$var['basefilename'] = basename(self::$var['PHP_SELF']);
        self::$var['siteurl'] = CONFIG('siteurl');
        self::$var['query_string'] = $_SERVER['QUERY_STRING'];
        define('SAFEKEY', self::$var['safekey']);
        define('QHASH', self::acthash());
        define('STATIC_URL', CONFIG('static/resurl'));
        self::runModel();
    }
    
    public static function runWithApi() {
        self::$var = array(
            'starttime' => RUNFIRSTTIME,
            'clientip' => self::_get_client_ip(),
            'PHP_SELF' => htmlspecialchars(self::_get_script_url())
        );
        define('SAFEKEY', self::$var['safekey']);
        define('QHASH', self::acthash());
        define('STATIC_URL', CONFIG('static/resurl'));
        self::runModel();
        $coreLast = returnClass('corelast');
        $coreLast->coreLastRunWithApi();
        $className = 'Api_' . SA;
        $model = returnClass($className);
        $model->getInput = self::v('get|post', '*', array('addslashes', 'htmlspecialchars'));
        $model->coreModel();
    }

    public static function runModel() {
        $coreLast = returnClass('corelast');
        $coreLast->coreLastRun();
        $className = 'Model_' . SA;
        $model = returnClass($className);
        $model->getInput = self::v('get|post', '*', array('addslashes', 'htmlspecialchars'));
        $model->coreModel();
    }

    public static function sget($value) {
        $name = explode('/', $value);
        switch (count($name)) {
            case 1:
                return self::$var[$name[0]];
            case 2:
                return self::$var[$name[0]][$name[1]];
            case 3:
                return self::$var[$name[0]][$name[1]][$name[2]];
            case 4:
                return self::$var[$name[0]][$name[1]][$name[2]][$name[3]];
            default :
                return self::$var;
        }
    }

    public static function cache() {
        return returnClass('cache');
    }

    public static function acthash($specialadd = '', $len = '8') {
        return substr(md5(substr(TIMESTAMP, 0, -7) . self::$var['uid'] . self::$var['safekey'] . $specialadd), 8, $len);
    }

    public static function gpset($varset = '') {
        $var = self::v('get|post|file', '*', array('addslashes', 'htmlspecialchars'));
        if ($varset) {
            return $var[$varset];
        } else {
            return $var;
        }
    }

    public static function v($do, $array = '', $var1 = '', $var2 = '', $var3 = '') {
        $withUoke = returnClass('withUoke');
        $arrays = $array == '*' ? '' : $array;
        try {
            $return = $withUoke->webSetInit($do, $arrays, $var1, $var2, $var3);
        } catch (Exception $exc) {
            debug($exc->getMessage(), $exc->getCode(), array());
        }
        return $return;
    }
    

    public static function _get_script_url() {
        if (!isset(self::$var['PHP_SELF'])) {
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            if (basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                self::$var['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
            } else if (basename($_SERVER['PHP_SELF']) === $scriptName) {
                self::$var['PHP_SELF'] = $_SERVER['PHP_SELF'];
            } else if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                self::$var['PHP_SELF'] = $_SERVER['ORIG_SCRIPT_NAME'];
            } else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
                self::$var['PHP_SELF'] = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            } else if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0) {
                self::$var['PHP_SELF'] = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            } else {
                debug('request_tainting');
            }
        }
        return self::$var['PHP_SELF'];
    }

    public static function _get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }

    public static function getmorebug() {
        if (!self::$var['ajax']) {
            $cache = self::cache();
            $debuginfo = array('time' => number_format((dmicrotime() - self::$var['starttime']), 6) . 's', 'queries' => Factory_Db::countSqlNum(), 'memory' => intval(return_bytes(memory_get_usage() / (1024 * 1024) . 'k')) . "KB");
            $includes = get_included_files();
            $debug = '<style>.debug p {padding:0;margin:0;line-height:25px;}</style>';
            $debug .= '<div class="debug" style="width: 80%; margin: 0 auto; padding: 10px; background: #CFF; border: 2px #DDE solid;">';
            $debug .= '<p><strong>系统处理信息:</strong></p>';
            $debug .= '<p>效率:' . $debuginfo['time'] . ',  查询数据库: ' . $debuginfo['queries'] . ',  使用了' . $debuginfo['memory'] . '</p>';
            $debug .= '<p><strong>缓存系统:</strong></p>';
            $debug .= '<p>' . $cache->getInfo() . '</p>';
            if (!self::$var['otherconfig']['frameonly']) {
                $debug .= '<p><strong>数据库执行:</strong></p>';
                foreach (Factory_Db::getSqlList() as $key => $sqlstr) {
                    $debug .= '<p style="border: 1px #aaa solid; padding: 3px; margin-bottom: 2px;">' . $sqlstr . '</p>';
                }

                $debug .= '<p><strong>加载文件:</strong></p>';
                foreach ($includes as $fn) {
                    $debug .= '<p style="border: 1px #aaa solid; padding: 3px; margin-bottom: 2px;">' . $fn . '</p>';
                }
            }
            $debug .= '</div>';
            echo $debug;
        } else {
            if (!self::$var['otherconfig']['frameonly']) {
                $debug = '<p><strong>数据库执行:</strong></p>';
                foreach (Factory_Db::getSqlList() as $key => $value) {
                    $debug .= '<p style="border: 1px #aaa solid; padding: 3px; margin-bottom: 2px;">' . $value . '</p>';
                }
            }
            echo $debug;
        }
    }

    public static function easybug() {
        self::$var['bug']['runsql'] = 0;
        if (!self::$var['ajax'] && function_exists('xdebug_time_index')) {
            self::$var['bug']['runsec'] = function_exists('xdebug_time_index') ? number_format(xdebug_time_index(), 6) : number_format((dmicrotime() - RUNFIRSTTIME), 6);
            $html = '<p>' . self::$var['bug']['runsec'] . 's, runsize: ' . xdebug_memory_usage() . 'Kb, runavgsize: ' . xdebug_peak_memory_usage() . 'Kb, runsql:' . var_export(self::$var['bug']['runsql'], true) . '</p>';
            echo $html;
        } else {
            self::$var['bug']['runsec'] = number_format((dmicrotime() - self::$var['starttime']), 6);
            self::$var['bug']['runmemory'] = intval(return_bytes(memory_get_usage() / (1024 * 1024) . 'k'));
            self::$var['bug']['runsql'] = Factory_Db::countSqlNum();
        }
    }

}
