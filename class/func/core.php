<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * 核心函数库
 * @author chengyi 
 */
function Q($readname) {
    return app::sget($readname);
}

function CONFIG($name) {
    static $config = array();
    if (!$config) {
        require Uoke_ROOT . 'config.php';
        $config = $_config;
    }
    return getArrayTree($name, $config);
}

function ACTIVE($checkName, $inputName, $class = 'active') {
    if($checkName == $inputName) {
        echo $class;
    }
}

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    $ckey_length = 4;
    $authkey = SAFEKEY;
    $key = md5($key != '' ? $key : $authkey);
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);

    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if ($operation == 'DECODE') {
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

function think_filter(&$value) {
    if (preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i', $value)) {
        $value .= ' ';
    }
}

function debug($errorMessage, $errorCode, $errorOther) {
    new qunkenException($errorMessage, $errorCode, $errorOther);
}

function showmsg($error, $data = array(), $url = '') {
    $url = !$url ? getenv("HTTP_REFERER") : $url;
    if (Q('ajax') == TRUE) {
        echo json_encode(array('status' => $error, 'data' => $data, 'msg' => systemlang_lang::phplang('status_code_'.$error)));
    } else {
        $message = systemlang_lang::phplang('status_code_'.$error);
        if(defined('ADMIN_MODEL') && ADMIN_MODEL == true) {
            require getTemplate('admin/showmessage');
        } else {
            require getTemplate('showmessage');
        }
    }
    exit;
}

function getArrayTree($treeArray, $Array) {
    $k = explode('/', $treeArray);
    switch (count($k)) {
        case 1:
            return $Array[$k[0]];
        case 2:
            return isset($Array[$k[0]][$k[1]]) ? $Array[$k[0]][$k[1]] : '';
        case 3:
            return $Array[$k[0]][$k[1]][$k[2]];
        case 4:
            return $Array[$k[1]][$k[1]][$k[2]][$k[3]];
    }
}

function dimplode($array) {
    if (!empty($array)) {
        return "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
    } else {
        return 0;
    }
}

function dmicrotime() {
    return array_sum(explode(' ', microtime()));
}

function dstrlen($str) {
    if (strtolower(CHARSET) != 'utf-8') {
        return strlen($str);
    }
    $count = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $value = ord($str[$i]);
        if ($value > 127) {
            $count++;
            if ($value >= 192 && $value <= 223)
                $i++;
            elseif ($value >= 224 && $value <= 239)
                $i = $i + 2;
            elseif ($value >= 240 && $value <= 247)
                $i = $i + 3;
        }
        $count++;
    }
    return $count;
}

function percentage($string) {
    return round($string * 100, '2') . '%';
}

