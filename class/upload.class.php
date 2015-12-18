<?php
if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}
/**
 * 上传处理类
 *
 * @author chengyi
 */
class upload {

    private $path;
    private $error = 0;
    private $usedate = FALSE;
    private $upload = array();
    private $errormsg = array('501' => '不存在的上传文件', '503' => '文件类型不正确', '502' => '系统权限不足，请联系管理员');

    /**
     * 构造函数
     * @param mixed $path 放置文件夹
     * @param mixed $usedate 是否生成日期目录
     * @access public
     */
    public function __construct($path = 'upload', $usedate = TRUE, $otherpath = '') {
        $this->path = CONFIG('otherconfig/static/dataurl') . $path;
        $this->path = $otherpath ? $this->path . '/' . $otherpath . '/' : $this->path . '/';

        if (!helper_file::checkdir($this->path)) {
            helper_file::makedir($this->path);
        }

        $this->usedate = $usedate;
    }
    
    public function uploadWithArr($num, $upload, $newfilename = '', $allowtype = array('jpg', 'png', 'gif')) {
        for($i=1; $i<=$num; $i++) {
            $arr = array('name' => $upload['name'][$i], 'type' => $upload['type'][$i], 'tmp_name' => $upload['tmp_name'][$i], 
                'error' => $upload['error'][$i], 'size' => $upload['size'][$i]);
            if(!$upload['name'][$i]) {
                $return[$i] = '';
            } else {
                $return[$i] = $this->upload($arr,$newfilename,$allowtype)->getFileUri();
            }
        }
        return $return;
    }
    
    
    public function upload($upload, $newfilename = '', $allowtype = array('jpg', 'png', 'gif')) {
        //$upload['tmp_name'] = str_replace('\\\\', '\\', $upload['tmp_name']);
        $typeallow = (array) $allowtype;
        try {
            if (!is_uploaded_file($upload['tmp_name']) || $upload['size'] == 0) {
                $this->error = 501;
                throw new Exception($this->errormsg[$this->error]);
            } else {
                if ($this->usedate) {
                    $uploadpath = $this->path . date('Ymd') . '/';
                } else {
                    $uploadpath = $this->path;
                }
                $this->upload['size'] = intval($upload['size']);
                $this->upload['name'] = htmlspecialchars(trim($upload['name']), ENT_QUOTES);
                $this->upload['ext'] = $this->fileext($upload['name']);
                if (!$this->filetypecheck($typeallow)) {
                    $this->error = 503;
                    throw new Exception($this->errormsg[$this->error]);
                }
                $this->upload['localname'] = !$newfilename ? date('Ymd') . cutstr(md5($this->upload['name']), 20) . '.' . $this->upload['ext'] : $newfilename . '.' . $this->upload['ext'];
                if (strlen($this->upload['name']) > 90) {
                    $this->upload['name'] = cutstr($this->upload['name'], 80, '') . '.' . $this->upload['ext'];
                }
                $this->upload['pathdir'] = $uploadpath;
                if (!helper_file::checkdir($this->upload['pathdir'])) {
                    helper_file::makedir($this->upload['pathdir']);
                }
                $this->upload['path'] = Uoke_ROOT . $this->upload['pathdir'] . $this->upload['localname'];
                if (move_uploaded_file($upload['tmp_name'], $this->upload['path'])) {
                    $this->error = 0;
                } elseif (copy($upload['tmp_name'], $this->upload['path'])) {
                    $this->error = 0;
                } elseif (is_readable($upload['tmp_name']) && ($fp_s = fopen($upload['tmp_name'], 'rb')) && ($fp_t = fopen($this->upload['path'], 'wb'))) {
                    while (!feof($fp_s)) {
                        $s = fread($fp_s, 1024 * 512);
                        fwrite($fp_t, $s);
                    }
                    fclose($fp_s);
                    fclose($fp_t);
                    $this->error = 0;
                } else {
                    $this->error = 502;
                    throw new Exception($this->errormsg[$this->error]);
                }
                $this->upload['path'] = str_replace(Uoke_ROOT, '', $this->upload['path']);
                clearstatcache();
                return $this;
            }
        } catch (Exception $e) {
            return $this;
        }
    }

    /**
     * 删除当前附件
     * @access public
     */
    public function delete() {
        @unlink($this->upload['path']);
    }

    public function readinfo() {
        return $this->upload;
    }
    
    public function getFileUri() {
        return $this->upload['path'];
    }

    public function is_image() {
        $pic = getimagesize($this->upload['path']);
        return $pic;
    }

    private function fileext($filename) {
        return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
    }

    private function filetypecheck($typeallow) {
        if (!in_array($this->upload['ext'], $typeallow)) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

?>
