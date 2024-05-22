<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AJKEmail 
{
    public $from_name='PPMS - GoAJ&K';
    public $from_email='info@ajk.gov.pk';

    public $subject = 'Test';
    public $body = 'Test';
    public $sendto = array();

    function __construct($subject,$sendto){
        $this->subject = $subject;
        $this->sendto = $sendto;
    }

    function email_body($toName,$fromName,$message)
    {
        $this->body = '<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
         <tr>
         <td>&nbsp;</td>
         <td class="container">
         <div class="content">
         <table role="presentation" class="main">
         <tr>
         <td class="wrapper">
         <table role="presentation" border="0" cellpadding="0" cellspacing="0">
         <tr>
         <td>
         <p>Hi '.$toName.',<br>'.$message.'</p> 
         <p><br>Thank you  &  Good luck!<br><br>'.$fromName.'</p>
         </td>
         </tr>
         </table>
         </td>
         </tr>
         </table>';
    }

    public function footer()
    {
        $footer = '
        <div class="footer">
         <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="content-block powered-by">Powered by <a href="http://itb.ajk.gov.pk.io">Information Technology Board of AJ&K</a>.</td>
            </tr>
         </table>
        </div>
       </body>
      </html>';

        return $footer;
    }
    public function header()
    {
        //email_subject
        $header = '<!doctype html>
      <html>
        <head>
          <meta name="viewport" content="width=device-width" />
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
          <title>'.$this->subject.'</title>
          <style>
            img {border: none;-ms-interpolation-mode: bicubic;max-width: 100%;}
            body {background-color: #f6f6f6;font-family: sans-serif;-webkit-font-smoothing: antialiased;font-size: 14px;line-height: 1.4;margin: 0;padding: 0;-ms-text-size-adjust: 100%;-webkit-text-size-adjust: 100%;}
            table {border-collapse: separate;mso-table-lspace: 0pt;mso-table-rspace: 0pt;width: 100%; }
            table td {font-family: sans-serif;font-size: 14px;vertical-align: top; }
            .body {background-color: #f6f6f6;width: 100%; }
            .container {display: block;margin: 0 auto !important;max-width: 580px;padding: 10px;width: 580px; }
            .content {box-sizing: border-box;display: block;margin: 0 auto;max-width: 580px;padding: 10px; 
            }
            .main {background: #ffffff;border-radius: 3px;width: 100%; }
            .wrapper {box-sizing: border-box;padding: 20px; }
            .content-block {padding-bottom: 10px;padding-top: 10px;}
            .footer {clear: both;margin-top: 10px;text-align: center;width: 100%; }
            .footer td,.footer p,.footer span,.footer a {color: #999999;font-size: 12px;text-align: center; }
            h1,h2,h3,h4 {color: #000000;font-family: sans-serif;font-weight: 400;line-height: 1.4;margin: 0;margin-bottom: 30px; }
            h1 {font-size: 35px;font-weight: 300;text-align: center;text-transform: capitalize; }
            p,ul,ol {font-family: sans-serif;font-size: 14px;font-weight: normal;margin: 0;margin-bottom: 15px; }
            p li,ul li,ol li {list-style-position: inside;margin-left: 5px; }
            a {color: #3498db;text-decoration: underline; }
            .btn {box-sizing: border-box;width: 100%; }
            .btn > tbody > tr > td {padding-bottom: 15px; }
            .btn table {width: auto; }
            .btn table td {background-color: #ffffff;border-radius: 5px;text-align: center; }
            .btn a {background-color: #ffffff;border: solid 1px #3498db;border-radius: 5px;box-sizing: border-box;color: #3498db;cursor: pointer;display: inline-block;font-size: 14px;font-weight: bold;margin: 0;padding: 12px 25px;text-decoration: none;text-transform: capitalize; }
            .btn-primary table td {background-color: #3498db; }
            .btn-primary a {background-color: #3498db;border-color: #3498db;color: #ffffff; }
            .last {margin-bottom: 0; }
            .first {margin-top: 0; }
            .align-center {text-align: center; }
            .align-right {text-align: right; }
            .align-left {text-align: left; }
            .clear {clear: both; }
            .mt0 {margin-top: 0; }
            .mb0 {margin-bottom: 0; }
            .powered-by a {text-decoration: none; }
            hr {border: 0;border-bottom: 1px solid #f6f6f6;margin: 20px 0; }
            @media only screen and (max-width: 620px) {
              table[class=body] h1 {font-size: 28px !important;margin-bottom: 10px !important; }
              table[class=body] p,table[class=body] ul,table[class=body] ol,table[class=body] td,table[class=body] span,table[class=body] a {font-size: 16px !important; }
              table[class=body] .wrapper,table[class=body] .article {padding: 10px !important; }
              table[class=body] .content {padding: 0 !important; }
              table[class=body] .container {padding: 0 !important;width: 100% !important; }
              table[class=body] .main {border-left-width: 0 !important;border-radius: 0 !important;border-right-width: 0 !important; }
              table[class=body] .btn table {width: 100% !important; }
              table[class=body] .btn a {width: 100% !important; }
              table[class=body] .img-responsive {height: auto !important;max-width: 100% !important;width: auto !important; }
            }
            @media all {
             .ExternalClass {width: 100%; }
             .ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div {line-height: 100%;}
             .apple-link a {color: inherit !important;font-family: inherit !important;font-size: inherit !important;font-weight: inherit !important;line-height: inherit !important;text-decoration: none !important; }
             .btn-primary table td:hover {background-color: #34495e !important; }
             .btn-primary a:hover {background-color: #34495e !important;border-color: #34495e !important; } 
           }
         </style>
       </head>
       <body class="">';
       return $header;
    }
    public function send_individual($fromName,$message)
    {
        // test and change accordingly when in use
        $ret = array('status'=>false,'message'=>'Sending...');

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try 
        {
            //Server settings
            $mail->SMTPDebug = 0;                                             // TCP port to connect to
            $mail->IsSMTP();
            $mail->Host = "mail.ajk.gov.pk";
            //Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->isHTML(true);                                  // Set userEmail format to HTML
            $mail->Subject = $this->subject;
            foreach($this->sendto as $mailto)
            {
                $mail->addAddress($mailto[1], $mailto[0]);
                $mail->Body    = $this->header().$this->email_body($mailto[0],$fromName,$message).$this->footer();
                $mail->send();
            }
            //Content

            $ret = array('status'=>true,'message'=>'Notification email Sent.');
        } catch (Exception $e) {
            $ret = array('status'=>false,'message'=>'Unable to send notification on email.'.$mail->ErrorInfo);
        }

        return $ret;

    }
    public function send()
    {
        $ret = array('status'=>false,'message'=>'Sending...');

        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try 
        {
            //Server settings
            $mail->SMTPDebug = 0;                                             // TCP port to connect to
            $mail->IsSMTP();
            $mail->Host = "mail.ajk.gov.pk";
            //Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            foreach($this->sendto as $mailto)
            {
              $mail->addAddress($mailto[1], $mailto[0]);
            }
            //Content
            $mail->isHTML(true);                                  // Set userEmail format to HTML
            $mail->Subject = $this->subject;
            $mail->Body    = $this->header().$this->body.$this->footer();

            $mail->send();
            $ret = array('status'=>true,'message'=>'Notification email Sent.');
        } catch (Exception $e) {
            $ret = array('status'=>false,'message'=>'Unable to send notification('.$this->subject.').'.$mail->ErrorInfo);
        }

        return $ret;
    }
}