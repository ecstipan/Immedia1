<?php
	global $config, $db;
	define('ARC', true);
	session_start();
	require_once('./config.php');
	require_once('./inc.php');
	require_once('./recaptchalib.php');
	require_once('./formmail.php');
	$error = false;
	$ErrorMessage = 'An Unknown Error Occured';
	if (!connectDB()) die('SQL Error!');

	//check the captcha before anything else
	
	if (!isset($_POST["recaptcha_challenge_field"])||!isset($_POST["recaptcha_response_field"])||$_POST["recaptcha_response_field"]=='') {
		$error = true;
		$ErrorMessage = 'You have not entered any captcha numbers.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	}

  	$privatekey = $config['captcha_private_key'];
	$resp = recaptcha_check_answer ($privatekey,
	                                $_SERVER["REMOTE_ADDR"],
	                                $_POST["recaptcha_challenge_field"],
	                                $_POST["recaptcha_response_field"]);
	
	if (!$resp->is_valid) {
	    // What happens when the CAPTCHA was entered incorrectly
		$error = true;
		$ErrorMessage = 'You have entered the wrong captcha numbers.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	}
	 

	//so we have validated our captcha,
	//Let's validate the rest of the stuff
	if (!isset($_POST["name"]) || $_POST["name"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid name.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $name = urldecode($_POST["name"]);
	
	if (!isset($_POST["org"]) || $_POST["org"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid org.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $org = urldecode($_POST["org"]);
	
	if (!isset($_POST["phone"]) || $_POST["phone"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid phone.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $phone = urldecode($_POST["phone"]);
	
	if (!isset($_POST["email"]) || $_POST["email"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid email.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $email = urldecode($_POST["email"]);
	
	if (!isset($_POST["dates"]) || $_POST["dates"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid dates.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $dates = urldecode($_POST["dates"]);
	
	if (!isset($_POST["loc"]) || $_POST["loc"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid loc.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $loc = urldecode($_POST["loc"]);
	
	if (!isset($_POST["desc"]) || $_POST["desc"] == "") {
		$error = true;
		$ErrorMessage = 'Enter a valid desc.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $desc = urldecode($_POST["desc"]);
	
	
	//checkboxes
	if (!isset($_POST["check-sound"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid sound.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $sound = ($_POST["check-sound"] === 'true');
	
	if (!isset($_POST["check-lighting"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid lighting.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $lighting = ($_POST["check-lighting"] === 'true');
	
	if (!isset($_POST["check-video"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid video.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $video = ($_POST["check-video"] === 'true');
	
	if (!isset($_POST["check-ac"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid ac.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $ac = ($_POST["check-ac"] === 'true');
	
	if (!isset($_POST["check-heating"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid heating.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $heating = ($_POST["check-heating"] === 'true');
	
	if (!isset($_POST["check-staging"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid staging.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $staging = ($_POST["check-staging"] === 'true');
	
	if (!isset($_POST["check-power"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid power.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $power = ($_POST["check-power"] === 'true');
	
	if (!isset($_POST["check-search"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid search.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $search = ($_POST["check-search"] === 'true');
	
	if (!isset($_POST["check-rental"])) {
		$error = true;
		$ErrorMessage = 'Enter a valid rental.';
		echo json_encode(array('error' 	=> $error, 'message' 	=> $ErrorMessage));
		exit();
	} else $rental = ($_POST["check-rental"] === 'true');
	
	//generate our email string
	$EmailToSend = "Hello,
	
Someone has submitted an event via the web form.  The details are as follows:
	
Name: ".$name."
Organization: ".$org."
Phone: ".$phone."
Email: ".$email."
Event Date(s): ".$dates."
	
Location: ".$loc."
	
Description: ".$desc."
	
Requirements:
Sound: ".(($sound) ? 'YES' : 'NO')."
Video: ".(($video) ? 'YES' : 'NO')."
Lighting: ".(($lighting) ? 'YES' : 'NO')."
AC: ".(($ac) ? 'YES' : 'NO')."
Heating: ".(($heating) ? 'YES' : 'NO')."
Staging: ".(($staging) ? 'YES' : 'NO')."
Searchlights: ".(($search) ? 'YES' : 'NO')."
Power: ".(($power) ? 'YES' : 'NO')."
Rental: ".(($rental) ? 'YES' : 'NO')."
	
	
	";
	$statusSubject = 'New event request for '.$dates." from ".$name;
	
	
	//so now we're validated, let's figure out who to email.
	$emailArray = array();
	
	$sql="SELECT * FROM {{DB}}.`mail`;";
	$result = queryDB($sql);
	
	if ($result && count($result) > 0) {
		//so we catually have stuff
		$temp = $result[0]['alwaysnotify'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					$emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['sound'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($sound) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['lighting'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($lighting) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['video'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($video) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['ac'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($ac) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['heating'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($heating) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['staging'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($staging) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['power'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($power) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['search'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($search) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
		$temp = $result[0]['rental'];
		if ($temp != '' && strpos($temp, ',') > 0) {
			$sectionArray = explode(',', str_replace(' ', '', $temp));
			if ( count($sectionArray) > 0) {
				foreach ($sectionArray as $key => $value) {
					if ($rental) $emailArray[$value] = true;
				}
			}
		} else if (str_replace(' ', '', $temp) != '') $emailArray[str_replace(' ', '', $temp)] = true;
	}

	if ( count($emailArray) > 0) {
		foreach ($emailArray as $adminemail => $value) {
			SendMail($adminemail, $statusSubject, $EmailToSend);
		}
	}
	
	//Send a thank you, message recipt
	SendMail($email, 'Thank you for contacting us!', "Hello ".$name.",
	
	Thank you for contacting us for your event on ".$dates.".  We'll get back to you as soon as we can.  Feel free to contact us at sales@immedia1.com or at 1-800-874-3337 with any questions or concerns.
	
Regards,
The Immedia Staff");
	
	//exit without a hitch
	echo json_encode(array('error' => false, 'success' => true));
?>