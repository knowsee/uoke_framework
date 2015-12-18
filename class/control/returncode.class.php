<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class control_returnCode {
    
    const ALWAYS_TRUE = 200;
    const FORM_SUBMIT_TRUE = 2000;
    const FORM_INFO_EMPTY = 2001;
    
    const AJAX_GET_NOMODEL = 5000;
    
    const PROJECT_MONEY_FAIL = 6000;
    const PROJECT_MAKE_TRUE = 6009;
    
    const REG_ERROR_EMAIL = 9001;
    const REG_ERROR_OTHER = 9002;
    const REG_ERROR_NOTFOUND = 9009;
    const REG_TRUE = 9000;
}
