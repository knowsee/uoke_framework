<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_userBase {

    const TABLE_NAME = 'userbasemoney';
    const TABLE_NAME_LOG = 'userbasemoney_log';
    
    const TABLE_KEY = 'baseId';
    const TABLE_USERID = 'userId';
    const TABLE_BASEMONEY = 'baseMoney';
    const TABLE_RUNMONEY = 'runMoney';
    const TABLE_REDSHARE = 'redShare';
    const TABLE_CREATETIME = 'createTime';
    const TABLE_UPDATETIME = 'updateTime';
    const TABLE_LOG_MSG = 'logMessage';
    
    public static function makeUserPlan($pushMoney, $baseId, $userId) {
        $baseInfo = array(
            self::TABLE_KEY => $baseId,
            self::TABLE_USERID => $userId,
            self::TABLE_BASEMONEY => $pushMoney,
            self::TABLE_RUNMONEY => $pushMoney,
            self::TABLE_UPDATETIME => TIMESTAMP,
            self::TABLE_CREATETIME => TIMESTAMP
        );
        self::getDB()->table(self::TABLE_NAME)->insert($baseInfo);
        self::makeLog($baseInfo, $baseId, $userId, 'add');
    }
    
    public static function pushMoneyByUserId($pushMoney, $baseId, $userId, $do) {
        
        $doSql = $do == 'add' ? '+' : '-';
        $baseInfo = array(
            self::TABLE_BASEMONEY => array($doSql, $pushMoney),
            self::TABLE_RUNMONEY => array($doSql, $pushMoney),
        );
        
        self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_USERID => $userId, self::TABLE_KEY => $baseId))->update($baseInfo);
        self::makeLog(array(self::TABLE_BASEMONEY => $pushMoney,self::TABLE_RUNMONEY => $pushMoney),
                $baseId, $do == 'add' ? 'cash_in' : 'cash_out');
    }
    
    public static function getUserPlanByUserId($baseId, $userId) {
        return self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_USERID => $userId, self::TABLE_KEY => $baseId))->getOne();
    }
    
    public static function getPlanUserHaveList($baseId) {
        return self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_KEY => $baseId))->getList();
    }

    public static function getUserPlanList($userId) {
        return self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_USERID => $userId))->getList();
    }
    
    public static function getUserPlanById($baseId, $userId) {
        return self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_KEY => $baseId, self::TABLE_USERID => $userId))->getOne();
    }
    
    private static function makeLog($info, $baseId, $userId, $type) {
        $baseUserInfo = self::getUserPlanById($baseId);
        $baseInfoLog = array(
            self::TABLE_KEY => $baseId,
            self::TABLE_USERID => $userId,
            self::TABLE_BASEMONEY => $info[self::TABLE_BASEMONEY],
            self::TABLE_RUNMONEY => $baseUserInfo[self::TABLE_RUNMONEY],
            self::TABLE_UPDATETIME => TIMESTAMP,
            self::TABLE_LOG_MSG => self::getLogMsg($type)
        );
        
        self::getDB()->table(self::TABLE_NAME_LOG)->insert($baseInfoLog);
    }
    
    private static function getLogMsg($type) {
        $msg = array(
            'update' => '更新了产品',
            'add' => '理财产品完成建仓',
            'cash_in' => '产品加仓操作',
            'cash_out' => '产品减仓操作'
        );
        return $msg[$type];
    }

    private static function getDB() {
        return Factory_Db::getInstance();
    }
}
