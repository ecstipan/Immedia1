<?php
    /*===========================================*
    * 											*
    *  @Title:	Mail Management Source API		*
    *  @Author: Rayce Stipanovich				*
    *  @Rev: 	0.0.1							*
    *  @URL:	solidarity.wpi.edu				*
    * 											*
    *===========================================*/
   
   /*	boolean SendMail(string to, string subject, string body)
    * 		- detects whether using SMTP or PHP mail
    * 		- forms email
    * 		- sends via SMTP or PHP
    * 		- handles SSL authentication
    * 		- returns true on success/false on fail
    * 		- outputs error in debug
    */
    
    global $config;
	if(!defined('ARC')) die('Hax!');
    
    function SendMail($to, $subject = 'No Subject', $body = '', $senderName = "-1", $SenderEmail = "-1"){
    	global $config;
		
    	if(!isset($to)) return false;
		
		//do some cleaning
		$body = trim($body);
		$subject = stripslashes(nl2br(trim($subject)));
		
		//send our message out
		if ($config['email']['mail_smtp']===true){
    		
			//use pear's SMTP mail factory
			require_once "Mail.php";
			
			if ($SenderEmail != "-1" && $senderName != "-1" ) {
				$from = $senderName." <".$SenderEmail.">";
			} else {
    			$from = $config['email']['mail_smtp_from_name']." <".$config['email']['mail_smtp_from_email_address'].">";
			}

			$host = $config['email']['mail_smtp_host'];
			$username = $config['email']['mail_smtp_user'];
			$password = $config['email']['mail_smtp_password'];
			 
			//establish authentication
			if($config['email']['mail_smtp_auth']===true){
				$auth = true;
			}else{
				$auth = false;
			}
			 
			 //are we using ssl?
			if($config['email']['mail_smtp_ssl']===true){
				$host = 'ssl://'.$config['email']['mail_smtp_host'];
				$port = "465";
			}else{
				$host = $config['email']['mail_smtp_host'];
				$port = $config['email']['mail_smtp_port'];
			}
			 
			$headers = array (	'From' => $from,
			   					'To' => $to,
			   					'Subject' => $subject);
			$smtp = Mail::factory('smtp',
				array (	'host' => $host,
						'auth' => $auth,
						'port' => $port,
						'username' => $username,
						'password' => $password));
			 
			$mail = $smtp->send($to, $headers, $body);
			 
			if (PEAR::isError($mail)) {
				echo "Email Failed: ".$mail->getMessage();
				return false;
			}
			
			//everything worked
			return true;
		}else{
    		//use regular mail

			//add some headers
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers .= "From: ".$replyName." <".$replyEmail.">\r\n";
			$headers .= "To: <".$to.">\r\n";
			
			//send the damn thing
			return mail($to, $subject, $body, $headers);
    	}
    }
    
?>