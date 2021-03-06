<?php

/**
 * Mail Service Send emails in different formats
 *
 * @author Iulian Cristea
 * @copyright 2015-2016 memobit.ro
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Application\Services
 * @version 1.0.1
*/

namespace Application\Services;

use Core\Config;

/**
 * Mail Service Class Send emails in different formats
 * 
*/
class MailService {

	/**
	 * Send a simple HTML email.
	 *
	 * @param string $subject The subject used in the email.
	 * @param string $to The email destination.
	 * @param string $body The body of the email.
	 * @return bool If one argument is empty will return false. Also returns TRUE/FALSE whether the email has been sent with success or not.
	*/
	public static final function send($subject = '', $to = '', $body = '') {

		if (empty($to) || empty($subject) || empty($body)) {
			return false;
		}

		$headers  = "From: " . Config::get('mail.from') . " \r\n";
		$headers .= "Content-Type: text/html; charset=utf-8 ";
		$headers .= "MIME-Version: 1.0 ";

		return @mail($to, $subject, $body, $headers);
	}

	/**
	 * Sends a HTML email using SMTP.
	 *
	 * @param string $subject The subject used in the email.
	 * @param string $to The email destination.
	 * @param string $body The body of the email.
	 * @return bool If one argument is empty will return false. Returns an Email Error if it fails or TRUE on success.
	*/
	public static final function send_smtp($subject = '', $to = '', $body = '') {

		if (empty($to) || empty($subject) || empty($body)) {
			return false;
		}

		include_once(dirname(__FILE__) . '/../Vendors/PHPMailer/class.phpmailer.php');
		include_once(dirname(__FILE__) . '/../Vendors/PHPMailer/class.smtp.php');

		$mail = new \PHPMailer(); 

		$mail->IsSMTP();
		$mail->Host = Config::get('mail.host');
		$mail->CharSet = "UTF-8";
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = "ssl";
		$mail->Username = Config::get('mail.username');
		$mail->Password = Config::get('mail.password');
		$mail->Port = Config::get('mail.port');


	    $mail->SetFrom(Config::get('mail.from'));
	    $mail->Subject = $subject;
	    $mail->MsgHTML($body);
	    // $mail->AddAddress($to, "USER NAME");
	    $mail->AddAddress($to);

	    if(!$mail->Send()) {
	        return "Mailer Error: " . $mail->ErrorInfo;
	    } else {
	        return true;
	    }
	}
}