function returnClass($class, $func = '') {
    static $classReturn = array();
    $m5 = $class . '_' . $func;
    if (!isset($classReturn[$m5])) {
        $o = new $class();
        if (!empty($func) && method_exists($o, $func)) {
            $classReturn[$m5] = call_user_func(array(&$o, $func));
        } else {
            $classReturn[$m5] = $o;
        }
    }
    return $classReturn[$m5];
}

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val{strlen($val) - 1});
    switch ($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

function resetkey($array, $key) {
    foreach ($array as $k => $val) {
        $newArray[] = $val[$key];
    }
    return $newArray;
}

function keybyvalue($array, $value) {
    foreach ($array as $key => $v) {
        $newarray[$key] = $v[$value];
    }
    return $newarray;
}

function substrstring($string, $format, $pres = '') {
    $v = 0;
    $formatnum = count($format);
    if ($formatnum > 1) {
        foreach ($format as $value) {
            $pre = $v > 0 ? $pres : '';
            $newstring .= $pre . substr($string, $v, $value);
            $v += $value;
        }
    } else {
        $formatfor = round(strlen($string) / $format[0]);
        for ($i = 0; $i < $formatfor; $i++) {
            $o = $i * $format[0];
            $pre = $i > 0 ? $pres : '';
            $newstring .= $pre . substr($string, $o, $format[0]);
        }
    }
    return $newstring;
}

function msgformat($message) {
    return nl2br(str_replace(array("\t", '   ', '  '), array('&nbsp; &nbsp; &nbsp; &nbsp; ', '&nbsp; &nbsp;', '&nbsp;&nbsp;'), $message));
}

function sendmail($to, $message) {
    $mail = new ext_phpmailer();
    $body = msgformat($message);
    $mail->CharSet = CHARSET;
    $mail->IsSMTP();
    $mail->IsHTML(true);
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.qq.com';
    $mail->Port = '25';
    $mail->Username = '328905418';
    $mail->Password = 'tengyuanye2010';
    $mail->From = 'chengyi2007@qq.com';
    $mail->FromName = 'Pay2u';
    $mail->AddReplyTo('chengyi2007@qq.com', 'Pay2u');
    $mail->AddAddress($to['fromaddr'], $to['fromname']);
    $mail->Subject = '';
    $mail->Body = $body;
    $mail->Send();
}

function valformat($text) {
    $text = strip_tags($text, '<table><tr><td><b><strong><i><em><u><a><div><span><p><strike><blockquote><ol><ul><li><font><img><br><br/><h1><h2><h3><h4><h5><h6><script>');
    if (ismozilla()) {
        $text = preg_replace("/(?<!<br>|<br \/>|\r)(\r\n|\n|\r)/", ' ', $text);
    }
    $text = preg_replace("/<br.*>/siU", "\n", $text);
    return $text;
}

function ismozilla() {
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if (strpos($useragent, 'gecko') !== FALSE) {
        preg_match("/gecko\/(\d+)/", $useragent, $regs);
        return $regs[1];
    }
    return FALSE;
}

function sizeformat($bytesize) {
    $i = 0;
    while (abs($bytesize) >= 1024) {
        $bytesize = $bytesize / 1024;
        $i++;
        if ($i == 4)
            break;
    }
    $units = array('Bytes', 'KB', 'MB', 'GB', 'TB');
    $newsize = round($bytesize, 2);
    return($newsize . $units[$i]);
}

function convert($value, $from) {
    return mb_convert_encoding($value, CHARSET, $from);
}

function checkc($string) {
    $encoding = '';
    for ($i = 0; $i < strlen($string); $i++) {
        if (ord($string{$i}) < 128)
            continue;
        if ((ord($string{$i}) & 224) == 224) {
            $char = $string{ ++$i};
            if ((ord($char) & 128) == 128) {
                $char = $string{ ++$i};
                if ((ord($char) & 128) == 128) {
                    $encoding = 'utf-8';
                    break;
                }
            }
        }
        if ((ord($string{$i}) & 192) == 192) {
            $char = $string{ ++$i};
            if ((ord($char) & 128) == 128) {
                $encoding = "gbk";
                break;
            }
        }
    }
    return $encoding;
}

function cutstr($string, $length, $dot = '') {
    if (strlen($string) <= $length) {
        return $string;
    }
    $pre = chr(1);
    $end = chr(1);
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), $string);
    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }
            if ($noc >= $length) {
                break;
            }
        }
        if ($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }
    $strcut = str_replace(array($pre . '&' . $end, $pre . '"' . $end, $pre . '<' . $end, $pre . '>' . $end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
    $pos = strrpos($strcut, chr(1));
    if ($pos !== false) {
        $strcut = substr($strcut, 0, $pos);
    }
    return $strcut . $dot;
}

function maketime($type) {
    switch ($type) {
        case 'day0':
            $retrun = strtotime(todate(TIMESTAMP, 'ymd'));
            break;
        case 'day24':
            $retrun = strtotime(todate(TIMESTAMP, 'ymd') . ' 23:59:59');
            break;
        case 'monthfirst':
            $retrun = strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
            break;
        case 'monthlast':
            $retrun = strtotime(date('Y-m-t 23:59:59', strtotime(date("Y-m-d"))));
            break;
        case 'yearfirst':

            break;
        case 'yearlast':

            break;
        default:
            $retrun = TIMESTAMP;
            break;
    }

    return $retrun;
}

function formatTimeChange($date, $dgformat = 'Y') {
    $timestmp = strtotime($date);
    return gmdate($dgformat, $timestmp);
}

function todate($mktime, $dgformat = 'all') {
    if ($mktime < 1) {
        return 'No Time';
    }
    $mktime += 8 * 3600;
    if ($dgformat == 'all') {
        $formattime = gmdate('Y-m-d H:i', $mktime);
    } elseif ($dgformat == 'ymd') {
        $formattime = gmdate('Y-m-d', $mktime);
    } elseif ($dgformat == 'md') {
        $formattime = gmdate('m-d', $mktime);
    } elseif ($dgformat == 'mdh') {
        $formattime = gmdate('m-d H:i', $mktime);
    } else {
        $formattime = gmdate($dgformat, $mktime);
    }
    return $formattime;
}

function getTimeFormat($date, $getType = 'last') {
    $timestmp = strtotime($date);
    if($getType == 'last') {
        return date('Y-m-t', $timestmp);
    }
}

function daddslashes($string, $force = 1) {
    if (is_array($string)) {
        $keys = array_keys($string);
        foreach ($keys as $key) {
            $val = $string[$key];
            unset($string[$key]);
            $string[addslashes($key)] = daddslashes($val, $force);
        }
    } else {
        $string = addslashes($string);
    }
    return $string;
}

function dstripslashes($string) {
    if (empty($string))
        return $string;
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dstripslashes($val);
        }
    } else {
        $string = stripslashes($string);
    }
    return $string;
}

