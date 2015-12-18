<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class helper_log {
    
    const IMPORTANT = '9999';
    const NOTICE = '999';
    const INFO = '99';
    
    public static $_logMessage = array();
    public static $_logWirteObj = array();
    
    public static function runLog() {
        self::$_logWirteObj = array('begin' => smallTime());
    }

    public static function writeLog($message, $type = 'php', $level = self::NOTICE) {
        self::$_logMessage[$type][TIMESTAMP][$level][] = $message;
    }
    
    public static function writeFirstLog($name, $message) {
        $fp = fopen(Uoke_ROOT . 'data/log/' . $name.'.txt', "w");
        fputs($fp, '=========' . date(TIMESTAMP) . '=============' . "\r\n" . $message. "\r\n\r\n");
        fclose($fp);
    }

    public static function writeOtherLogFile($name, $message) {
        $fp = fopen(Uoke_ROOT . 'data/log/' . $name.'.txt', "w");
        fputs($fp, $message);
        fclose($fp);
    }
    
    public static function saveLog() {
        self::$_logWirteObj['end'] = smallTime();
        self::$_logWirteObj['runtime'] = self::$_logWirteObj['end'] - self::$_logWirteObj['begin'];
        $fp = fopen(Uoke_ROOT . 'data/log/' . date('Y-m-d_H').'.txt', "a+");
        fputs($fp, '=========' . date(TIMESTAMP) . '=============' . "\r\n" . var_export(self::$_logWirteObj, TRUE). "\r\n". var_export(self::$_logMessage, TRUE) . "\r\n\r\n");
        fclose($fp);
    }
    
}
