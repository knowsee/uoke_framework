<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_user {

    const TABLE_NAME = 'user';
    const TABLE_KEY = 'userId';
    const TABLE_UNIONKEY = 'userRealId';
    const TABLE_REALNAME = 'userRealName';
    const TABLE_EMAIL = 'userEmail';
    const TABLE_PASSWORD = 'userPassword';
    const TABLE_ONLYHASH = 'userHash';
    const TABLE_LOGINTIME = 'userLoginTime';
    const TABLE_REGTIME = 'userRegTime';
    const TABLE_UPDATETIME = 'userUpdateTime';

    public static function getUserFelidArray() {
        $userArray = array(
            self::TABLE_REALNAME => '真实姓名',
            self::TABLE_EMAIL => '邮箱地址',
            self::TABLE_PASSWORD => '客户密码',
        );
        return $userArray;
    }

    public static function regUser($userInfo) {
        if (!isemail($userInfo[self::TABLE_EMAIL])) {
            return control_returnCode::REG_ERROR_EMAIL;
        }
        $userHash = strtr(sha1(mt_rand(0, 99999)), 6, 6);
        $userData = array(
            self::TABLE_UNIONKEY => self::makeUserId(),
            self::TABLE_REALNAME => $userInfo[self::TABLE_REALNAME],
            self::TABLE_PASSWORD => self::passwordHash($userInfo[self::TABLE_PASSWORD], $userHash),
            self::TABLE_ONLYHASH => $userHash,
            self::TABLE_EMAIL => $userInfo[self::TABLE_EMAIL],
            self::TABLE_REGTIME => time(),
            self::TABLE_UPDATETIME => time()
        );
        self::getDB()->table(self::TABLE_NAME)->insert($userData);
        return control_returnCode::REG_TRUE;
    }

    public static function checkUser($keyName, $userPassword) {
        $userInfo = self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_UNIONKEY => $keyName))->getOne();
        if ($userInfo[self::TABLE_PASSWORD] !== self::passwordHash($userPassword, self::TABLE_ONLYHASH)) {
            return false;
        } else {
            return true;
        }
        return control_returnCode::REG_TRUE;
    }

    public static function editUser($keyName, $userInfo) {
        if (!isemail($userInfo[self::TABLE_EMAIL])) {
            return control_returnCode::REG_ERROR_EMAIL;
        }
        $userInfo[self::TABLE_UPDATETIME] = time();
        self::getDB()->table(self::TABLE_NAME)->where(array(self::TABLE_UNIONKEY => $keyName))->update(
                $userInfo
        );
        return control_returnCode::REG_TRUE;
    }

    public static function getUserInfo($keyName) {
        return self::getDB()->table(self::TABLE_NAME)
                        ->where(array(self::TABLE_UNIONKEY => $keyName))->getOne();
    }

    public static function getUserListByIds($ids) {
        list(,$list) = self::getDB()->table(self::TABLE_NAME)
                        ->where(array(self::TABLE_KEY => array('IN' => $ids)))
                        ->getList(self::TABLE_KEY);
        return $list;
    }

    public static function getList($start, $limit) {
        return self::getDB()->table(self::TABLE_NAME)
                        ->order(array(self::TABLE_REGTIME => 'DESC'))
                        ->limit(control_coreControl::makeLimit($start, $limit))->getList();
    }

    public static function getListByKeyword($keyword, $start, $limit) {
        return self::getDB()->table(self::TABLE_NAME)
                        ->whereOr(array(self::TABLE_UNIONKEY => array('LIKE' => $keyword), self::TABLE_REALNAME => array('LIKE' => $keyword)))
                        ->order(array(self::TABLE_REGTIME => 'DESC'))
                        ->limit(control_coreControl::makeLimit($start, $limit))
                        ->getList();
    }

    public static function getDB() {
        return Factory_Db::getInstance();
    }

    private static function passwordHash($password, $hash) {
        return sha1($password . md5($hash));
    }

    private static function makeUserId() {
        $rep = substr(md5(array_sum(explode(' ', microtime()))), 2, mt_rand(3, 6));
        $str = substr(sha1(time() . mt_rand(199999, 999999)), 2, mt_rand(7, 10));
        for ($i = 1; $i < 5; $i++) {
            $repKey = mt_rand($i, mt_rand($i, 6));
            if (isset($str[$repKey]) && isset($rep[$repKey])) {
                $str[$repKey] = $rep[$repKey];
            }
        }
        return $str;
    }

}