function dhtmlspecialchars($string) {
    if (empty($string))
        return $string;
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = htmlspecialchars($string, ENT_QUOTES);
    }
    return $string;
}

function random($length, $numeric = 0) {
    $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
    if ($numeric) {
        $hash = '';
    } else {
        $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
        $length--;
    }
    $max = strlen($seed) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $seed{mt_rand(0, $max)};
    }
    return $hash;
}

function dencrypt($string, $key, $isEncrypt = true) {
    if (!isset($string{0}) || !isset($key{0})) {
        return false;
    }
    $dynKey = $isEncrypt ? hash('sha1', microtime(true)) : substr($string, 0, 40);
    $fixedKey = hash('sha1', $key);
    $dynKeyPart1 = substr($dynKey, 0, 20);
    $dynKeyPart2 = substr($dynKey, 20);
    $fixedKeyPart1 = substr($fixedKey, 0, 20);
    $fixedKeyPart2 = substr($fixedKey, 20);
    $key = hash('sha1', $dynKeyPart1 . $fixedKeyPart1 . $dynKeyPart2 . $fixedKeyPart2);
    $string = $isEncrypt ? $fixedKeyPart1 . $string . $dynKeyPart2 : (isset($string{339}) ? gzuncompress(base64_decode(substr($string, 40))) : base64_decode(substr($string, 40)));
    $n = 0;
    $result = '';
    $len = strlen($string);
    for ($n = 0; $n < $len; $n++) {
        $result .= chr(ord($string{$n}) ^ ord($key{$n % 40}));
    }
    return $isEncrypt ? $dynKey . str_replace('=', '', base64_encode($n > 299 ? gzcompress($result) : $result)) : substr($result, 20, -20);
}

function logs($msg, $type = 'php') {
    helper_log::writeLog($msg, $type);
}

function smallTime() {
    return array_sum(explode(' ', microtime()));
}

function arraytostring($array) {
    foreach ($array as $key => $value) {
        $string .= $key . ': ' . $value;
    }
    return $string;
}

function getTemplate($filename) {
    return Uoke_ROOT . 'template/' . $filename . '.php';
}

function en_des($encrypt, $key) {
    $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $encrypt, MCRYPT_MODE_CBC, APIKEYCODE);
    return base64_encode($encrypted);
}

function de_des($decrypt, $key) {
    return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($decrypt), MCRYPT_MODE_CBC, APIKEYCODE));
}

function stripPkcs7Padding($string) {
    $slast = ord(substr($string, -1));
    $slastc = chr($slast);
    $pcheck = substr($string, -$slast);
    if (preg_match("/$slastc{" . $slast . "}/", $string)) {
        $string = substr($string, 0, strlen($string) - $slast);
        return $string;
    } else {
        return false;
    }
}

function checkOrderId($orderid) {
    if (strlen(preg_replace('/[\.a-zA-Z]/s', '', $orderid)) > 0) {
        return false;
    }
    if (strlen($orderid) !== 15) {
        return false;
    }
    return $orderid;
}

function isfloat($int) {
    $int = sprintf("%.2f", $int);
    if (!is_numeric($int)) {
        return false;
    }
    return $int;
}

function isphone($val) {
    $isMob = "/^1[3-5,8]{1}[0-9]{9}$/";
    $isTel = "/^([0-9]{3,4}-)?[0-9]{7,8}$/";
    if (!preg_match($isMob, $val) && !preg_match($isTel, $val)) {
        return false;
    } else {
        return true;
    }
}

function isemail($val) {
    $pathString = strstr($val, '@');
    if (strlen(strstr($pathString, '.')) < 3 || strlen(strstr($pathString, '.')) > 8) {
        return false;
    } else {
        return true;
    }
}

function validateIDCard($IDCard) {
    if (strlen($IDCard) == 18) {
        return check18IDCard($IDCard);
    } elseif ((strlen($IDCard) == 15)) {
        $IDCard = convertIDCard15to18($IDCard);
        return check18IDCard($IDCard);
    } else {
        return false;
    }
}

