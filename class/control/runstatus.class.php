<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_runStatus {

    const TABLE_NAME = 'system_rundata';
    
    const TABLE_KEY = 'todayCash';
    const TABLE_KEYNAME = 'todayGetcash';
    const TABLE_BASEMONEY = 'runCash';
    const TABLE_RUNMONEY = 'runStock';
    const TABLE_CREATETIME = 'runNoStock';
    const TABLE_UPDATETIME = 'runBankMoney';
    
    
    public static function editBaseInfo($info) {
        
        $baseInfo = array(
            self::TABLE_KEYNAME => $info['basename'],
            self::TABLE_BASEMONEY => $info['basemoney'],
            self::TABLE_RUNMONEY => $info['runMoney'],
            self::TABLE_UPDATETIME => TIMESTAMP
        );
        
        self::getDB()->table(self::TABLE_NAME)->update($baseInfo);
    }
    

    private static function getDB() {
        return Factory_Db::getInstance();
    }
}
