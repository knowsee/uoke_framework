<?php

/**
 * 处理错误类
 * 
 * @author chengyi
 */
class qunkenException extends Exception {

    protected $message;
    protected $more;
    protected $errorInfo;
    private $strDEBUG = array();

    public function __construct($message, $code = 0, $other = '') {
        $this->debugInfo($message, $code, $other);
    }

    public function debugInfo($message, $code = 0, $other = '') {
        $this->message = $message ? $message : $this->getMessage();
        $this->errorInfo = array('version' => isset($other['version']) ? $other['version'] : PHP_VERSION, 'file' => isset($other['file']) ? stripslashes($other['file']) : (string) $this->getFile()
            , 'line' => isset($other['line']) ? $other['line'] : $this->getLine(), 'code' => !$this->getCode() ? $code : $this->getCode(), 'sql' => isset($other['sql']) ? stripslashes($other['sql']) : '', 'drive' => isset($other['drive']) ? $other['drive'] : 'php');
        $this->makeLog();
        if (DEBUGSET) {
            if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) || (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == '1')) {
                $this->ajax = TRUE;
            } else {
                $this->ajax = FALSE;
            }
            $this->showError(DEBUGSET > 0 ? true : false);
        }
    }

    public function __toString() {
        return 'Error Info:' . $this->getMessage();
    }

    public function makeLog() {
        $put = '';
        foreach ($_REQUEST as $key => $value) {
            $value = is_array($value) ? $this->showArray($value) : $value;
            $put .= "[$key: $value] \n";
        }
        $sqlerror = '';
        $trace = $this->getTrace();
        if (!$trace[0]['line']) {
            $sqlerror = "Error: {$this->errorInfo['file']}(line: {$this->errorInfo['line']}) <br /> {$trace[0]['class']}{$trace[0]['type']}{$trace[0]['function']} \r\n";
        } else {
            foreach ($this->getTrace() as $key => $value) {
                if(isset($value['file'])) {
                    $sqlerror .= "\r\nError[$key]: $value[file](line: $value[line]) [$value[class]$value[type]$value[function]]";
                    $sqlerror .= "\r\nArgs: (".$this->showArray($value['args']).")";
                } else {
                    $sqlerror .= "\r\nError[$key]: [$value[class]$value[type]$value[function]]";
                    $sqlerror .= "\r\nArgs: (".$this->showArray($value['args']).")";
                }
            }
        }
        helper_log::writeLog("Error-Msg:{$this->message},\n FileTrace: {$sqlerror}, \nDrive-Info: {$this->errorInfo['drive']}, Error-Url: {$_SERVER['QUERY_STRING']},\nGET&&POST：" . $put, $this->errorInfo['drive']);
    }
	
    public function showArray($array) {
        return implode(',', $array);
    }

    public function showError($show = false) {
        $this->message = str_replace(Uoke_ROOT, '{WEBROOT}', $this->message);
        ob_clean();
        ob_start();
        if ($show) {
            if (!empty($this->errorInfo['sql'])) {
                $this->more = '【DEBUG-SQL】"' . $this->errorInfo['sql'] . '"<br>';
            }
            $trace = $this->getTrace();
            $sqlerror = "Error: {$this->errorInfo[file]}(line: {$this->errorInfo[line]}) <br /> {$trace[0]['class']}{$trace[0]['type']}{$trace[0]['function']} <br /><br />Error File Trace:<br />";
            if ($trace[0]['line']) {
                foreach ($trace as $key => $value) {
                    if(isset($value['file'])) {
                        $sqlerror .= "<br>Error[$key]: $value[file](line: $value[line]) [$value[class]$value[type]$value[function]]";
                        $sqlerror .= "<br>Args: (".$this->showArray($value['args']).')';
                    } else {
                        $sqlerror .= "<br>Error[$key]: [$value[class]$value[type]$value[function]]";
                        $sqlerror .= "<br>Args: (".$this->showArray($value['args']).')';
                    }
                }
            }
            $this->more = $sqlerror;
            if ($this->ajax) {
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    echo '<div style="font-size: 10px;">';
                    echo '<p>' . $this->errorInfo['drive'] . ' Error' . ', (' . $this->errorInfo['version'] . ')</p>';
                    echo '<p>Error message: <br><font color="#FF8800">' . $this->message . '</font></p>';
                    echo $this->more ? '<p>Error : <br><font color="#FF8800">' . $this->more : '' . '</font></p>';
                    echo '</div>';
                } else {
                    echo '<div style="font-size: 10px;">';
                    echo '<p>' . $this->errorInfo['drive'] . ' Error' . ', (' . $this->errorInfo['version'] . ')</p>';
                    echo '<p>Error message: <br><font color="#FF8800">' . $this->message . '</font></p>';
                    echo $this->more ? '<p>Error : <br><font color="#FF8800">' . $this->more : '' . '</font></p>';
                    echo '</div>';
                }
            } else {
                require Uoke_ROOT . 'class/lib/qunkenErrorPage.php';
            }
        } else {
            if (!$this->ajax) {
                echo $this->message . '<br />';
            } else {
                echo json_encode(array('status' => $this->errorInfo['code'], 'data' => array('msg' => $this->message), 'msg' => $this->message));
            }
        }
        exit;
    }

}

?>
