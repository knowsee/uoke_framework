<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_coreControl {

    public static function makeLimit($start, $limit) {
        return array(($start-1)*$limit, $limit);
    }
    
    public static function isPageNext($total, $pageNum, $onpage) {
        if(ceil($total/$pageNum) <= $onpage) {
            return false;
        } else {
            return true;
        }
    }

}
