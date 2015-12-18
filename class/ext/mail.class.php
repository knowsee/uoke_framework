<?php

if (!defined('IN_UOKE')) {
    exit('Wrong!!');
}

class ext_mail {

    public $server = '';
    public $username = '';
    public $password = '';
    public $marubox = '';
    public $email = '';

    public function __construct($username, $password, $EmailAddress, $mailserver = 'localhost', $ssl = false) {
        set_time_limit(60);
        $this->server = "{" . "$mailserver:143" . "}INBOX";
        $this->username = $username;
        $this->password = $password;
        $this->email = $EmailAddress;
    }

    function connect() {
        $this->marubox = imap_open($this->server, $this->username, $this->password);
        if (!$this->marubox) {
            echo "Error: Connecting to mail server";
            exit;
        }
    }

    function getBody($mid) {
        if (!$this->marubox)
            return false;
        $body = $this->get_part($this->marubox, $mid, "TEXT/HTML");
        if (!$body) {
            $body = $this->get_part($this->marubox, $mid, "TEXT/PLAIN");
        }
        if (!$body) {
            $body = $this->get_part($this->marubox, $mid, "MULTIPART/ALTERNATIVE");
        }
        $freecov = array('gbk', 'gb2312', 'gb18030');
        if (!in_array($body['charset'], $freecov)) {
            return $this->email_Decode($body['text']);
        } else {
            return $body['text'];
        }
    }

    function getHeaders($mid) {
        if (!$this->marubox) {
            return false;
        }
        $mail_header = @imap_fetchheader($this->marubox, $mid);
        if ($mail_header == false) {
            return false;
        }
        $mail_header = imap_rfc822_parse_headers($mail_header);
        $sender = isset($mail_header->from[0]) ? $mail_header->from[0] : '';
        $sender_replyto = isset($mail_header->reply_to[0]) ? $mail_header->reply_to[0] : '';
        if (strtolower($sender->mailbox) != 'mailer-daemon' && strtolower($sender->mailbox) != 'postmaster') {
            $newvalue['personal'] = $this->email_Decode($sender->personal);
            $newvalue['sender_personal'] = $this->email_Decode($sender_replyto->personal);
            $newvalue['subject'] = $this->email_Decode($mail_header->subject);
            $newvalue['toaddress'] = isset($mail_header->toaddress) ? $this->email_Decode($mail_header->toaddress) : '';
            $mail_header = (array) $mail_header;
            $sender = (array) $sender;
            $mail_details = array(
                'feid' => imap_uid($this->marubox, $mid),
                'from' => strtolower($sender['mailbox']) . '@' . $sender['host'],
                'from_name' => $newvalue['personal'],
                'to_other' => strtolower($sender_replyto->mailbox) . '@' . $sender_replyto->host,
                'toname_other' => $newvalue['sender_personal'],
                'subjects' => $newvalue['subject'],
                'to' => $newvalue['toaddress'],
                'time' => strtotime($mail_header['Date'])
            );
        }
        return $mail_details;
    }

