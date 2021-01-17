<?php

namespace spark\drivers\Mail;

use \PHPMailer;

/**
* Mailer
*
* @version 0.1
*/
class Mailer
{
    const TYPE_EMAIL = 'admin::emails/email_verification.php';
    const TYPE_FORGOT_PASS        = 'admin::emails/forgot_password.php';

    public function send($mailName, $to, $subject, array $data = [])
    {
        $app = app();

        $data['_title'] = $subject;

        $content = $app->view->fetch($mailName, $data);
        $mailer = $this->getPhpMailer(get_option('site_email'), $to, $subject, $content);
        return $mailer->send();
    }

    public function getPhpMailer($from, $to, $subject, $body)
    {
        $app = app();
        $smtp = (int) get_option('smtp_enabled');
        $mail = new PHPMailer(true);
        $mail->XMailer = APP_NAME;

        if ($smtp) {
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Debugoutput = function ($str, $level) {
                logger()->critical($str);
            };
            $mail->Host = get_option('smtp_host');
            $mail->Port = get_option('smtp_port');

            if ((int) get_option('smtp_auth_enabled')) {
                $mail->SMTPAuth = true;
                $mail->Username = get_option('smtp_username');
                $mail->Password = get_option('smtp_password');
                $mail->SMTPSecure = get_option('smtp_secure');
            }
        }

        //Set the CharSet encoding
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = sp_strip_tags($body);
        $mail->isHTML(true);
        $mail->setFrom($from, get_option('site_name'));

        foreach ((array) $to as $address) {
            $mail->addAddress($address);
        }


        return $mail;
    }
}
