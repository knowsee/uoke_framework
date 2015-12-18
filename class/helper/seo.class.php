<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * SEO Helper
 * 
 * @author knowsee
 * @copyright (c) 2015, Uoke
 * @example helper_file::title(模块)
 * @version 1.0
 */

class helper_seo {
    
    public static function title() {
        $getArgs = func_get_args();
        $_seo = array(
            'index' => '',
            'auth' => '',
            'show' => ''
        );
        $getArgs[0] = $_seo[$getArgs[0]];
        $title = call_user_func_array('sprintf', $getArgs);
        echo $title;
    }

    public static function keyword() {
        $getArgs = func_get_args();
        $_seo = array(
            'index' => '',
            'auth' => '',
            'show' => ''
        );
        $getArgs[0] = $_seo[$getArgs[0]];
        $title = call_user_func_array('sprintf', $getArgs);
        echo $title;
    }
    
    public static function description() {
        $getArgs = func_get_args();
        $_seo = array(
            'index' => '',
            'auth' => '',
            'show' => ''
        );
        $getArgs[0] = $_seo[$getArgs[0]];
        $title = call_user_func_array('sprintf', $getArgs);
        echo $title;
    }
}

?>
