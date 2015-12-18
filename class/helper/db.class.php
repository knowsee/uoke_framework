<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
/**
 * 数据库的helper类
 * @author Knowsee
 */
class helper_db {
    
    public static function field($field) {
        return '`'.implode('`,`', $field).'`';
    }

    public static function fieldType($field, $queryType) {
        switch ($queryType) {
            case 'count':
                $type = 'COUNT';
                break;
            case 'sum':
                $type = 'SUM';
                break;
            case 'avg':
                $type = 'AVG';
                break;
            default :
                $type = '';
                break;
        }
        return $type ? sprintf('%s(' . $field . ')', $type) : $field;
    }

    public static function handlesql($array) {
        //array('userName' => array('>=' => '111', '<=' => '2'))
        //array('userName' => '')
        foreach ($array as $feild => $value) {
            if (is_array($value)) {
                foreach ($value as $handle => $val) {
                    $sql[] = self::condSql($feild, $val, $handle);
                }
            } else {
                $sql[] = self::condSql($feild, $value, '');
            }
        }
        return $sql;
    }

    public static function condSql($key, $value, $handle) {
        if (in_array($handle, array('>', '<', '>=', '<=', '!=', '<>'))) {
            $sql = "`$key` " . $handle . " '$value'";
        } elseif ($handle == 'IN') {
            $sql = "`$key` IN(".  dimplode($value).")";
        } elseif ($handle == 'LIKE') {
            $sql = "`$key` LIKE '%$value%'";
        } else {
            $sql = "`$key` = '$value'";
        }
        return $sql;
    }
    
    public static function arrayToSql($array, $glue = ',') {
        $sql = $comma = '';
        foreach ($array as $k => $v) {
            $k = trim($k);
            if(is_array($v)) {
                $sql[] = "`$k`= `$k`$v[0]'$v[1]'";
            } else {
                $sql[] = "`$k`='$v'";
            }
        }
        return implode($glue, $sql);
    }
    
}
