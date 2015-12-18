<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * HTTP/链接 操作类
 *
 * @author knowsee
 */
class helper_http {

    public static function geturlinfo($url) {
        $file = '';
        preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/', $url, $file);
        return array('filename' => $file[1], 'ext' => self::fileext($file[1]));
    }

    public static function fileext($filename) {
        return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
    }
    
    /*
     * Content-Length
     */
    public static function geturl($url, $type = '') {
        $url = get_headers($url, true);
        if (preg_match('/200/', $url[0])) {
            return $url;
        } else {
            return $url;
        }
    }
    
    public static function repurl($url) {
        preg_match_all("/(?:\{)(.*)(?:\})/i",$url, $result);
        foreach($result[1] as $val) {
            $urlRepArray[] = Q($val);
            $urlArray[] = '{'.$val.'}';
        }
        return str_replace($urlArray, $urlRepArray, $url);
    }

}

?>
