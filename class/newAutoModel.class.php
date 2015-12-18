<?php

define('IN_UOKE', TRUE);
define('RUNFIRSTTIME', array_sum(explode(' ', microtime())));
define('Uoke_ROOT', substr(dirname(__FILE__), 0, -5));
define('ICONV_ENABLE', function_exists('iconv'));
define('MB_ENABLE', function_exists('mb_convert_encoding'));
define('EXT_OBGZIP', function_exists('ob_gzhandler'));
define('TIMESTAMP', time());
define('GRPAPP', Uoke_ROOT . 'class/lib/');
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0 );
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    ini_set('magic_quotes_runtime', 0);
    define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
} else {
    define('MAGIC_QUOTES_GPC', false);
}
define('IS_CGI', (0 === strpos(PHP_SAPI, 'cgi') || false !== strpos(PHP_SAPI, 'fcgi')) ? 1 : 0 );
define('IS_CLI', PHP_SAPI == 'cli' ? 1 : 0);
require Uoke_ROOT . 'class/func/core.php';
class newAutoModel {
    public static function uoke() {
        self::coreRun();
        if (DEBUGSET == 1) {
            error_reporting(E_ERROR);
        } elseif (DEBUGSET == 2) {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
        ob_start();
        helper_log::runLog();
        app::run();
    }
    
    public static function uokeWithApi() {
        define('APIKEYCODE', CONFIG('apikey'));
        self::coreRun();
        if (DEBUGSET == 2) {
            error_reporting(E_ALL);
        } else {
            error_reporting(0);
        }
        ob_start();
        helper_log::runLog();
        app::runWithApi();
    }

    public static function autoload($class_name) {
        try {
            $namespace = explode('\\', $class_name);
            if (isset($namespace[1])) {
                require_cache(Uoke_ROOT . 'class/' . strtolower($namespace[0]) . '/' . strtolower($namespace[1]) . '.class.php');
            } else {
                $names = explode('_', $class_name);
                if (!isset($names[1]) && is_file(Uoke_ROOT . 'class/' . strtolower($class_name) . '.class.php')) {
                    require_cache(Uoke_ROOT . 'class/' . strtolower($class_name) . '.class.php');
                } elseif (is_file(Uoke_ROOT . 'class/' . strtolower($names[0]) . '/' . strtolower($names[1]) . '.class.php')) {
                    require_cache(Uoke_ROOT . 'class/' . strtolower($names[0]) . '/' . strtolower($names[1]) . '.class.php');
                }
            }
        } catch (Exception $e) {
            debug($e->getMessage(), $e->getCode(), array());
        }
    }
    
    private static function coreRun() {
        define('CHARSET', CONFIG('charset'));
        define('DEBUGSET', CONFIG('opendebug'));
        ini_set('date.timezone', 'Asia/Shanghai');
        header('Content-Type: text/html; charset=' . CHARSET);
        spl_autoload_register('newAutoModel::autoload');
        register_shutdown_function('newAutoModel::appSystemError');
        set_error_handler('newAutoModel::errorExcption');
        set_exception_handler('newAutoModel::errorExcption');
        self::cacheCore();
    }

    private static function cacheCore() {
        if (is_file(Uoke_ROOT . 'data/~runtimes.php')) {
            require_cache(Uoke_ROOT . 'data/~runtimes.php');
        } else {
            $php = '';
            $php .= compile(Uoke_ROOT . 'class/app.class.php');
            $php .= compile(Uoke_ROOT . 'class/model.class.php');
            $php .= compile(Uoke_ROOT . 'class/controlmain.class.php');
            $php .= compile(Uoke_ROOT . 'class/corelast.class.php');
            $php .= compile(Uoke_ROOT . 'class/helper/log.class.php');
            $php .= compile(Uoke_ROOT . 'class/withuoke.class.php');
            $php .= compile(Uoke_ROOT . 'class/qunkenexception.class.php');
            $fp = fopen(Uoke_ROOT . 'data/~runtimes.php', "w+");
            fputs($fp, '<?php //Cache Time: ' . date('Y-m-d', time()) . "\r\n" . $php);
            fclose($fp);
        }
    }

    public static function appSystemError() {
        if ($e = error_get_last()) {
            if (!in_array($e['type'], array(E_NOTICE, E_WARNING))) {
                $errorMessage = '['.$e['type'].'] '.$e['message'].' In '.$e['file'].'(Line '.$e['line'].')';
                debug($errorMessage, $e['type'], array('file' => $e['file'], 'line' => $e['line']));
            }
        }
        helper_log::saveLog();
    }

    public static function errorExcption($e, $errstr, $errfile, $errline) {
        if (is_object($e)) {
            debug($e->getMessage(), $e->getCode());
        } else {
            $errorStr = '';
            switch ($e) {
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_WARNING:
                case E_NOTICE:
                    break;
                case E_USER_ERROR:
                default:
                    $errorStr = "[$e] $errstr In " . $errfile . "(Line $errline)";
                    break;
            }
            if ($errorStr) {
                debug($errorStr, $e, '');
            }
        }
    }
}
