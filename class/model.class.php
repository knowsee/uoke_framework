<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

/**
 * Description of model
 *
 * @author chengyi
 */
class model {

    public $getInput;
    public $view = array();
    protected $pageNum = '20';
    protected $defaultTemplate = 'index';
    protected $uriReplace = false;
    protected $privateUri = array();
    protected $modelUriRewrite = array();
    protected $modelUriRewriteCL = '/';

    public function __construct() {
        
    }

    public function coreModel() {
        $this->getInput['act'] = !$this->getInput['act'] ? 'default' : $this->getInput['act'];
        $this->getInput['page'] = $this->getInput['page'] < 1 ? 1 : intval($this->getInput['page']);
        $className = strtolower($this->getInput['act']) . '_Action';
        try {
            if (!method_exists($this, $className)) {
                throw new Exception('SYSTEM ERROR MODEL NOT FOUND', -1000);
            } else {
                $this->$className();
            }
        } catch (Exception $ex) {
            debug($ex->getMessage(), $ex->getCode(), '');
        }
    }

    public function getSysInit($name) {
        return app::sget($name);
    }

    public function getModel($name) {
        try {
            $className = 'model_' . $name;
            return returnClass($className);
        } catch (Exception $ex) {
            debug($ex->getMessage(), $ex->getCode(), '');
        }
    }

    public function view($name, $value = '') {
        if (is_array($name)) {
            foreach ($name as $viewKey => $viewData) {
                $this->view[$viewKey] = $viewData;
            }
        } else {
            $this->view[$name] = $value;
        }
    }

    public function display($filename) {
        foreach ($this->view as $key => $value) {
            $$key = $value;
        }
        ob_clean();
        ob_flush();
        require Uoke_ROOT . 'template/' . $filename . '.php';
    }

    public function getFileInput($fileList = array()) {
        return app::v('file', $fileList, array('addslashes'));
    }

    public function getAllPageNum($num, $pageNum = '') {
        $pageNum = !$pageNum ? $this->pageNum : $pageNum;
        return ceil($num / $pageNum);
    }

    public function getPageUri() {
        $getArgs = func_get_args();
        $getArgs[0] = helper_http::repurl($getArgs[0]);
        return call_user_func_array('sprintf', $getArgs);
    }

    public function replaceUri() {
        foreach ($this->privateUri as $key => $url) {
            $a = parse_url($url);
            $a['path'] = basename($a['path'], ".php");
            parse_str($a['query'], $a['query']);
            $replaceString = array($a['path']);
            $strUri = isset($this->modelUriRewrite[$key]) ? $this->modelUriRewrite[$key] : $this->modelUriRewrite['global'];
            if(!$strUri) {
                continue;
            }
            $strNum = explode($this->modelUriRewriteCL, $strUri);
            $num = 0;
            for ($i = 0; $i < count($strNum); $i++) {
                $first = strpos($strNum[$i], '{');
                $last = strpos($strNum[$i], '}');
                if ($first && $last) {
                    $strNum[$i] = substr($strNum[$i], $first, ($last - $first + 1));
                    $num++;
                }
            }
            $addUri = array();
            foreach ($a['query'] as $k => $urlValue) {
                if (in_array('{' . $k . '}', $strNum)) {
                    $replaceString[] = $urlValue;
                } else {
                    $addUri[$k] = $urlValue;
                }
            }
            $this->privateUri[$key] = str_replace($strNum, $replaceString, $strUri). '?' .http_build_query($addUri);
        }
    }

    protected function modelDisplay($display = array()) {
        if($this->uriReplace == false) {
            $this->replaceUri();
        }
        $displayArray = array_merge(array('act' => $this->getInput['act']), $display, $this->privateUri);
        $this->view($displayArray);
        $this->display($this->defaultTemplate);
    }

    /*
     * New Page Html Pubilc.
     * @param $url         URI地址
     * @param $page        当前页码
     * @param $allpage     总页数
     * @param $style       样式 small / big
     * 
     */

    public function pageHtml($url, $page, $allpage, $style = 'big') {
        if ($allpage < 2) {
            return '';
        }
        $page = $page < 1 ? '1' : (int) $page;
        $mpurl = strpos($url, '?') !== FALSE ? '&amp;' : '?';
        $pageurl = $url . $mpurl . 'page=';
        $pagehtml .= '<div class="widget-foot"><ul class="pagination pull-right">';
        if ($page > 1) {
            $pagehtml .= '<li><a href=""' . $pageurl . abs($page - 1) . '"">' . self::sysl('page_up') . '</a></li>'; //self::sysl('page_up')
        }
        if ($style == 'big') {
            $beginpage = $page - 3 < 1 ? '1' : $page - 3;
            $pageup = $beginpage + 9;
            $pageup = $pageup > $allpage ? $allpage : $pageup;
            for ($i = $beginpage; $i <= $pageup; $i++) {
                if ($page == $i) {
                    $loop = 'class="active"';
                } else {
                    $loop = '';
                }
                $pagehtml .= '<li ' . $loop . '><a href="' . $pageurl . $i . '">' . $i . '</a></li>';
            }
        }
        if ($page + 1 <= $allpage) {
            $nextpage = $page + 1;
            $pagehtml .= '<li><a href="' . $pageurl . $nextpage . '">' . self::sysl('page_down') . '</a></li>'; //self::sysl('page_down')
        }
        $pagehtml .= '</ul><div class="clearfix"></div> </div>';
        return $pagehtml;
    }

}
