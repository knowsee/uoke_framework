<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * 发送短信_luosimao的接口
 *
 * @author chengyi
 */
class ext_smsluosimao {

    private $config = array();

    public function __construct($config) {
        $this->config = $config;
        return $this;
    }

    public function send($phone, $message) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->config['send_url']);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-'.$this->config['send_key']);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $phone, 'message' => $message));
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);
        if($res['error'] !== 0) {
            throw new sms_Exception($res['msg'], $res['error']);
        } else {
            return true;
        }
    }

}