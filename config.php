<?php

/*
 * 本文件是Uoke 框架的基础配置
 * $_config['cache']['memory'] 是内存缓存时执行的缓存类型 可填写: Memcache, Eaccelerator 也可以自定义
 * 自定义规则(括弧内的内容自定义): class/cache/ 下建立: (Memcache).class.php 区分大小写, 请谨慎!
 */

$_config['1']['host'] = 'mysys.acghx.net';
$_config['1']['dbuser'] = 'root';
$_config['1']['password'] = 'chengyi94';
$_config['1']['dbname'] = 'ap';
$_config['1']['dbcharset'] = 'utf8';
$_config['1']['db_pre'] = '';
$_config['1']['mysqltype'] = 'db_mysqli';
$_config['1']['mysqlpconnect'] = '1';

$_config['static']['resurl'] = 'http://127.0.0.1/moneyNew/static/';

$_config['sms']['send_url'] = 'http://sms-api.luosimao.com/v1/send.json';
$_config['sms']['send_key'] = '';
$_config['sms']['template']['regtext'] = '欢迎你注册, 请在15分钟之内完成, 验证码: %s 【公司名称】';


$_config['cache']['memory']['type'] = '';
$_config['cache']['memory']['config'] = array('server' => '127.0.0.1', 'port' => 11211,'time' => '30');
$_config['cache']['file']['dir'] = 'cache/file/';
$_config['cache']['file']['time'] = '30';

$_config['siteurl'] = 'http://127.0.0.1/moneyNew/';
$_config['safekey'] = '1d02312345eeeCVvG7';
$_config['charset'] = 'utf-8';
/**
 * DEBUG开关: 1: 一般性错误信息展示 2: 详细性错误信息展示 0: 日志性错误信息记录
 */
$_config['opendebug'] = '1';
$_config['cookiepre'] = 'Q22F_';
$_config['cookietime'] = '3600';
$_config['cookiepath'] = '/';
$_config['cookiedomain'] = '';
$_config['apikey'] = '123212';
