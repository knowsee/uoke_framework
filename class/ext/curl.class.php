<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * 读取远程数据类
 *
 * @author chengyi
 */
class ext_curl {

    private $url = '';
    private $body = '';
    private $header = '';
    private $ch = '';
    private $html = '';
    private $set = array();

    /**
     * 构造函数
     * 
     * @access public
     */
    public function __construct($time = 30) {
        $this->set['CURLOPT_CONNECTTIMEOUT'] = $time;
        return $this;
    }

    public function close() {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

    public function open() {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->set['CURLOPT_CONNECTTIMEOUT']);
        $this->header = '';
        $this->body = '';
        return $this;
    }

    public function setCookies($cookies = '') {
        if (!$cookies) {
            foreach ($_COOKIE as $k => $v) {
                $cookies .= $k . "=" . $v . ";";
            }
        }
        curl_setopt($this->ch, CURLOPT_COOKIE, $cookies);
        return $this;
    }

    public function setCurlsetting($newstring) {
        foreach ($newstring as $key => $value) {
            curl_setopt($this->ch, $key, $value);
        }
        return $this;
    }

    public function post($url, $query = array()) {
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_URL, $url);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $query);
        $this->requrest();
        return $this;
    }

    public function get($url, $query = array(), $queryStr = '') {
        curl_setopt($this->ch, CURLOPT_URL, $this->makeurl($url, $query, $queryStr));
        $this->requrest();
        return $this;
    }

    public function header() {
        return $this->header;
    }

    public function body() {
        return $this->body;
    }

    public function getStatus($url) {
        curl_setopt($this->ch, CURLOPT_URL, $this->makeurl($url));
        $this->html = curl_exec($this->ch);
        $header_size = curl_getinfo($this->ch);
        return $header_size['http_code'];
    }

    private function requrest() {
        $response = curl_exec($this->ch);
        $errno = curl_errno($this->ch);
        if ($errno > 0) {
            throw new curl_Exception(curl_error($this->ch), $errno);
        }
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $this->header = substr($response, 0, $header_size);
        $this->body = substr($response, $header_size);
    }

    private function makeurl($url, $query = array(), $queryStr) {
        if (!empty($query)) {
            $url .= strpos($url, '?') === false ? '?' : '&';
            $url .= is_array($query) ? http_build_query($query) : $query;
            if ($queryStr) {
                $url .= '&'.$queryStr;
            }
        }
        $this->url = $url;
        return $url;
    }
    
    public function getUri() {
        return $this->url;
    }

    public function getPageContent() {
        $pageinfo['content_type'] = curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE);
        preg_match('/charset=([^\s\n\r]+)/i', curl_getinfo($this->ch, CURLINFO_CONTENT_TYPE), $matches); //从header 里取charset   
        if (trim($matches[1])) {
            $pageinfo['charset'] = trim($matches[1]);
        }
        if (empty($pageinfo['charset'])) {
            preg_match('@<meta.+charset=([\w\-]+)[^>]*>@i', $this->html, $matches);
            $pageinfo['charset'] = trim($matches[1]);
        }
        $our = str_replace('-', '', CHARSET);
        $pageinfo['charset'] = str_replace('-', '', $pageinfo['charset']);
        if (strtolower($pageinfo['charset']) !== $our) {
            $this->html = $html = mb_convert_encoding($this->html, 'UTF-8', $pageinfo['charset']);
        }
        $pageinfo['urllist'] = $this->getUrl($this->html);
        $this->html = preg_replace("/<script.*>(.*)<\/script>/smUi", '', $this->html);
        //remove link    
        $this->html = preg_replace("/<link\s+[^>]+>/smUi", '', $this->html);
        //remove <!--  -->   
        $this->html = preg_replace("/<!--.*-->/smUi", '', $this->html);
        //remove <style  </<style>   
        $this->html = preg_replace("/<style.*>(.*)<\/style>/smUi", '', $this->html);
        //remove 中文空格   
        $this->html = preg_replace("/　/", '', $this->html);
        preg_match('@<meta\s+name=\"*description\"*\s+content\s*=\s*([^/>]+)/*>@i', $this->html, $matches);
        $desc = trim($matches[1]);
        $pageinfo['description'] = str_replace("\"", '', $desc);
        preg_match('@<meta\s+name=\"*keywords\"*\s+content\s*=\s*([^/>]+)/*>@i', $this->html, $matches);
        $keywords = trim($matches[1]);
        $pageinfo['keywords'] = str_replace("\"", '', $keywords);
        preg_match("/<title>(.*)<\/title>/smUi", $this->html, $matches);
        $pageinfo['title'] = trim($matches[1]);
        preg_match("/<body.*>(.*)<\/body>/smUi", $this->html, $matches);
        $pageinfo['body'] = addslashes($this->replaceHtmlAndJs($matches[1]));
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $pageinfo['all'] = str_replace("/r", '', $this->replaceHtmlAndJs(substr($html, $header_size)));
        return $pageinfo;
    }

    public function getUrl($data) {
        $pattern = '/<a(?:.*?)href=[\'"]([^\"\']*)[\'"][^<]*?<\/a>/i';
        preg_match_all($pattern, $data, $links);
        return $links[1];
    }

    /**
     * 去掉所有的HTML标记和JavaScript标记  
     */
    public function replaceHtmlAndJs($document) {
        $search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
            '@<[\/\!]*?[^<>]*?>@si', // Strip out HTML tags
            '@<style[^>]*?>.*?</style>@siU', // Strip style tags properly
            '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
        );
        $text = preg_replace($search, '', $document);
        return $text;
    }

    public function __destruct() {
        $this->close();
    }

}

class curl_Exception extends Exception {
    
}

?>