//计算身份证的最后一位验证码,根据国家标准GB 11643-1999
function calcIDCardCode($IDCardBody) {
    if (strlen($IDCardBody) != 17) {
        return false;
    }
    //加权因子 
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值 
    $code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum = 0;

    for ($i = 0; $i < strlen($IDCardBody); $i++) {
        $checksum += substr($IDCardBody, $i, 1) * $factor[$i];
    }

    return $code[$checksum % 11];
}

// 将15位身份证升级到18位 
function convertIDCard15to18($IDCard) {
    if (strlen($IDCard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码 
        if (array_search(substr($IDCard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $IDCard = substr($IDCard, 0, 6) . '18' . substr($IDCard, 6, 9);
        } else {
            $IDCard = substr($IDCard, 0, 6) . '19' . substr($IDCard, 6, 9);
        }
    }
    $IDCard = $IDCard . calcIDCardCode($IDCard);
    return $IDCard;
}

// 18位身份证校验码有效性检查 
function check18IDCard($IDCard) {
    if (strlen($IDCard) != 18) {
        return false;
    }
    $IDCardBody = substr($IDCard, 0, 17); //身份证主体
    $IDCardCode = strtoupper(substr($IDCard, 17, 1)); //身份证最后一位的验证码
    if (calcIDCardCode($IDCardBody) != $IDCardCode) {
        return false;
    } else {
        return true;
    }
}

function hideString($string, $repstr = '****') {
    $pathString = strstr($string, '@');
    if (strlen(strstr($pathString, '.')) > 2) {
        $str = strlen($string) - strlen($pathString);
    } else {
        $str = strlen($string);
    }
    $start = ceil($str / 4.5);
    $length = ceil(($str - $start) / 2);
    return substr_replace($string, $repstr, $start, $length);
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

function arrayCheck($array) {
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $array[$key] = arrayCheck($value);
        } else {
            if (is_numeric($array[$key]) && count(explode('.', $array[$key])) == 2) {
                $array[$key] = (float) $array[$key];
            } elseif (is_numeric($array[$key])) {
                $array[$key] = (int) $array[$key];
            } elseif (is_bool($array[$key])) {
                $array[$key] = (bool) $array[$key];
            } else {
                $array[$key] = (string) $array[$key];
            }
        }
    }
    return $array;
}

function abslength($str) {
    $len = strlen($str);
    $i = 0;
    $j = 0;
    while ($i < $len) {
        if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xf9) . "]+$/", $str[$i])) {
            $i+=3;
        } else {
            $i+=1;
        }
        $j++;
    }
    return $j;
}

function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    if (!$_importFiles[$filename])
        throw new Exception($filename . ' Class File Not Found', '9999');
    return $_importFiles[$filename];
}

function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && DEBUGSET) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

function compile($filename) {
    $content = php_strip_whitespace($filename);
    $content = trim(substr($content, 5));
    if ('?>' == substr($content, -2))
        $content = substr($content, 0, -2);
    return $content;
}

function jsonFormat($data, $indent = null) {

    // 将urlencode的内容进行urldecode  
    $data = urldecode($data);

    // 缩进处理  
    $ret = '';
    $pos = 0;
    $length = strlen($data);
    $indent = isset($indent) ? $indent : '    ';
    $newline = "\n";
    $prevchar = '';
    $outofquotes = true;

    for ($i = 0; $i <= $length; $i++) {

        $char = substr($data, $i, 1);

        if ($char == '"' && $prevchar != '\\') {
            $outofquotes = !$outofquotes;
        } elseif (($char == '}' || $char == ']') && $outofquotes) {
            $ret .= $newline;
            $pos --;
            for ($j = 0; $j < $pos; $j++) {
                $ret .= $indent;
            }
        }

        $ret .= $char;

        if (($char == ',' || $char == '{' || $char == '[') && $outofquotes) {
            $ret .= $newline;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $ret .= $indent;
            }
        }

        $prevchar = $char;
    }

    return $ret;
}

/** 将数组元素进行urlencode 
 * @param String $val 
 */
function jsonFormatProtect(&$val) {
    if ($val !== true && $val !== false && $val !== null) {
        $val = urlencode($val);
    }
}

function epsForTen($number) {
    if($number < 1) {
        return '0元';
    }
    $string_num = strlen($number);
    switch ($string_num) {
        case $string_num > 10:
            return sprintf("%.2f", $number/100000000).'亿元';
        case $string_num > 9:
            return sprintf("%.2f", $number/10000000).'千万元';
        case $string_num > 6:
            return sprintf("%.2f", $number/1000000).'百万元';
        default:
            return sprintf("%.2f", $number/10000).'万元';
    }
}

?>