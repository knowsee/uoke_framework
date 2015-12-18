<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_baseMoney {

    const TABLE_NAME = 'moneystock';
    const TABLE_NAME_LOG = 'moneystock_log';
    const TABLE_NAME_CLIENT = 'moneystock_client';
    
    const TABLE_KEY = 'baseId';
    const TABLE_KEYNAME = 'baseName';
    const TABLE_BASEMONEY = 'baseMoney';
    const TABLE_RUNMONEY = 'runMoney';
    const TABLE_CLIENTMONEY = 'clientMoney';
    const TABLE_CREATETIME = 'createdTime';
    const TABLE_UPDATETIME = 'updatedTime';
    const TABLE_LOG_MSG = 'logMessage';

    public static function getMoneyArray() {
        $moneyArray = array(
            self::TABLE_BASEMONEY => '基金基础资金',
            self::TABLE_RUNMONEY => '基金运行资金',
            self::TABLE_CLIENTMONEY => '客户运行资金',
        );
        return $moneyArray;
    }

    public static function getList($start, $limit) {
        return self::getDB()->table(self::TABLE_NAME)->order(array(self::TABLE_UPDATETIME => 'DESC'))->limit(control_coreControl::makeLimit($start, $limit))->getList();
    }
    
    public static function getListByIds($ids) {
        return self::getDB()->table(self::TABLE_NAME)->where(array(
            self::TABLE_KEY => array('IN' => $ids)
        ))->getList(self::TABLE_KEY);
    }
    
    public static function getLogList($baseId, $start, $limit) {
        return self::getDB()->table(self::TABLE_NAME_LOG)->where(array(self::TABLE_KEY => $baseId))->order(array(self::TABLE_UPDATETIME => 'DESC'))->limit(control_coreControl::makeLimit($start, $limit))->getList();
    }

    public static function getBaseInfo($baseId) {
        return self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_KEY => $baseId))->getOne();
    }

    public static function makeBaseInfo($info) {
        $baseInfo = array(
            self::TABLE_KEYNAME => $info[self::TABLE_KEYNAME],
            self::TABLE_BASEMONEY => $info[self::TABLE_BASEMONEY],
            self::TABLE_RUNMONEY => $info[self::TABLE_RUNMONEY],
            self::TABLE_CLIENTMONEY => $info[self::TABLE_RUNMONEY],
            self::TABLE_CREATETIME => TIMESTAMP,
            self::TABLE_UPDATETIME => TIMESTAMP
        );

        $baseId = self::getDB()->table(self::TABLE_NAME)->insert($baseInfo, true);
        self::makeLog($info, $baseId, 'add');
        self::makeBaseClientInfo($info, $baseId);
        return $baseId;
    }
    
    public static function makeBaseClientInfo($info, $baseId) {
        $baseInfo = array(
            self::TABLE_KEY => $baseId,
            self::TABLE_BASEMONEY => $info[self::TABLE_BASEMONEY],
            self::TABLE_RUNMONEY => $info[self::TABLE_RUNMONEY],
            self::TABLE_CREATETIME => TIMESTAMP,
            self::TABLE_UPDATETIME => TIMESTAMP
        );
        self::getDB()->table(self::TABLE_NAME_CLIENT)->insert($baseInfo);
    }
    
    public static function editBaseMoneyInfo($moneyInfo, $baseId) {
        $baseInfo = array(
            self::TABLE_CLIENTMONEY => $moneyInfo[self::TABLE_CLIENTMONEY],
            self::TABLE_RUNMONEY => $moneyInfo[self::TABLE_RUNMONEY],
            self::TABLE_UPDATETIME => TIMESTAMP
        );
        self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_KEY => $baseId))->update($baseInfo);
        self::makeLog($moneyInfo, $baseId, 'update');
    }

    public static function editBaseInfo($info, $baseId) {

        $baseInfo = array(
            self::TABLE_KEYNAME => $info[self::TABLE_KEYNAME],
            self::TABLE_UPDATETIME => TIMESTAMP
        );

        self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_KEY => $baseId))->update($baseInfo);
        self::makeLog($info, $baseId, 'update');
        self::editBaseClientInfo($info, $baseId);
    }
    
    public static function editBaseClientInfo($info, $baseId) {
        $baseInfo = array(
            self::TABLE_BASEMONEY => $info[self::TABLE_BASEMONEY],
            self::TABLE_RUNMONEY => $info[self::TABLE_RUNMONEY],
            self::TABLE_UPDATETIME => TIMESTAMP
        );
        self::getDB()->table(self::TABLE_NAME_CLIENT)->where(array(self::TABLE_KEY => $baseId))->update($baseInfo);
    }

    public static function editCash($baseId, $money, $do) {
        $doSql = $do == 'add' ? '+' : '-';
        $baseInfo = array(
            self::TABLE_BASEMONEY => array($doSql, $money),
            self::TABLE_RUNMONEY => array($doSql, $money),
        );
        self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_KEY => $baseId))->update($baseInfo);
        self::makeLog(array(self::TABLE_BASEMONEY => $money,self::TABLE_RUNMONEY => $money),
                $baseId, $do == 'add' ? 'cash_in' : 'cash_out');
        self::editBaseClientInfo($baseInfo, $baseId);
    }

    private static function makeLog($info, $baseId, $type) {
        $baseInfo = self::getBaseInfo($baseId);
        $baseInfoLog = array(
            self::TABLE_KEY => $baseId,
            self::TABLE_BASEMONEY => $info[self::TABLE_BASEMONEY],
            self::TABLE_RUNMONEY => $baseInfo[self::TABLE_RUNMONEY],
            self::TABLE_UPDATETIME => TIMESTAMP,
            self::TABLE_LOG_MSG => self::getLogMsg($type)
        );

        self::getDB()->table(self::TABLE_NAME_LOG)->insert($baseInfoLog);
    }

    private static function getLogMsg($type) {
        $msg = array(
            'update' => '产品信息更新',
            'add' => '产品信息建立',
            'cash_in' => '产品资金注入',
            'cash_out' => '产品资金移出'
        );
        return $msg[$type];
    }

    private static function getDB() {
        return Factory_Db::getInstance();
    }

}
