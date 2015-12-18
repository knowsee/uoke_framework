<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class corelast extends app {

    public function coreLastRun() {
        $this->handleUserInfoWithWeb();
        $this->handleWebLast();
    }

    public function coreLastRunWithApi() {
        
    }

    private function handleUserInfoWithWeb() {
        $user_cookies = parent::v('cookies', array('password'));
        if ($user_cookies['password']) {
            
        } else {
            $this->loginIn();
        }
    }

    private function loginIn() {
        parent::$var['userid'] = '0';
        parent::$var['username'] = '游客';
        parent::$var['member'] = array();
    }

    private function handleWebLast() {
        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || $_REQUEST['ajax'] == '1') {
            parent::$var['ajax'] = TRUE;
        } else {
            parent::$var['ajax'] = FALSE;
        }
    }

}

?>