    function get_mime_type(&$structure) {
        $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
        if ($structure->subtype) {
            return $primary_mime_type[(int) $structure->type] . '/' . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }

    function email_Decode($value) {
        if ($value[0] !== '=') {
            $newvalue = explode('=?', $value, 2);
            if ($newvalue[1]) {
                if (checkc($newvalue[0]) !== 'gbk') {
                    $newcov = convert($newvalue[0]);
                }
                $t = '=?' . $newvalue[1];
                $newcov .= iconv_mime_decode($t, 0, 'gbk');
                return $newcov;
            }
            return checkc($value) !== 'gbk' ? convert($value) : $value;
        } elseif ($value[0] == '=') {
            return iconv_mime_decode($value, 0, 'gbk');
        }
    }

    function get_part($stream, $msg_number, $mime_type, $structure = false, $part_number = false, $charset = '') {
        if (!$structure && is_numeric($msg_number) && $msg_number > 0) {
            $structure = imap_fetchstructure($stream, $msg_number);
        }
        $part = $structure->parts[0]->parameters[0];
        if ($part->value[0] !== '-') {
            $charset = $charset ? $charset : $part->value;
            if (!$charset) {
                $charset = $structure->parameters[0]->value;
            }
        }
        if ($structure) {
            if ($mime_type == $this->get_mime_type($structure)) {
                if (!$part_number) {
                    $part_number = "1";
                }
                $text = imap_fetchbody($stream, $msg_number, $part_number);
                $return['charset'] = $charset;
                if ($structure->encoding == 3) {
                    $return['text'] = imap_base64($text);
                } else if ($structure->encoding == 4) {
                    $return['text'] = imap_qprint($text);
                } else {
                    $return['text'] = $text;
                }
                return $return;
            }
            if ($structure->type == 1) {
                while (list($index, $sub_structure) = each($structure->parts)) {
                    if ($part_number) {
                        $prefix = $part_number . '.';
                    }
                    $data = $this->get_part($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1), $charset);
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }

    function getTotalMails() {
        if (!$this->marubox)
            return false;
        $headers = imap_thread($this->marubox);
        foreach ($headers as $key => $val) {
            $tree = explode('.', $key);
            if ($tree[1] == 'num') {
                $return[] = $val;
            }
        }
        return $return;
    }

    function getStatus() {
        $status = imap_status($this->marubox, $this->server, SA_ALL);
        $return['num'] = $status->messages;
        $return['newnum'] = $status->unseen;
        $return['lastuid'] = $status->uidnext;
        return $return;
    }

    function GetAttach($mid, $path) {
        if (!$this->marubox) {
            return false;
        }
        $struckture = imap_fetchstructure($this->marubox, $mid);
        $ar = "";
        if ($struckture->parts) {
            foreach ($struckture->parts as $key => $value) {
                $enc = $struckture->parts[$key]->encoding;
                if ($struckture->parts[$key]->ifdparameters) {
                    $name = $struckture->parts[$key]->dparameters[0]->value;
                    $message = imap_fetchbody($this->marubox, $mid, $key + 1);
                    if ($enc == 0)
                        $message = imap_8bit($message);
                    if ($enc == 1)
                        $message = imap_8bit($message);
                    if ($enc == 2)
                        $message = imap_binary($message);
                    if ($enc == 3)
                        $message = imap_base64($message);
                    if ($enc == 4)
                        $message = quoted_printable_decode($message);
                    if ($enc == 5)
                        $message = $message;
                    $fp = fopen($path . $name, "w");
                    fwrite($fp, $message);
                    fclose($fp);
                    $ar = $ar . $name . ",";
                }
                if ($struckture->parts[$key]->parts) {
                    foreach ($struckture->parts[$key]->parts as $keyb => $valueb) {
                        $enc = $struckture->parts[$key]->parts[$keyb]->encoding;
                        if ($struckture->parts[$key]->parts[$keyb]->ifdparameters) {
                            $name = $struckture->parts[$key]->parts[$keyb]->dparameters[0]->value;
                            $partnro = ($key + 1) . "." . ($keyb + 1);
                            $message = imap_fetchbody($this->marubox, $mid, $partnro);
                            if ($enc == 0)
                                $message = imap_8bit($message);
                            if ($enc == 1)
                                $message = imap_8bit($message);
                            if ($enc == 2)
                                $message = imap_binary($message);
                            if ($enc == 3)
                                $message = imap_base64($message);
                            if ($enc == 4)
                                $message = quoted_printable_decode($message);
                            if ($enc == 5)
                                $message = $message;
                            $fp = fopen($path . $name, "w");
                            fwrite($fp, $message);
                            fclose($fp);
                            $ar = $ar . $name . ",";
                        }
                    }
                }
            }
        }
        $ar = substr($ar, 0, (strlen($ar) - 1));
        return $ar;
    }

    function deleteMails($mid) {
        if (!$this->marubox) {
            return false;
        }
        imap_delete($this->marubox, $mid);
    }

    function close_mailbox() {
        if (!$this->marubox) {
            return false;
        }
        imap_close($this->marubox, CL_EXPUNGE);
    }

}

?>