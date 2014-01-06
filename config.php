<?php
	global $config, $db;
	if(!defined('ARC')) die('Hax!');
	$config = Array();
	$config['database'] = Array();
	$config['email'] = Array();
	
	//BEGIN SETTINGS CONFIGURATION//
	
	//  DATABASES  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$config['database']['host'] 						= 'localhost';										//Our database is on the same server <-otherwise use IP
	$config['database']['username'] 					= '';								//MySQL Username				
	$config['database']['password'] 					= '';										//MySQL Password
	$config['database']['database'] 					= '';									//What database are we using?
	
	//  UPLOADS  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$config['upload_dir'] 								= "../uploads/images/";								//Folder on the server to store uploaded images.
	$config['images_url']								= "http://collablab.wpi.edu/sean/uploads/images/";	//Web URL of the images folder.
	
	//  EMAIL  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$config['email']['mail_smtp']						= true;												//Use SMTP to send mail?
	$config['email']['mail_smtp_host'] 					= "";								//SMTP Server Address
	$config['email']['mail_smtp_from_name']				= "Immedia";										//Name that shows up on contact list (If none specified)
	$config['email']['mail_smtp_from_email_address'] 	= "";						//Return Email Address (If none speficied)
	$config['email']['mail_smtp_user'] 					= "";								//SMTP User Account
	$config['email']['mail_smtp_password'] 				= "";									//SMTP Password
	$config['email']['mail_smtp_port']					= 587;												//Port to run SMTP over
	$config['email']['mail_smtp_ssl']					= false;											//Are we using SSL encryption?
	$config['email']['mail_smtp_auth']					= true;												//Do we even need to authenticate on thie server?
	
	//  CAPTCHA  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$config['captcha_public_key']						= '6LcNJ-sSAAAAAPfNS5EiAvO-qySIuUCHU3wuyiVa';		//ReCAPTCHA Public Key
	$config['captcha_private_key']						= '6LcNJ-sSAAAAAGHnjXOwLY2qXwgmf_qaPFtRLyWp';		//ReCAPTCHA Public Key
	
	
	//END OF SETTINGS CONFIGURATION//
?>