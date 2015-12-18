<?php declare(strict_types = 1);
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
/**
 * 数据库处理工厂
 * @author Knowsee
 */
class Factory_Db {

    private static $instance;
    private static $runSql = array();
    private static $debugSql = array();

    /**
     * 
     * @param type $dbType
     * @return db_AdapterMysqli
     */
    public static function getInstance(string $dbType = 'mysqli') {
        if (is_null(self::$instance)) {
            if ($dbType == 'mysqli') {
                self::$instance = new db_AdapterMysqli(CONFIG('1'));
            }
        }
        return self::$instance;
    }

    public static function getDebug() {
        var_export(self::$debugSql);
        var_export(self::$runSql);
    }

    public static function getSqlList() {
        return self::$runSql;
    }

    public static function saveRunSql(string $sql) {
        self::$runSql[] = $sql;
    }

    public static function countSqlNum() {
        return count(self::$runSql);
    }

    public static function setDebugSql(array $info) {
        self::$debugSql[] = $info;
    }

}
