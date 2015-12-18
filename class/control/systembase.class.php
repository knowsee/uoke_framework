<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_systemBase {

    const TABLE_NAME = 'system_rundata';
    const TABLE_NAME_LOG = 'system_rundata_log';
    
    const TABLE_TODAYCASH = 'todayCash';
    const TABLE_TODAYGETCASH = 'todayGetcash';
    const TABLE_RUNCASH = 'runCash';
    const TABLE_RUNSTOCK = 'runStock';
    const TABLE_RUNNOSTOCK = 'runNoStock';
    const TABLE_RUNBANKMONEY = 'runBankMoney';
    const TABLE_UPDATETIME = 'updateTime';

    public static function getMoneyArray() {
        $moneyArray = array(
            self::TABLE_TODAYCASH => '今日实际资金',
            self::TABLE_TODAYGETCASH => '今日运行资金',
            self::TABLE_RUNBANKMONEY => '银行实际资金',
            self::TABLE_RUNCASH => '货币基金资金',
            self::TABLE_RUNNOSTOCK => '股票实际资金',
            self::TABLE_RUNSTOCK => '股票市值资金',
        );
        return $moneyArray;
    }
    
    public static function getSystemBase() {
        return self::getDB()->table(self::TABLE_NAME)->getOne();
    }

    public static function updateSystemBase($baseInfo) {
        $baseInfo[self::TABLE_TODAYCASH] = $baseInfo[self::TABLE_RUNBANKMONEY] + $baseInfo[self::TABLE_RUNCASH] + $baseInfo[self::TABLE_RUNNOSTOCK];
        $baseInfo[self::TABLE_TODAYGETCASH] = $baseInfo[self::TABLE_TODAYCASH] + $baseInfo[self::TABLE_RUNSTOCK] - $baseInfo[self::TABLE_RUNNOSTOCK];
        $baseInfo[self::TABLE_UPDATETIME] = TIMESTAMP;
        self::getDB()->table(self::TABLE_NAME)->update($baseInfo);
        self::makeLog($baseInfo);
    }
    
    public static function getLogList($start, $limit) {
        return self::getDB()->table(self::TABLE_NAME_LOG)->order(array(self::TABLE_UPDATETIME => 'DESC'))->limit(control_coreControl::makeLimit($start, $limit))->getList();
    }

    private static function makeLog($baseInfo) {
        self::getDB()->table(self::TABLE_NAME_LOG)->insert($baseInfo);
    }

    private static function getDB() {
        return Factory_Db::getInstance();
    }

}
