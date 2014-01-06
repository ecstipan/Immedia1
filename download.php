<?php
global $config, $db;
	define('ARC', true);
	session_start();
	require_once('./config.php');
	require_once('./inc.php');

	if (!connectDB()) die('SQL Error!');
	
	if (!isset($_GET['p']) || $_GET['p'] == '') {
		//header('Location: ./');
		exit();
	}
	
	$publicationid = mysql_escape_string(trim(htmlspecialchars_decode($_GET['p'])));
	
	$sql="SELECT * FROM {{DB}}.`publications` WHERE `id` = '".$publicationid."';";
	$result = queryDB($sql);
	if (!isset($result) || !$result[0]['id'] ) {
		die('Failed to find file!');
	}
	
	if(!isset($result[0]['pdf']) || strlen($result[0]['pdf']) <5) {
		die('Failed to find file!');
	}
	
	$path = $result[0]['pdf'];
	$title = str_replace(array(' ', '\'', '.', '"', ','), '', $result[0]['title']);
	
	
	if(!file_exists($path)) {
		die('Failed to find file! ' . $path);
	}
	
	//die($path);
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Transfer-Encoding: binary');
	header('Content-type: application/pdf');
	header('Content-Disposition: attachment; filename="'.$title.".pdf\"");
	
	// The PDF source is in original.pdf
	readfile('/var/www/'.$path);


?>