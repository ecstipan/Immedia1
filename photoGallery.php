<?php
    //I know, I know, don't mix the application and presentation layer....
	//this is one page... I can afford to put both in one file.
	//there is also less to explain in terms of layer management
	//wheeeeee
	
	global $config, $db;
	define('ARC', true);
	session_start();
	require_once('./config.php');
	require_once('./inc.php');

	if (!connectDB()) die('SQL Error!');
	
	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');
	echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";
	
	echo '<xml>';
	
echo "
<juiceboxgallery
	useThumbDots=\"true\"
	showThumbsButton=\"false\"
	showExpandButton=\"false\"
	showOpenButton=\"false\"
	galleryTitle=\" \"
>";

$sql="SELECT * FROM {{DB}}.`people` ORDER BY `id` DESC LIMIT 0, 40;";
$result = queryDB($sql);
$i=0;
$c = count($result);
if(!isset($result[0]['id'])) $c=0;
while($i<$c) {
	$name = $result[$i]['name'];
	$desc = $result[$i]['desc'];
	$photourl = $result[$i]['picture'];
			
	echo "
<image imageURL=\"",$photourl,"\" 
	thumbURL=\"",$photourl,"\" 
	linkURL=\"",$photourl,"\" 
	linkTarget=\"_blank\">
	<title>",$name,"</title>
	<caption>",$desc,"</caption>
</image>";
			
		
	$i++;
}

echo "
</juiceboxgallery>
</xml>";

?>