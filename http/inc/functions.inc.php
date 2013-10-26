<?php

require_once('Mail.php');

/**********************************

Generic functions theat may be called from many differant places

*/

function sendEmail($settings, $subject, $body) {

		$mailSettings = array(
				'host' => $settings['alertSmtp']
			);
			
		if ($settings['alertSMTPAuth']) {
			$mailSettings['auth'] = true;
			$mailSettings['username'] = $settings['alertSmtpAuthUser'];
			$mailSettings['password'] = $settings['alertSmtpAuthPass'];
			$mailSettings['port'] = $settings['alertSmtpAuthPort'];
		}
	
		//$settings['alertDevice']

		$mail = Mail::factory("smtp", $mailSettings );

		$headers = array("From"=>$settings['alertEmail'], "Subject"=>$subject);
		$mail->send($settings['alertEmail'], $headers, $body);		

}