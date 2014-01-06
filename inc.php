<?php
    if(!defined('ARC')) die('Hax!');
	
	function connectDB(){
		global $config, $db;
		
		//check if we had PDO enabled
		if (!defined('PDO::ATTR_DRIVER_NAME')) {
			return false;
		}
		
		//start pulling our data from the config
		$host = $config['database']['host'];
		$dbname = $config['database']['database'];
		$user = $config['database']['username'];
		$password = $config['database']['password'];
	
		//start our dsn string
		$dsn = 'mysql';
		
		//connection options array
		$options = array();
		
		//configure PDO for each driver
											//MySQL
		$dsn .= ':host='.$host.';dbname='.$dbname;
	
		if (isset($coreSettings['database']['port'])) 
			$dsn .= ';port='.$coreSettings['database']['port'];

		//finally we try and connect
		try {
			$db = new PDO($dsn, $user, $password, $options);
		} catch (PDOException $e) {
			return false;
		}
		
		//everything checnks out
		return true;
	}

	function disconnectDB(){
		global $config, $db;
		
		//self explanitory
		if (isset($db) && $db)
		$db = null;
	}
	
	function queryDB($qstring){
		global $config, $db;
		
		//We can't wuery anyhting if we dont' have a PDO object
		if (!isset($db) || !$db) return false;
		if ( !$db->getAttribute(PDO::ATTR_CONNECTION_STATUS)) return false;
		
		//clean up our stuff
		if ($qstring==='') return false;
		$qstring = trim($qstring);
		$qstring = str_replace("{{DB}}", "`".$config['database']['database']."`", $qstring);
		
		//prepare a cached statement
		$stmt = $db->prepare($qstring);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		
		//execute the query
		if (!$stmt->execute()) return false;
		
		//grab our data
		$result = $stmt->fetchAll();
		
		//clear our resources
		$stmt->closeCursor();
		
		//output what we have
		if($result) return $result;
		return true;
	}
	
	function lastInsertDB(){
		global $config, $db;
		
		//We can't wuery anyhting if we dont' have a PDO object
		if (!isset($db) || !$db) return false;
		if ( !$db->getAttribute(PDO::ATTR_CONNECTION_STATUS)) return false;
		
		//poll our object for our last id
		return $db->lastInsertId(); 
	}
	
	function isLoggedIn() {
		global $config, $db;
		//see if our session is set
		if (!isset($_SESSION['admin'])) return false;
		return true;
	}
	
	function login($password) {
		global $config, $db;
		if (isLoggedIn()) return true;
		$password = md5(mysql_escape_string(trim(urldecode($password))));
		$sql = "SELECT `value` FROM {{DB}}.`settings` WHERE `name` = 'admin_password';";
		$result = queryDB($sql);
		if (!$result || !$result[0]['value']) return false;
		
		if ($result[0]['value'] != $password) return false;
		
		session_start();
		$_SESSION['admin'] = true;
		return true;
    }
	
	function logout() {
		global $config, $db;
		$_SESSION['admin'] = false;
		session_destroy();
	}
	
	function updatePage($page, $rawcontent) {
		global $config, $db;
		if (!isLoggedIn()) {
			header('Location: ../');
			return false;
		}
		if (!isset($page)||strlen($page)<4) return false;
		$page = htmlspecialchars(mysql_escape_string(trim($page)));
		if (!isset($rawcontent)) return false;
		$content = htmlspecialchars(mysql_escape_string(trim($rawcontent)));
		$sql = "UPDATE {{DB}}.`pages` SET `content`= '".$content."' WHERE `name` = '".$page."';";
		$result = queryDB($sql);
		if (!$result) return false;
		return true;
	}

	function printPage($page, $usedate = true) {
		$html = '';
		$page = strtolower(mysql_escape_string(trim($page)));
		$sql = "SELECT `content`,`updated` FROM {{DB}}.`pages` WHERE `name` = '".$page."';";
		$result = queryDB($sql);
		if (!$result || !$result[0]['content']) {
			echo 'Failed to load page.';
			return false;
		}
		
		$html = htmlspecialchars_decode($result[0]['content']);
		echo $html;
	}
	
	
	function editProject($id) {
		global $config, $db;
		if (!isLoggedIn()) header('Location: ../');
		
	}
	
	function deleteProject() {
		global $config, $db;
		if (!isLoggedIn()) header('Location: ../');
		
	}
	
	function addPublication() {
		global $config, $db;
		if (!isLoggedIn()) header('Location: ../');
		
	}
	
	function editPublication($id) {
		global $config, $db;
		if (!isLoggedIn()) header('Location: ../');
		
	}
	
	function deletePublication() {
		global $config, $db;
		if (!isLoggedIn()) header('Location: ../');
		
	}
	
	function updateSetting($setting, $value) {
		global $config, $db;
		if (!isLoggedIn()) header('Location: ../');
		if (!isset($setting)||$setting=='') return false;
		if (!isset($value)) return false;
		$value = mysql_escape_string(trim($value));
		$sql = "UPDATE {{DB}}.`settings` SET `value`= '".$value."' WHERE `name` = '".$setting."';";
		$result = queryDB($sql);
		if (!$result) return false;
		return true;
	}
	
	function getSetting($setting) {
		global $config, $db;
		if (!isset($setting)||$setting=='') return false;
		$sql = "SELECT `value` FROM {{DB}}.`settings` WHERE `name` = '".$setting."';";
		$result = queryDB($sql);
		if (!$result||!isset($result[0]['value'])) return false;
		echo htmlspecialchars_decode($result[0]['value']);
	}
?>