<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class systemlang_lang {

    public static function phplang($var) {
        $_lang = array(
            'status_code_999' => '请登录后再次操作.',
            'status_code_9999' => '您的提交存在异常,请重试.',
            'status_code_2000' => '表单提交成功，已经完成数据更新',
            'status_code_2001' => '检索不到可供编辑的信息',
            'status_code_9000' => '用户数据更新完毕',
            'status_code_9001' => 'E-mail不正确',
            'status_code_200' => 'ok'
        );
        $getArgs = func_get_args();
        $getArgs[0] = $_lang[$var];
        $returnArgs = count($getArgs) > 1 ? call_user_func_array('sprintf', $getArgs) : $_lang[$var];
        return $returnArgs;
    }

}
