<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" /> 
        <title>Uoke Error Page</title>
        <style type="text/css"> 
            body {
                background: #f7fbe9;
                font-family: "Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana;
            }
            #error {
                background: none repeat scroll 0 0 #333333;
                border-radius: 4px 4px 4px 4px;
                color: #FFFFFF;
                margin: 100px auto 0;
                padding: 10px 20px;
                width: 620px;
                -moz-border-radius-topleft: 4px;
                -moz-border-radius-topright: 4px;
                -moz-border-radius-bottomleft: 4px;
                -moz-border-radius-bottomright: 4px;
                -webkit-border-top-left-radius: 4px;
                -webkit-border-top-right-radius: 4px;
                -webkit-border-bottom-left-radius: 4px;
                -webkit-border-bottom-right-radius: 4px;
                border-top-left-radius: 4px;
                border-top-right-radius: 4px;
                border-bottom-left-radius: 4px;
                border-bottom-right-radius: 4px;
            }
            h1 {
                padding: 10px;
                margin: 0;
                font-size: 36px;
            }
            p {
                padding: 0 20px 0 20px;
                margin: 0;
                font-size: 14px;
            }
            img {
                padding: 0 0 5px 500px;
            }
        </style> 
    </head> 
    <body> 
        <div id="error">
            <h2><?php echo $this->errorInfo['drive'] . ' Error' . ', (' . $this->errorInfo['version'] . ')'; ?></h2>
            <p>Error Code: <br><font color="#FF8800"><?php echo $this->errorInfo['code']; ?></font></p>
            <p>Error message: <br><font color="#FF8800"><?php echo $this->message; ?></font></p>
            <p>Error Other: <?=  var_export($this->errorInfo, true)?></p>
            <?php echo $this->more ? '<p>Error: <br><font color="#FF8800">' . $this->more : ''; ?></font></p>
            <?php echo '<p style="text-align:right;">Base In Uoke 2.9.1</p>'; ?>
        </div>
    </body>
</html>