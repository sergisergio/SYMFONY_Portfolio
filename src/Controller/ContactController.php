<?php
/**
 * Created by PhpStorm.
 * User: leazygomalas
 * Date: 21/07/2019
 * Time: 17:08
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\SMTPClient;

class ContactController extends AbstractController
{
    /**
     * @Route(path="contact", name="contact")
     */
    public function send()
    {
        $use_smtp = '0';
        $emailto = 'ptraon@gmail.com';

        // retrieve from parameters
        $emailfrom = isset($_POST["email"]) ? $_POST["email"] : "";
        $nocomment = isset($_POST["nocomment"]) ? $_POST["nocomment"] : "";
        $subject = 'Vous avez reçu un email de la part de '.$_POST["email"];
        $message = '';
        $response = '';
        $response_fail = 'Une erreur est intervenue.';

        // Honeypot captcha
        if($nocomment == '') {

            $params = $_POST;
            foreach ( $params as $key=>$value ){

                if(!($key == 'ip' || $key == 'emailsubject' || $key == 'url' || $key == 'emailto' || $key == 'nocomment' || $key == 'v_error' || $key == 'v_email')){

                    $key = ucwords(str_replace("-", " ", $key));

                    if ( gettype( $value ) == "array" ){
                        $message .= "$key: \n";
                        foreach ( $value as $two_dim_value )
                            $message .= "...$two_dim_value<br>";
                    }else {
                        $message .= $value != '' ? "$key: $value\n" : '';
                    }
                }
            }

            $response = self::sendEmail($subject, $message, $emailto, $emailfrom);

        } else {

            $response = $response_fail;

        }

        echo $response;
    }

    public function sendEmail($subject, $content, $emailto, $emailfrom) {

        $from = $emailfrom;
        $response_sent = 'Merci. Votre message a bien été envoyé';
        $response_error = 'Une erreur est survenue. Veuillez réessayer.';
        $subject =  self::filter($subject);
        $url = "Page d'origine: ".$_SERVER['HTTP_REFERER'];
        $ip = "Adresse IP: ".$_SERVER["REMOTE_ADDR"];
        $message = $content."\n$ip\r\n$url";

        // Validate return email & inform admin
        $emailto = self::filter($emailto);

        // Setup final message
        $body = wordwrap($message);

        if($use_smtp == '1'){

            $SmtpServer = 'SMTP SERVER';
            $SmtpPort = 'SMTP PORT';
            $SmtpUser = 'SMTP USER';
            $SmtpPass = 'SMTP PASSWORD';

            $to = $emailto;
            $SMTPMail = new SMTPClient($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $subject, $body);
            $SMTPChat = $SMTPMail->self::SendMail();
            $response = $SMTPChat ? $response_sent : $response_error;

        } else {

            // Create header
            $headers = "From: $from\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/plain; charset=utf-8\r\n";
            $headers .= "Content-Transfer-Encoding: quoted-printable\r\n";

            // Send email
            $mail_sent = @mail($emailto, $subject, $body, $headers);
            $response = $mail_sent ? $response_sent : $response_error;

        }
        return $response;
    }

    private function filter($value) {
        $pattern = array("/\n/", "/\r/", "/content-type:/i", "/to:/i", "/from:/i", "/cc:/i");
        $value = preg_replace($pattern, "", $value);
        return $value;
    }
}