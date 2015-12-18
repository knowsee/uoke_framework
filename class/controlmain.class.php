<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class controlMain {

    public static function add($data) {
        return self::getDB()->insert($data);
    }

    public static function editById($data, $id) {
        return self::getDB()->where(array(self::TABLE_KEY => $id))->update($data);
    }

    public static function editByWhere($data, $param) {
        return self::getDB()->where($param)->update($data);
    }

    public static function deleteById($id) {
        return self::getDB()->where(array(self::TABLE_KEY => $id))->delete();
    }

    public static function deleteByParam($param) {
        return self::getDB()->where($param)->delete();
    }

    public static function getById($id) {
        return self::getDB()->where(array(self::TABLE_KEY => $id))->getOne();
    }

    public static function getByParam($param) {
        return self::getDB()->where($param)->getOne();
    }

    public static function getListByParam($param, $order, $start, $limit) {
        return self::getDB()->where($param)->order($order)->limit(control_coreControl::makeLimit($start, $limit))->getList();
    }

    public static function getAllListByParam($param, $order) {
        return self::getDB()->where($param)->order($order)->getList();
    }

    /**
     * @param $field
     * @param string $countString(COUNT|SUM|AVG)
     * @return array
     */
    public static function countField($field, $countString = 'COUNT') {
        return self::getDB()->getFieldCount($field, $countString);
    }

    /**
     * @param $field
     * @param array $param
     * @param string $countString(COUNT|SUM|AVG)
     * @return array
     */
    public static function countFieldByParam($field, $param, $countString = 'COUNT') {
        return self::getDB()->where($param)->getFieldCount($field, $countString);
    }

    private static function getDB() {
        return Factory_Db::getInstance()->table(parent::TABLE_NAME);
    }

}