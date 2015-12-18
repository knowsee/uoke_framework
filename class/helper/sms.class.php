<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * 短信接口助手类
 * 
 * @author knowsee
 * @copyright (c) 2015, Uoke
 * @example helper_sms::$function
 * @version 1.0
 */

class helper_sms {
    
    public static function send($phone, $message) {
        if(!self::checkPhoneNumber($phone)){
            return false;
        }
        $config = CONFIG('sms');
        $sms = new ext_smsluosimao($config);
        try {
            $sms->send($phone, $message);
            return true;
        } catch (sms_Exception $ex) {
            log::writeLog($ex->message(), 'sms');
            return false;
        }
    }
    
    public static function mobileCheckCode($phone) {
        $code = mt_rand(10000, 99999);
        $lastCode = app::d()->getOne('mobile_code', array('mobile' => $phone, 'time' => '>='.(TIMESTAMP-15*60)));
        if($lastCode['count'] > 2) {
            return control_returnCode::SMS_CHECK_CODE_TOOMORE;
        } else {
            $code = $lastCode['code'];
        }
        $message = sprintf(CONFIG('sms/template/regtext'), $code);
        $return = self::send($phone, $message);
        if($return && !$lastCode) {
            app::d()->insert('mobile_code', array('mobile' => $phone, 'count' => '1', 'code' => $code, 'time' => TIMESTAMP));
        } elseif($return) {
            app::d()->update('mobile_code', array('count' => '+1'), array('mobile' => $phone, 'code' => $lastCode['code']));
        } else {
            return $return == false ? control_returnCode::SMS_CHECK_PHONEERROR : control_returnCode::SMS_CHECK_CODE_NOTURE;
        }
        return control_returnCode::SMS_CHECK_TRUE;
    }
    
    public static function checkCode($phone, $code) {
        $status = app::d()->getOne('mobile_code', array('mobile' => $phone, 'code' => $code,'time' => '>='.(TIMESTAMP-15*60)));
        if($status) {
            return true;
        } else {
            return false;
        }
    }


    public static function checkPhoneNumber($number) {
        if(!isphone($number)) {
            return false;
        }
        return true;
    }
}


class sms_Exception extends Exception {
    
    protected $smsmessage = array(
        '-10' => '验证信息失败',
        '-20' => '短信余额不足',
        '-30' => '短信内容为空',
        '-32' => '短信内容缺少签名信息',
        '-40' => '错误的手机号',
        '-41' => '号码在黑名单中',
        '-42' => '验证码类短信发送频率过快',
        '-50' => '请求发送IP不在白名单内',
    );
    
    public function message() {
        return $this->smsmessage[$this->getCode()];
    }
}
