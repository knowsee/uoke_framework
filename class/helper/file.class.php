<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * 文件操作助手类
 * 
 * @author knowsee
 * @copyright (c) 2013, Uoke
 * @example helper_file::$function
 * @version 1.0
 */

class helper_file {
    
    /*
     * 写入文件
     * 
     * @access public
     * @example helper_file::writefile('example', 'exa.html', 'data/temp/', array('append' => TRUE,'read' => TRUE));
     * Array[append] 为文件写入是否需要以追加的形式写入， TRUE为是，FALSE为否，默认为是。
     * Array[read] 为是否在写入结束后读取文件， TRUE为是，FALSE为否，默认为否。
     * @param string $string 写入的字符串
     * @param string $filename 写入的文件名
     * @param string $filedir 写入的文件所在文件夹
     * @param array $dosetting 缓存参数
     * @return boolen or string
     */
    
    public static function writefile($string, $filename, $filedir, $dosetting = array()) {
        $setting = array(
            'append' => is_bool($dosetting['append']) ? $dosetting['append'] : TRUE,
            'read' => is_bool($dosetting['read']) ? $dosetting['read'] : FALSE,
        );
        if(!self::checkdir(Uoke_ROOT.$filedir)) {
            if(!self::makedir(Uoke_ROOT.$filedir)) {
                return FALSE;
            }
        }
        $filefullpath = Uoke_ROOT.$filedir . '/' . $filename;
        $filereturn = file_put_contents($filefullpath, $string, $setting['append'] ? FILE_APPEND : '');
        if ($filereturn) {
            if ($setting['read']) {
                return file_get_contents($filefullpath);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 检查目录合法性
     * 
     * @access public
     * @example helper_file::checkdir('data/temp/');
     * @param string $dir 目录名
     * @return boolen
     */
    
    public static function checkdir($dir) {
        if (is_dir($dir)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 检查文件合法性
     * 
     * @access public
     * @example helper_file::checkfile('example.txt');
     * @param string $file 文件名（含目录）
     * @return boolen
     */
    
    public static function checkfile($file) {
        if (is_file($file)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 建立目录
     * 
     * @access public
     * @example helper_file::makedir('data/temp/');
     * @param string $dir 目录名
     * @return boolen
     */
    
    public static function makedir($dir) {
        if (!mkdir(Uoke_ROOT.$dir, 0755)) {
            return FALSE;
        } else {
            touch(Uoke_ROOT.$dir . '/index.html');
            return TRUE;
        }
    }

}

?>
