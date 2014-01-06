<?php
	ini_set('upload_max_filesize', '100M');
	ini_set('post_max_size', '100M');
	ini_set('max_input_time', 120);

	//phpinfo();
	//exit();

	global $config, $db;
	define('ARC', true);
	require_once('../config.php');
	require_once('../inc.php');
    session_start();
	if (!connectDB()) die('SQL Error!');
    
	//we're doing all of this in one file... yay for doing things quickly and dirty
	//No, I don't develop like this all the time, but this is stable and simple
	//there's no need for my cdn or URI mapper here... maybe in later revisions
		
	
	if (!isLoggedIn() && !isset($_POST['password'])) {
		//echo lovin screen
		
		$etext = 'false';
		if(isset($_GET['badpassword']) ) $etext = 'true';
		
		echo '
		<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
	<script type="text/javascript">
		var error = ',$etext,';
		$(function() {
			$("#failedpassword").hide();
			$("#passwordinput").focus();
			if (error) {
				$("#failedpassword").show();
				setTimeout(function () {
					$("#failedpassword").fadeOut("fast");
				}, 1000);
			}
		});
	</script>
	<div id="bodydiv">
		<div id="failedpassword">
			<center>Invalid Password!</center>
		</div>
		<div id="loginbox">
			<form name="login" action="" method="POST">
				<h1>Restricted Area</h1>
				<h2>This area is password-protected.</h2>
				<input id="passwordinput" type="password" name="password" />
				<input type="hidden" name="action" value="login" />
				<input id="login_button" name="login" type="submit" value="Enter">
			</form>
		</div>
	</div>
</body>
</html>
		';
		
		exit();
	}
	
	if (isLoggedIn() && ((!isset($_POST['action']) || $_POST['action'] == '') && (!isset($_GET['action']) || $_GET['action'] == ''))) {
		//echo admin terminal
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
	<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$("#link_home").click(function(){
				window.location ="../";
			});
			$("#link_updatehome").click(function(){
				window.location ="./?action=edithome";
			});
			
			$("#link_lighting").click(function(){
				window.location ="./?action=editlighting";
			});
			$("#link_sound").click(function(){
				window.location ="./?action=editsound";
			});
			$("#link_power").click(function(){
				window.location ="./?action=editpower";
			});
			$("#link_sl").click(function(){
				window.location ="./?action=editsl";
			});
			$("#link_cc").click(function(){
				window.location ="./?action=editcc";
			});
			$("#link_video").click(function(){
				window.location ="./?action=editvideo";
			});
			
			
			$("#link_contact").click(function(){
				window.location ="./?action=editcontact";
			});
			$("#link_photo").click(function(){
				window.location ="./?action=coverphoto";
			});
			$("#link_password").click(function(){
				window.location ="./?action=password";
			});
			$("#link_mail").click(function(){
				window.location ="./?action=mail";
			});
			$("#link-add-project").click(function(){
				window.location ="./?action=addproject";
			});
			$("#link-edit-project").click(function(){
				$("#editprojsel").fadeIn();
			});
			
			$("#link-add-software").click(function(){
				window.location ="./?action=addsoftware";
			});
			$("#link-edit-software").click(function(){
				$("#editsoftwaresel").fadeIn();
			});
			
			$("#link-add-people").click(function(){
				window.location ="./?action=addz";
			});
			$("#link-edit-people").click(function(){
				$("#editzsel").fadeIn();
			});
			
			$("#link-add-publication").click(function(){
				window.location ="./?action=addpublication";
			});
			$("#link-edit-publication").click(function(){
				$("#editpubsel").fadeIn();
			});
			$("#editprojsel").hide();
			
			$("#ep_c").click(function(){
				$("#editprojsel").hide();
				return false;
			});
			$("#ep_edit").click(function(){
				var proj = $("#edit_P_sel").val();
				window.location = "./?action=editproject&p="+proj;
				return false;
			});
			$("#ep_del").click(function(){
				if(confirm("Are you sure you want to delete this?")){
					var proj = $("#edit_P_sel").val();
					window.location = "./?action=delproject&p="+proj;
				}
				return false;
			});
			$("#editsoftwaresel").hide();
			
			//software
			$("#es_c").click(function(){
				$("#editsoftwaresel").hide();
				return false;
			});
			$("#es_edit").click(function(){
				var proj = $("#edit_s_sel").val();
				window.location = "./?action=editsoftware&p="+proj;
				return false;
			});
			$("#es_del").click(function(){
				if(confirm("Are you sure you want to delete this?")){
					var proj = $("#edit_s_sel").val();
					window.location = "./?action=delsoftware&p="+proj;
				}
				return false;
			});
			
			//software
			$("#eu_c").click(function(){
				$("#editpubsel").hide();
				return false;
			});
			$("#eu_edit").click(function(){
				var proj = $("#edit_u_sel").val();
				window.location = "./?action=editpub&p="+proj;
				return false;
			});
			$("#eu_del").click(function(){
				if(confirm("Are you sure you want to delete this?")){
					var proj = $("#edit_u_sel").val();
					window.location = "./?action=delpub&p="+proj;
				}
				return false;
			});
			$("#editpubsel").hide();
			
			//software
			$("#ez_c").click(function(){
				$("#editzsel").hide();
				return false;
			});
			$("#ez_edit").click(function(){
				var proj = $("#edit_z_sel").val();
				window.location = "./?action=editz&p="+proj;
				return false;
			});
			$("#ez_del").click(function(){
				if(confirm("Are you sure you want to delete this?")){
					var proj = $("#edit_z_sel").val();
					window.location = "./?action=delz&p="+proj;
				}
				return false;
			});
			$("#editzsel").hide();
			
			$("#link_o_pub").click(function(){
				$("#order_pub_panel").fadeIn();
			});
			$("#pub_sortable_c").click(function(){
				$("#order_pub_panel").hide();
				return false;
			});
			//pub_sortable
			$( "#pub_sortable" ).sortable({
				update: function(){
					$.ajax({
					    type: "POST",
					    dataType: "json",
					    data: "action=changepubgroups&"+$(this).sortable("serialize"),
					    beforeSend: function(x) {
					        if(x && x.overrideMimeType) {
					            x.overrideMimeType("application/json;charset=UTF-8");
					        }
					    },
					    url: "./?action=changepubgroups",
					    success: function(data) {
					        //alert(data);
					        if (data.success == true){
					          
					        } else{
					        	alert("Failed to update order.");
					        }
					    },
					    error: function(data) {
					    	alert("Failed to update order.");
					    }
					});
				}
			});
			$( "#pub_sortable" ).disableSelection();
			$("#order_pub_panel").hide();
			
			$("#link_o_p").click(function(){
				$("#order_p_panel").fadeIn();
			});
			$("#p_sortable_c").click(function(){
				$("#order_p_panel").hide();
				return false;
			});
			//pub_sortable
			$( "#p_sortable" ).sortable({
				update: function(){
					$.ajax({
					    type: "POST",
					    dataType: "json",
					    data: "action=changepgroups&"+$(this).sortable("serialize"),
					    beforeSend: function(x) {
					        if(x && x.overrideMimeType) {
					            x.overrideMimeType("application/json;charset=UTF-8");
					        }
					    },
					    url: "./?action=changepgroups",
					    success: function(data) {
					        //alert(data);
					        if (data.success == true){
					          
					        } else{
					        	alert("Failed to update order.");
					        }
					    },
					    error: function(data) {
					    	alert("Failed to update order.");
					    }
					});
				}
			});
			$( "#p_sortable" ).disableSelection();
			$("#order_p_panel").hide();
			
			$("#link_mo_p").click(function(){
				$("#m_order_pub_panel").fadeIn();
			});
			$("#m_order_pub_panel").hide();
			m_pub_sortable_c
			$("#m_pub_sortable_c").click(function(){
				$("#m_order_pub_panel").hide();
				return false;
			});
			$("#m_pub_sortable_s").click(function(){
				window.location = "./?action=manorderpub&group="+$("#m_pub_group_sel").val();
				return false;
			});
			
			$("#link_mo_s").click(function(){
				window.location = "./?action=manordersof";
				return false;
			});
			
			$("#link_mo_r").click(function(){
				window.location = "./?action=manorderproj";
				return false;
			});
		});
	</script>	
	<div id="adminbodydiv">
		<div id="admin_panel">
			<div id="link_home" class="admin_panel_link" rel="../"><img src="../images/icons/home_back_32.png" width="32" height="32" /><p>View Site</p></div>
			<div id="link_updatehome" class="admin_panel_link"><img src="../images/icons/home_32.png" width="32" height="32" /><p>Update Home Page</p></div>
			<div id="link_contact" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update News</p></div>
			
			<div id="link-add-project" class="admin_panel_link"><img src="../images/icons/folder_32.png" width="32" height="32" /><p>Add Sale Item</p></div>
			<div id="link-edit-project" class="admin_panel_link"><img src="../images/icons/folder_page_32.png" width="32" height="32" /><p>Edit Sale Item</p></div>
			<div id="link_mo_r" class="admin_panel_link"><img src="../images/icons/folder_page_32.png" width="32" height="32" /><p>Order Sale Items</p></div>
	
			<div id="link-add-software" class="admin_panel_link"><img src="../images/icons/activity_monitor_add.png" width="32" height="32" /><p>Add Background</p></div>
			<div id="link-edit-software" class="admin_panel_link"><img src="../images/icons/activity_monitor.png" width="32" height="32" /><p>Delete Background</p></div>
			<div id="link_mo_s" class="admin_panel_link"><img src="../images/icons/activity_monitor.png" width="32" height="32" /><p>Order Backgrounds</p></div>
			
			<div id="link-add-people" class="admin_panel_link"><img src="../images/icons/camera_32.png" width="32" height="32" /><p>Add Gallery Photo</p></div>
			<div id="link-edit-people" class="admin_panel_link"><img src="../images/icons/camera_32.png" width="32" height="32" /><p>Edit Gallery</p></div>
			<div id="link_password" class="admin_panel_link"><img src="../images/icons/lock_32.png" width="32" height="32" /><p>Change Password</p></div>
			
			<div id="link_lighting" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update Lighting</p></div>
			<div id="link_sound" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update Sound</p></div>
			<div id="link_power" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update Power</p></div>
			
			<div id="link_sl" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update Searchlights</p></div>
			<div id="link_cc" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update Climate Control</p></div>
			<div id="link_video" class="admin_panel_link"><img src="../images/icons/newspaper_add_32.png" width="32" height="32" /><p>Update Video</p></div>
			
			<div id="link_mail" class="admin_panel_link"><img src="../images/icons/activity_monitor.png" width="32" height="32" /><p>Email Lists</p></div>
			
			
		</div>     
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
	
	<div id="m_order_pub_panel">
		<h1>Select a group to edit.</h1><br>
		<select name="group" id="m_pub_group_sel" style="width:500px;">
			';
			
			$sql="SELECT * FROM {{DB}}.`publications` GROUP BY `group`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['group'];
	
				echo '<option value="',$title,'">',$title,'</option>
				';
				$i++;
			}
			echo '
		</select><br><br>
		<button id="m_pub_sortable_s">Order This Group</button>
		<button id="m_pub_sortable_c">Close</button>
	</div>
	
	<div id="order_pub_panel">
		<h1>Drag to order groups.</h1><br>
		<ul id="pub_sortable">
			';
			
			$sql="SELECT * FROM {{DB}}.`publications` GROUP BY `group` ORDER BY `order`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['group'];
	
				echo '<li class="sorting" id="sortpub_',$title,'">',$title,'</li>
				';
				$i++;
			}
			echo '
		</ul>
		<button id="pub_sortable_c">Close</button>
	</div>
	<div id="order_p_panel">
		<h1>Drag to order groups.</h1><br>
		<ul id="p_sortable">
			';
			
			$sql="SELECT * FROM {{DB}}.`people` GROUP BY `group` ORDER BY `order`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['group'];
	
				echo '<li class="sorting" id="sortp_',$title,'">',$title,'</li>
				';
				$i++;
			}
			echo '
		</ul>
		<button id="p_sortable_c">Close</button>
	</div>
	<div id="editprojsel">
		<h1>Select a Sale Item to edit or delete.</h1><br>
		
		<select id="edit_P_sel" name="nothing">
			';
			
			$sql="SELECT * FROM {{DB}}.`projects`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['title'];
				$id = $result[$i]['id'];
	
				echo '<option id="v_',$id,'" value="',$id,'">',$title,'</option>
				';
								
		
				$i++;
			}
			echo '
		</select><Br><Br>
		<button id="ep_edit">Edit</button>
		<button id="ep_del">Delete</button>
		<button id="ep_c">Cancel</button>
	</div>
	
	<div id="editsoftwaresel">
		<h1>Select background to delete.</h1><br>
		
		<select id="edit_s_sel" name="nothing">
			';
			
			$sql="SELECT * FROM {{DB}}.`software`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['title'];
				$id = $result[$i]['id'];
	
				echo '<option id="v_',$id,'" value="',$id,'">',$title,'</option>
				';
								
		
				$i++;
			}
			echo '
		</select><Br><Br>
		<button id="es_del">Delete</button>
		<button id="es_c">Cancel</button>
	</div>
	<div id="editpubsel">
		<h1>Select a publication to edit or delete.</h1><br>
		
		<select id="edit_u_sel" name="nothing">
			';
			
			$sql="SELECT * FROM {{DB}}.`publications`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['title'];
				$id = $result[$i]['id'];
	
				echo '<option id="v_',$id,'" value="',$id,'">',$title,'</option>
				';
								
		
				$i++;
			}
			echo '
		</select><Br><Br>
		<button id="eu_edit">Edit</button>
		<button id="eu_del">Delete</button>
		<button id="eu_c">Cancel</button>
	</div>
	<div id="editzsel">
		<h1>Select a person to edit or delete.</h1><br>
		
		<select id="edit_z_sel" name="nothing">
			';
			
			$sql="SELECT * FROM {{DB}}.`people`;";
			$result = queryDB($sql);
			
			$i=0;
			$c = count($result);
			while($i<$c) {
				$title = $result[$i]['name'];
				$id = $result[$i]['id'];
	
				echo '<option id="v_',$id,'" value="',$id,'">',$title,'</option>
				';
								
		
				$i++;
			}
			echo '
		</select><Br><Br>
		<button id="ez_edit">Edit</button>
		<button id="ez_del">Delete</button>
		<button id="ez_c">Cancel</button>
	</div>
	<Br><br>
</body>
</html>';
		exit();
	}
	$action = '';
	if (isset($_POST['action'])) $action = mysql_escape_string(trim(urldecode($_POST['action'])));
	if($action=='' && isset($_GET['action'])) $action = mysql_escape_string(trim(urldecode($_GET['action'])));
	
	if ($action == 'logout') {
		echo 'Logging out...';
		logout();
		header('Location: ../');
	} else if ($action == 'login') {
		if (!isset($_POST['password']) || $_POST['password'] == '') header('Location: ?badpassword');
		if (!login($_POST['password'])) header('Location: ?badpassword');
		else header('Location: ./');
	} else if ($action == 'manorderpub') {
		if (!isset($_GET['group']) || $_GET['group'] == '') header('Location: ./');
		
		$group = trim(mysql_escape_string(htmlspecialchars_decode($_GET['group'])));
		
		//see if we exist
		$sql = "SELECT * FROM `publications` WHERE `group` = '".$group."' LIMIT 0, 1;";
		$result = queryDB($sql);
		if (!$result || !isset($result[0]['id'])) header('Location: ./');
		
		//we exist
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#savebutton\').click(function(){
				window.location = "./";
				return false;
			});
			
			$( "#man_sortable" ).sortable({
				update: function(){
					var d = "action=man_order_pub&group='.$group.'&"+$(this).sortable("serialize", {"key": "sort[]"});
					//alert(d);
					$.ajax({
					    type: "POST",
					    dataType: "json",
					    data: d,
					    beforeSend: function(x) {
					        if(x && x.overrideMimeType) {
					            x.overrideMimeType("application/json;charset=UTF-8");
					        }
					    },
					    url: "./?action=man_order_pub",
					    success: function(data) {
					        //alert(data);
					        if (data.success == true){
					          
					        } else{
					        	alert("Failed to update order. - "+data.e);
					        }
					    },
					    error: function(data) {
					    	alert("Failed to update order. - Could not sync!");
					    }
					});
				}
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing '.$group.'\'s Order: <button id="savebutton">Back</button>
			<br><br><center>
			<ul id="man_sortable">
				';
				
				$sql="SELECT * FROM {{DB}}.`publications` WHERE `group` = '".$group."' ORDER BY `manual_order`;";
				$result = queryDB($sql);
				
				$i=0;
				$c = count($result);
				while($i<$c) {
					$title = $result[$i]['title'];
					$id= $result[$i]['id'];
					echo '<li class="sorting" id="sort_',$id,'">',$title,'</li>
					';
					$i++;
				}
				echo '
			</ul>
			</center>
			<br>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	//man_order_pub
	} else if ($action == 'man_order_pub' ) {
		if (!isset($_POST['sort']) || $_POST['sort'] == '') {
			echo json_encode(Array('merror' => true, 'e' => 'Missing serialized array.'));
			return;
		}
		if (!isset($_POST['group']) || $_POST['group'] == '') {
			echo json_encode(Array('merror' => true, 'e' => 'Missing group id.'));
			return;
		}
		
		$group = trim(mysql_escape_string(htmlspecialchars_decode($_POST['group'])));
		
		//see if we exist
		$sql = "SELECT * FROM `publications` WHERE `group` = '".$group."' LIMIT 0, 1;";
		$result = queryDB($sql);
		if (!$result || !isset($result[0]['id']))  {
			echo json_encode(Array('merror' => true, 'e' => 'could not find group.'));
			return;
		}
		
		
		$i=0;
		foreach($_POST['sort'] as $key=>$value) {
			//mysql_query("UPDATE my_items" SET position = '" . $key . "' WHERE id ='" . $value . "'");
			$id = mysql_escape_string(htmlspecialchars_decode($value));
			$sql = "UPDATE {{DB}}.`publications` SET
			`manual_order` = '".$i."' 
			WHERE `id` = '".$id."' 
			AND `group` = '".$group."';";
			
			$result = queryDB($sql);
			if (!$result) {
				echo json_encode(Array('merror' => true, 'e' => 'Failed to update item.'));
				return;
			}
			
			$i++;
		}
			
		echo json_encode(Array('success' => true));
		return;
		
	} else if ($action == 'manordersof') {
		//we exist
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#savebutton\').click(function(){
				window.location = "./";
				return false;
			});
			
			$( "#man_sortable" ).sortable({
				update: function(){
					var d = "action=man_order_sof&"+$(this).sortable("serialize", {"key": "sort[]"});
					//alert(d);
					$.ajax({
					    type: "POST",
					    dataType: "json",
					    data: d,
					    beforeSend: function(x) {
					        if(x && x.overrideMimeType) {
					            x.overrideMimeType("application/json;charset=UTF-8");
					        }
					    },
					    url: "./?action=man_order_sof",
					    success: function(data) {
					        //alert(data);
					        if (data.success == true){
					          
					        } else{
					        	alert("Failed to update order. - "+data.e);
					        }
					    },
					    error: function(data) {
					    	alert("Failed to update order. - Could not sync!");
					    }
					});
				}
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Background Images Order: <button id="savebutton">Back</button>
			<br><br><center>
			<ul id="man_sortable">
				';
				
				$sql="SELECT * FROM {{DB}}.`software` ORDER BY `manual_order`;";
				$result = queryDB($sql);
				
				$i=0;
				$c = count($result);
				while($i<$c) {
					$title = $result[$i]['title'];
					$id= $result[$i]['id'];
					echo '<li class="sorting" id="sort_',$id,'">',$title,'</li>
					';
					$i++;
				}
				echo '
			</ul>
			</center>
			<br>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	//mail stuff
	} else if ($action == 'mail') {
		//we exist
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Edit Email Lists <button id="savebutton">Save</button>
			<form id="cureditform" method="POST" action="">';

				$sql="SELECT * FROM {{DB}}.`mail`;";
				$result = queryDB($sql);
				
				echo '
				<form name="edithome" id="cureditform" action="./" method="POST">
					<br><label style="font-size:16px;">Seperate emails with comma.</label>
					<br><label style="font-size:16px;">Send all to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="alwaysnotify" value="'.$result[0]['alwaysnotify'].'">
					<br><label style="font-size:16px;">Send all sound-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="sound" value="'.$result[0]['sound'].'">
					<br><label style="font-size:16px;">Send all lighting-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="lighting" value="'.$result[0]['lighting'].'">
					<br><label style="font-size:16px;">Send all video-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="video" value="'.$result[0]['video'].'">
					<br><label style="font-size:16px;">Send all ac-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="ac" value="'.$result[0]['ac'].'">
					<br><label style="font-size:16px;">Send all heating-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="heating" value="'.$result[0]['heating'].'">
					<br><label style="font-size:16px;">Send all staging-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="staging" value="'.$result[0]['staging'].'">
					<br><label style="font-size:16px;">Send all searchlights-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="search" value="'.$result[0]['search'].'">
					<br><label style="font-size:16px;">Send all power-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="power" value="'.$result[0]['power'].'">
					<br><label style="font-size:16px;">Send all rental-related to:</label>
					<br><input style="font-size:16px;width:400px;" type="text" name="rental" value="'.$result[0]['rental'].'">
					
					
					<input type="hidden" name="action" value="saveemail">
				</form>';
				
			echo '</form>
		</div>
	</div>
	<br>
	<br>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'saveemail' ) {
		echo "saving...";
		$alwaysnotify = (isset($_POST['alwaysnotify']) ? mysql_escape_string(trim(urldecode($_POST['alwaysnotify']))) : '');
		$sound = (isset($_POST['sound']) ? mysql_escape_string(trim(urldecode($_POST['sound']))) : '');
		$lighting = (isset($_POST['lighting']) ? mysql_escape_string(trim(urldecode($_POST['lighting']))) : '');
		$video = (isset($_POST['video']) ? mysql_escape_string(trim(urldecode($_POST['video']))) : '');
		$ac = (isset($_POST['ac']) ? mysql_escape_string(trim(urldecode($_POST['ac']))) : '');
		$heating = (isset($_POST['heating']) ? mysql_escape_string(trim(urldecode($_POST['heating']))) : '');
		$staging = (isset($_POST['staging']) ? mysql_escape_string(trim(urldecode($_POST['staging']))) : '');
		$search = (isset($_POST['search']) ? mysql_escape_string(trim(urldecode($_POST['search']))) : '');
		$power = (isset($_POST['power']) ? mysql_escape_string(trim(urldecode($_POST['power']))) : '');
		$rental = (isset($_POST['rental']) ? mysql_escape_string(trim(urldecode($_POST['rental']))) : '');

		$sql="UPDATE {{DB}}.`mail` SET 
		`alwaysnotify` = '".$alwaysnotify."',
		`sound` = '".$sound."',
		`lighting` = '".$lighting."',
		`video` = '".$video."',
		`ac` = '".$ac."',
		`heating` = '".$heating."',
		`staging` = '".$staging."',
		`search` = '".$search."',
		`power` = '".$power."',
		`rental` = '".$rental."'
		;";
		
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?action=mail');
			die();
		}

		header('Location: ./');
		die();

	//man_order_pub
	} else if ($action == 'man_order_sof' ) {
		if (!isset($_POST['sort']) || $_POST['sort'] == '') {
			echo json_encode(Array('merror' => true, 'e' => 'Missing serialized array.'));
			return;
		}
		
		$i=0;
		foreach($_POST['sort'] as $key=>$value) {
			//mysql_query("UPDATE my_items" SET position = '" . $key . "' WHERE id ='" . $value . "'");
			$id = mysql_escape_string(htmlspecialchars_decode($value));
			$sql = "UPDATE {{DB}}.`software` SET
			`manual_order` = '".$i."' 
			WHERE `id` = '".$id."';";
			
			$result = queryDB($sql);
			if (!$result) {
				echo json_encode(Array('merror' => true, 'e' => 'Failed to update item.'));
				return;
			}
			
			$i++;
		}
			
		echo json_encode(Array('success' => true));
		return;
		
	} else if ($action == 'manorderproj') {
		//we exist
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#savebutton\').click(function(){
				window.location = "./";
				return false;
			});
			
			$( "#man_sortable" ).sortable({
				update: function(){
					var d = "action=man_order_proj&"+$(this).sortable("serialize", {"key": "sort[]"});
					//alert(d);
					$.ajax({
					    type: "POST",
					    dataType: "json",
					    data: d,
					    beforeSend: function(x) {
					        if(x && x.overrideMimeType) {
					            x.overrideMimeType("application/json;charset=UTF-8");
					        }
					    },
					    url: "./?action=man_order_proj",
					    success: function(data) {
					        //alert(data);
					        if (data.success == true){
					          
					        } else{
					        	alert("Failed to update order. - "+data.e);
					        }
					    },
					    error: function(data) {
					    	alert("Failed to update order. - Could not sync!");
					    }
					});
				}
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Project\'s Order: <button id="savebutton">Back</button>
			<br><br><center>
			<ul id="man_sortable">
				';
				
				$sql="SELECT * FROM {{DB}}.`projects` ORDER BY `manual_order`;";
				$result = queryDB($sql);
				
				$i=0;
				$c = count($result);
				while($i<$c) {
					$title = $result[$i]['title'];
					$id= $result[$i]['id'];
					echo '<li class="sorting" id="sort_',$id,'">',$title,'</li>
					';
					$i++;
				}
				echo '
			</ul>
			</center>
			<br>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	//man_order_pub
	} else if ($action == 'man_order_proj' ) {
		if (!isset($_POST['sort']) || $_POST['sort'] == '') {
			echo json_encode(Array('merror' => true, 'e' => 'Missing serialized array.'));
			return;
		}
		
		$i=0;
		foreach($_POST['sort'] as $key=>$value) {
			//mysql_query("UPDATE my_items" SET position = '" . $key . "' WHERE id ='" . $value . "'");
			$id = mysql_escape_string(htmlspecialchars_decode($value));
			$sql = "UPDATE {{DB}}.`projects` SET
			`manual_order` = '".$i."' 
			WHERE `id` = '".$id."';";
			
			$result = queryDB($sql);
			if (!$result) {
				echo json_encode(Array('merror' => true, 'e' => 'Failed to update item.'));
				return;
			}
			
			$i++;
		}
			
		echo json_encode(Array('success' => true));
		return;
	
	} else if ($action == 'changepubgroups' ) {
		if (!isset($_POST['sortpub']) || $_POST['sortpub'] == '') {
			echo json_encode(Array('error' => true));
			
		}
		$i=0;
		foreach($_POST['sortpub'] as $key=>$value) {
			//mysql_query("UPDATE my_items" SET position = '" . $key . "' WHERE id ='" . $value . "'");
			$group = mysql_escape_string(htmlspecialchars_decode($value));
			$sql = "UPDATE {{DB}}.`publications` SET
			`order` = '".$i."' 
			WHERE `group` = '".$group."';";
			
			$result = queryDB($sql);
			if (!$result) {
				echo json_encode(Array('error' => true));
				return;
			}
			
			$i++;
		}
			
		echo json_encode(Array('success' => true));
		return;
	} else if ($action == 'changepgroups' ) {
		if (!isset($_POST['sortp']) || $_POST['sortp'] == '') {
			echo json_encode(Array('error' => true));
			return;
		}
		$i=0;
		foreach($_POST['sortp'] as $key=>$value) {
			//mysql_query("UPDATE my_items" SET position = '" . $key . "' WHERE id ='" . $value . "'");
			$group = trim(mysql_escape_string(htmlspecialchars_decode($value)));
			$sql = "UPDATE {{DB}}.`people` SET
			`order` = '".$i."' 
			WHERE `group` = '".$group."';";
			
			$result = queryDB($sql);
			if (!$result) {
				echo json_encode(Array('error' => true));
				return;
			}
			
			$i++;
		}
			
		echo json_encode(Array('success' => true));
		return;
	} else if ($action == 'edithome' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Homepage: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savehomepage">
				<textarea name="data" id="redactor_content">',printPage('home',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'editlighting' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Lighting Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savelighting">
				<textarea name="data" id="redactor_content">',printPage('lighting',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'editsound' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Sound Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savesound">
				<textarea name="data" id="redactor_content">',printPage('sound',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'editpower' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Power Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savepower">
				<textarea name="data" id="redactor_content">',printPage('power',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'editsl' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Searchlights Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savesl">
				<textarea name="data" id="redactor_content">',printPage('searchlights',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'editcc' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Climate Control Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savecc">
				<textarea name="data" id="redactor_content">',printPage('climatecontrol',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
} else if ($action == 'editvideo' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
``<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing Video Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savevideo">
				<textarea name="data" id="redactor_content">',printPage('video',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'editcontact' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
  
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#redactor_content\').redactor({
				imageUpload: "./index.php?action=uploadimage"
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="editpagediv">
			Editing News Page: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST">
				<input type="hidden" name="action" value="savecontact">
				<textarea name="data" id="redactor_content">',printPage('contact',false),'</textarea>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'savehomepage' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=edithomepage');
			exit();
		}
		if(!updatePage('home', $_POST['data'])){
			header('Location: ./?action=edithome');
			exit();
		} else header('Location: ./');
		
	} else if ($action == 'savesound' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editsound');
			exit();
		}
		if(!updatePage('sound', $_POST['data'])){
			header('Location: ./?action=editsound');
			exit();
		} else header('Location: ./');
	} else if ($action == 'savelighting' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editlighting');
			exit();
		}
		if(!updatePage('lighting', $_POST['data'])){
			header('Location: ./?action=editlighting');
			exit();
		} else header('Location: ./');
	} else if ($action == 'savepower' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editpower');
			exit();
		}
		if(!updatePage('power', $_POST['data'])){
			header('Location: ./?action=editpower');
			exit();
		} else header('Location: ./');
	} else if ($action == 'savesl' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editsl');
			exit();
		}
		if(!updatePage('searchlights', $_POST['data'])){
			header('Location: ./?action=editsl');
			exit();
		} else header('Location: ./');
	} else if ($action == 'savecc' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editcc');
			exit();
		}
		if(!updatePage('climatecontrol', $_POST['data'])){
			header('Location: ./?action=editcc');
			exit();
		} else header('Location: ./');
	} else if ($action == 'savevideo' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editvideo');
			exit();
		}
		if(!updatePage('video', $_POST['data'])){
			header('Location: ./?action=editvideo');
			exit();
		} else header('Location: ./');	
	} else if ($action == 'savecontact' ) {
		if (!isset($_POST['data'])) {
			header('Location: ./?action=editcontact');
			exit();
		}
		if(!updatePage('contact', $_POST['data'])){
			header('Location: ./?action=editcontact');
			exit();
		} else header('Location: ./');
	} else if ($action == 'coverphoto' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
   
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#savebutton\').click(function(){
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="smalladmindiv">
			Editing Cover Photo: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
				<label>Title</label>
				<input type="hidden" name="action" value="setcoverphoto">
				<input type="text" name="title" value="', getSetting('cover_title'), '" />
				<label>Caption</label>
				<input type="text" name="para" value="', getSetting('cover_desc'), '" />
				<label>Link to</label>
				<input type="text" name="link" value="', getSetting('cover_link'), '" />
						
				<label>&nbsp;  </label>
				<label>Leave blank to keep current file.</label>
				<label>&nbsp;  </label>
				<label>File</label><input name="file" type="file" />
				<label>File should be 960x300px.
				  </label>
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if ($action == 'setcoverphoto' ) {		
		echo 'saving photo...';
		if(!isset($_POST['title'])||$_POST['title']=='') {
			header('Location: ./?action=coverphoto');
			die();
		}
		if(!isset($_POST['para'])||$_POST['para']=='') {
			header('Location: ./?action=coverphoto');
			die();
		}
		if(!updateSetting('cover_title', $_POST['title'])) {
			header('Location: ./?action=coverphoto');
			die();
		}
		if(!updateSetting('cover_desc', $_POST['para'])) {
			header('Location: ./?action=coverphoto');
			die();
		}
		if(isset($_POST['link']))
			if(!updateSetting('cover_link', $_POST['link'])) {
				header('Location: ./?action=coverphoto');
				die();
			}
		
		if(isset($_FILES['file']))  {
			$dir = $config['upload_dir'] ;
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = 'uploads/images/'.$secret;

				if(!updateSetting('cover_photo', $link)) {
					header('Location: ./?action=coverphoto');
					die();
				}
			}
		}header('Location: ./');
	} else if (isLoggedIn() && isLoggedIn() && $action == 'password' ) {
		echo '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>Admin</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
   
  <link rel="stylesheet" href="../redactor/redactor.css" />
  <script type="text/javascript" src="../redactor/redactor.js"></script>
  
  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
</head>

<body>
<script type="text/javascript">
		$(function() {
			
			$("#admin_area").click(function(){
				window.location = "./";
			});
			$("#admin_logout").click(function(){
				$.post(\'./\', "action=logout", function(data) {
				  window.location = "../";
				});
			});
			$(\'#savebutton\').click(function(){
				var password=document.forms["edithome"]["pass_old"].value;
				if (password==null || password.length <2)
				{
					alert("Please enter your old password.");
					return false;
				}
				var passworda=document.forms["edithome"]["pass_a"].value;
				if (passworda==null || passworda.length <6)
				{
					alert("Please enter your password.");
					return false;
				}
				var passwordb=document.forms["edithome"]["pass_b"].value;
				if (passwordb==null || passwordb.length <6)
				{
					alert("Please enter your password.");
					return false;
				}
				if(passworda!=passwordb)
				{
					alert("Your passwords do not match!");
					return false;
				}
				$("#cureditform").submit();
				return false;
			});
			
		});
	</script>	
	<div id="bodydiv">
		<div class="smalladmindiv">
			Change Password: <button id="savebutton">Save</button>
			<br><br>
			<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
				<label>Old Password</label>
				<input type="hidden" name="action" value="setpassword">
				<input type="password" name="pass_old" />
				<label>New Password</label>
				<input type="password" name="pass_a" />
				<label>Confirm New Password</label>
				<input type="password" name="pass_b" />
			</form>
		</div>
	</div>
	<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
</body>
</html>';
	} else if (isLoggedIn() && $action == 'setpassword' ) {
		echo 'setting password...';
		if(!isset($_POST['pass_old'])||$_POST['pass_old']=='') {
			header('Location: ./?action=password');
			die();
		}
		if(!isset($_POST['pass_a'])||$_POST['pass_a']=='') {
			header('Location: ./?action=password');
			die();
		}
		if(!isset($_POST['pass_b'])||$_POST['pass_b']=='') {
			header('Location: ./?action=password');
			die();
		}
		if ($_POST['pass_a'] != $_POST['pass_b']) {
			header('Location: ./?action=password');
			die();
		}

		$password = md5(mysql_escape_string(trim(urldecode($_POST['pass_old']))));
		$sql = "SELECT `value` FROM {{DB}}.`settings` WHERE `name` = 'admin_password';";
		$result = queryDB($sql);
		if (!$result || !$result[0]['value'])  {
			header('Location: ./?action=password');
			die();
		}
		
		if ($result[0]['value'] != $password)  {
			header('Location: ./?action=password');
			die();
		}

		$newpass=md5(mysql_escape_string(trim(urldecode($_POST['pass_a']))));
		if(!updateSetting('admin_password', $newpass)) {
			header('Location: ./?action=password');
			die();
		}
		header('Location: ./');
	} else if ($action == 'addproject' ) {
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			  <link rel="shortcut icon" href="/favicon.ico" />
			  <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
			</head>
			
			<body>
			<script type="text/javascript">
					var noatt = true;
					var attps = "';
								/*
								$sql="SELECT * FROM {{DB}}.`publications`;";
								$result = queryDB($sql);
								
								$i=0;
								$c = count($result);
								while($i<$c) {
									$id = $result[$i]['id'];
						
									if($i>0)
									echo ', ';
									echo $id;			
							
									$i++;
								}*/
								echo '";
					var attatched = new Array();
					
					';
								/*
								$sql="SELECT * FROM {{DB}}.`publications`;";
								$result = queryDB($sql);
								
								$i=0;
								$c = count($result);
								while($i<$c) {
									$id = $result[$i]['id'];

									echo 'attatched['.$id.']=true;
									';			
							
									$i++;
								}*/
								echo '
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["title"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#sss").val(attps);
							
							$("#cureditform").submit();
							return false;
						});
						
						$("#padd").click(function(){
							if(noatt) {
								$("#attatchp").html("<b>Publications:</b>");
							}
							
							var id = $("#sel").val();
							if(attatched[id]) return false;
							$("#attatchp").html($("#attatchp").html()+"<br>"+$("#v_"+id).html());
							
							if(!noatt) {
								attps = attps +", "+id;
							} else {
								attps = id;
							}
							noatt = false;
							attatched[id]=true;
							
							return false;
						});
						
						$("#clear_button").click(function(){
							noatt = true;
							attatched = new Array();
							attps = "";
							$("#attatchp").html("No attatched projects.");
							return false;
						});
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Create New Sale Listing: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="setaddproject" />
							<label>Title</label><br>
							<input type="text" name="title" />
							
							<label>&nbsp; </label><br>
							<label>Quantity Available</label><Br>
							<input type="text" name="short" /><br>
							
							<label>Unit Price</label><br>
							<input type="text" name="price" />
							
							<label>&nbsp; </label>
							<br>
							<label>Thumbnail Image</label><Br>
							<input name="file" type="file" /><br>
							<label>Image should be close to 100x100.
							<br><br>
							
							<label>Description</label><br>
							<textarea name="data"></textarea>
							<br>
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'setaddproject' ) {
		if(!isset($_POST['title'])||$_POST['title']=='') {
			header('Location: ./?action=addproject');
			die();
		}
		if(!isset($_POST['data'])) {
			header('Location: ./?action=addproject');
			die();
		}
		if(!isset($_POST['price'])) {
			header('Location: ./?action=addproject');
			die();
		}
		
		echo 'adding project...';
		$title = mysql_escape_string(trim(urldecode($_POST['title'])));
		$data = mysql_escape_string(trim(urldecode($_POST['data'])));
		$price = mysql_escape_string(trim(urldecode($_POST['price'])));
		
		if(!isset($_POST['short'])) {
			$short = 1;
		} else $short = mysql_escape_string(trim(urldecode($_POST['short'])));
		
		if(isset($_FILES['file']))  {
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = 'uploads/images/'.$secret;

			}
		} else $link = 'images/missing.png';
		
		$sql = "INSERT INTO {{DB}}.`projects` (`title`, `desc`, `short_desc`, `picture`, `price`)VALUES('".$title."','".$data."','".$short."','".$link."','".$price."');";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?action=addproject&badsql');
			die();
		}
		header('Location: ./');
	} else if ($action == 'editproject' ) {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`projects` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./');
			die();
		}
		
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["title"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Edit Sale Item: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="seteditproject" />
							<label>Title</label><br>
							<input type="text" name="title" value="',$result[0]['title'],'"/>
							<br>
							<label>Quantity Available</label><Br>
							<input type="text" name="short" value="',$result[0]['short_desc'],'"/>
							<br>
							<label>Price</label><Br>
							<input type="text" name="price" value="',$result[0]['price'],'"/>
							<br><label>&nbsp; </label>
							<br>
							<label>Thumbnail Image</label><Br>
							<label>Leave blank to keep current image.</label><Br>
							<input name="file" type="file" /><br><label>Clear Image?</label><br>
							<input type="checkbox" name="ri" /><br>
							<label>Image should be close to 100x100.</label>
							<div id="testPIcture" style="float:right;background: url(../',$result[0]['picture'],');background-size:cover;width:150px;height:150px;padding:0px;margin-top:-130px;margin-right:400px;"></div>
							<br><br>
							<label>Description</label><br>
							<textarea name="data">',htmlspecialchars_decode($result[0]['desc']),'</textarea>
							<input type="hidden" name="p" value="',mysql_escape_string(trim(urldecode($_GET['p']))),'">
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
		
		
		
	} else if ($action == 'seteditproject' ) {
		echo 'editingg...';
		
		if(!isset($_POST['p'])||$_POST['p']=='') {
			echo "misisng p";
			//header('Location: ./?action=editproject&p='.$id);
			die();
		}
		
		if(!isset($_POST['title'])||$_POST['title']=='') {
			echo "misisng title";
			//header('Location: ./?action=editproject&p='.$id);
			die();
		}
		if(!isset($_POST['data'])||$_POST['data']=='') {
			echo "misisng data";
			//header('Location: ./?action=editproject&p='.$id);
			die();
		}
		if(!isset($_POST['price'])||$_POST['price']=='') {
			echo "misisng price";
			//header('Location: ./?action=editproject&p='.$id);
			die();
		}

		echo 'adding project...';
		$id = mysql_escape_string(trim(urldecode($_POST['p'])));
		echo $id.'<br>';
		$title = mysql_escape_string(trim(urldecode($_POST['title'])));
		$data = mysql_escape_string(trim(urldecode($_POST['data'])));
		$price = mysql_escape_string(trim(urldecode($_POST['price'])));
		
		if(!isset($_POST['short'])) {
			$short = '';
		} else $short = mysql_escape_string(trim(urldecode($_POST['short'])));
		
		$oldlink = '';
		$sql = "SELECT `picture` FROM {{DB}}.`projects` WHERE `id` = '".$id."';";
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editproject&p='.$id);
			die();
		}
		if(!isset($resultz[0]['picture'])||$resultz[0]['picture']=='') $oldlink = 'NULL';	
			else $oldlink = "'".$resultz[0]['picture']."'";
		
		
		echo $title.'<br>';
		echo $data.'<br>';
		echo $price.'<br>';
		echo $short.'<br>';
		echo $oldlink.'<br>';
		
		$link = 'hmmm';
		
		if(isset($_FILES['file']))  {
			echo "<br><br>adding file... <br><br>";
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = "'".'uploads/images/'.$secret."'";

			} else {
				echo "<br><br><br><br>settin glink....<br>";
				$link = $oldlink;
			}
		} else {
			echo "<br><br><br><br>settin glink....<br>";
			$link = $oldlink;
		}
		echo $oldlink.'<br>';
		echo $link.'<br>';
		echo "=======<br>";
		
		if(isset($_POST['ri']) && ($_POST['ri']=='on' || $_POST['ri'] == 1)) {
			$link ="'images/missing.png'";
		}
		
		$sql = "UPDATE {{DB}}.`projects` SET 
			`title` = '".$title."',
			`short_desc` = '".$short."',
			`price` = '".$price."', 
			`desc` = '".$data."',
			`picture` = ".$link." 
			WHERE `id` = '".$id."';
			";
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editproject&p='.$id.'&failedsql');
			die();
		}
		header('Location: ./');
	} else if ($action == 'delproject' ) {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./?deletefail');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`projects` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./?deletefail');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./?deletefail');
			die();
		}
		$sql="DELETE FROM {{DB}}.`projects` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?deletefail');
			die();
		}
		header('Location: ./');
		
	} else if ($action == 'addsoftware') {
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["title"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
						
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Add Background Image: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="setaddsoftware" />
							<label>Title</label><br>
							<input type="text" name="title" />
							<label>&nbsp; </label><br>
							<label>Description</label><Br>
							<input type="text" name="short" />
							<label>&nbsp; </label>
							<br>
							<label>Thumbnail Image</label><Br>
							<input name="file" type="file" /><br>
							<label>Image should be close to 100x100.
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'setaddsoftware' ) {
		echo "adding software...";
		if(!isset($_POST['title'])||$_POST['title']=='') {
			header('Location: ./?action=addproject');
			die();
		}
		
		$title = mysql_escape_string(trim(urldecode($_POST['title'])));
		
		if(!isset($_POST['short'])) {
			$short = '';
		} else $short = mysql_escape_string(trim(urldecode($_POST['short'])));
		
		if(isset($_FILES['file']))  {
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = 'uploads/images/'.$secret;

			}
		} else $link = 'images/missing.png';
		
		$sql = "INSERT INTO {{DB}}.`software` (`title`,`short_desc`, `picture`)VALUES('".$title."','".$short."','".$link."');";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?action=addsoftware&badsql');
			die();
		}
		header('Location: ./');
		
	} else if ($action == 'editsoftware') {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`software` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./');
			die();
		}
		
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["title"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
						
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Edit Software Item: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="seteditsoftware" />
							<label>Title</label><br>
							<input type="text" name="title" value="',$result[0]['title'],'"/>
							<br>
							<label>Short Description</label><Br>
							<input type="text" name="short" value="',$result[0]['short_desc'],'"/>
							<label>&nbsp; </label>
							<br>
							<label>Thumbnail Image</label><Br>
							<label>Leave blank to keep current image.</label><Br>
							<input name="file" type="file" /><br><label>Clear Image?</label><br>
							<input type="checkbox" name="ri" /><br>
							<label>Image should be close to 100x100.</label>
							
							<br><br>
							<label>Description</label><br>
							<textarea name="data" id="redactor_content">',htmlspecialchars_decode($result[0]['desc']),'</textarea>
							<input id="sss" type="hidden" name="pub" />
							<input type="hidden" name="p" value="',mysql_escape_string(trim(urldecode($_GET['p']))),'">
							
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'seteditsoftware' ) {
		echo 'editingg...';
		if(!isset($_POST['p'])||$_POST['p']=='') {
			header('Location: ./?action=editsoftware&p='.$id);
			die();
		}
		
		if(!isset($_POST['title'])||$_POST['title']=='') {
			header('Location: ./?action=editsoftware&p='.$id);
			die();
		}
		if(!isset($_POST['data'])||$_POST['data']=='') {
			header('Location: ./?action=editsoftware&p='.$id);
			die();
		}
		
		echo 'adding project...';
		$id = mysql_escape_string(trim(urldecode($_POST['p'])));
		echo $id.'<br>';
		$title = mysql_escape_string(trim(urldecode($_POST['title'])));
		$data = mysql_escape_string(trim(urldecode($_POST['data'])));
		
		if(!isset($_POST['short'])) {
			$short = '';
		} else $short = mysql_escape_string(trim(urldecode($_POST['short'])));
		
		$oldlink = '';
		$sql = "SELECT `picture` FROM {{DB}}.`software` WHERE `id` = '".$id."';";
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editsoftware&p='.$id);
			die();
		}
		if(!isset($resultz[0]['picture'])||$resultz[0]['picture']=='') $oldlink = 'NULL';	
			else $oldlink = "'".$resultz[0]['picture']."'";
		
		
		echo $title.'<br>';
		echo $data.'<br>';
		echo $short.'<br>';
		echo $oldlink.'<br>';
		
		$link = 'hmmm';
		
		
		if(isset($_FILES['file']))  {
			echo "<br><br>adding file... <br><br>";
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = "'".'uploads/images/'.$secret."'";

			} else {
				echo "<br><br><br><br>settin glink....<br>";
				$link = $oldlink;
			}
		} else {
			echo "<br><br><br><br>settin glink....<br>";
			$link = $oldlink;
		}
		echo $oldlink.'<br>';
		echo $link.'<br>';
		echo "=======<br>";
		
		if(isset($_POST['ri']) && ($_POST['ri']=='on' || $_POST['ri'] == 1)) {
			$link ="'images/missing.png'";
		}
		
		
		$sql = "UPDATE {{DB}}.`software` SET 
			`title` = '".$title."',
			`short_desc` = '".$short."',
			`desc` = '".$data."',
			`picture` = ".$link." 
			WHERE `id` = '".$id."';
			";
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editproject&p='.$id.'&failedsql');
			die();
		}
		header('Location: ./');
	} else if ($action == 'delsoftware' ) {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./?deletefail');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`software` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./?deletefail');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./?deletefail');
			die();
		}
		$sql="DELETE FROM {{DB}}.`software` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?deletefail');
			die();
		}
		header('Location: ./');
	} else if ($action == 'addpublication') {
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["title"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
						
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Add a Publication: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="setaddpub" />
							<label>Title</label><br>
							<input type="text" name="title" /><br>
							<label>Group</label><br>
							<input type="text" name="group" value="Other"/><br>
							<label>Author(s)</label><br>
							<input type="text" name="auth" />
							<label>&nbsp; </label><br>
							<label>Conference/Journal</label><Br>
							<input type="text" name="desc" /><br>
							<label>Link</label><Br>
							<input type="text" name="link" />
							<label>&nbsp; </label>
							<br>
							<label>PDF File (Optional)</label><Br>
							<input name="file" type="file" /><br>
							<label>Video URL</label><Br>
							<input type="text" name="video" />
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'setaddpub' ) {
		echo "adding software...";
		if(!isset($_POST['title'])||$_POST['title']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['group'])||$_POST['group']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['auth'])||$_POST['auth']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['desc'])||$_POST['desc']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		
		$title = mysql_escape_string(trim(urldecode($_POST['title'])));
		$group = mysql_escape_string(trim(urldecode($_POST['group'])));
		$auth = mysql_escape_string(trim(urldecode($_POST['auth'])));
		$desc = mysql_escape_string(trim(urldecode($_POST['desc'])));
		
		if(isset($_POST['link']) &&  $_POST['link']!='') $link = "'".mysql_escape_string(trim(urldecode($_POST['link'])))."'";
		else $link = 'NULL';
		
		if(isset($_POST['video']) && $_POST['video']!='') $video = "'".mysql_escape_string(trim(urldecode($_POST['video'])))."'";
		else $video = 'NULL';
		
		$PDF = 'NULL';
		
		if(isset($_FILES['file']))  {
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'application/pdf')
			{	
			    // setting file's mysterious name
			    $secret =  md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $PDF = '\'uploads/images/'.$secret."'";

			}
		} else $PDF = 'NULL';
		
		//see if our group is set
		$sql = "SELECT `order` FROM {{DB}}.`publications` WHERE `group` = '".$group."' LIMIT 0,1;";
		$result = queryDB($sql);
		if (!$result) {
			header('Location: ./?action=addpublication&badsql&nogroup');
			die();
		}
		if(!isset($result[0]['order'])) $order = 999;
		else $order = $result[0]['order'];
		
		$sql = "INSERT INTO {{DB}}.`publications` (`title`, `group`, `order`, `author`, `desc`, `link`, `pdf`, `video`)VALUES
		('".$title."','".$group."','".$order."','".$auth."','".$desc."', ".$link.", ".$PDF.", ".$video.");";
		
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?action=addpublication&badsql'.$sql);
			die();
		}
		header('Location: ./');
		
	} else if ($action == 'editpub') {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`publications` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./');
			die();
		}
		
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["title"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
						
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Edit Publication: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="seteditpub" />
							<label>Title</label><br>
							<input type="text" name="title" value="',$result[0]['title'],'"/><br>
							<label>Group</label><br>
							<input type="text" name="group" value="',$result[0]['group'],'"/><br>
							<label>Author(s)</label><br>
							<input type="text" name="auth" value="',$result[0]['author'],'"/>
							<label>&nbsp; </label><br>
							<label>Conference/Journal</label><Br>
							<input type="text" name="desc" value="',$result[0]['desc'],'" /><br>
							<label>Link</label><Br>
							<input type="text" name="link" value="',$result[0]['link'],'" />
							<label>&nbsp; </label>
							<br>
							<label>PDF File (Leave blank to keep)</label><Br>
							<input name="file" type="file" /><br>
							<label>Delete Current PDF File</label><Br>
							<input name="ri" type="checkbox">
							<label>Video URL</label><Br>
							<input type="text" name="video" value="',$result[0]['video'],'" />
							<input type="hidden" name="p" value="',mysql_escape_string(trim(urldecode($_GET['p']))),'">
						</form>
						
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'seteditpub' ) {
		echo 'editingg...';
		if(!isset($_POST['title'])||$_POST['title']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['group'])||$_POST['group']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['auth'])||$_POST['auth']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['desc'])||$_POST['desc']=='') {
			header('Location: ./?action=addpublication');
			die();
		}
		if(!isset($_POST['p'])||$_POST['p']=='') {
			header('Location: ./');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_POST['p'])));
		$title = mysql_escape_string(trim(urldecode($_POST['title'])));
		$group = mysql_escape_string(trim(urldecode($_POST['group'])));
		$auth = mysql_escape_string(trim(urldecode($_POST['auth'])));
		$desc = mysql_escape_string(trim(urldecode($_POST['desc'])));
		
		if(isset($_POST['link'])) $link = "'".mysql_escape_string(trim(urldecode($_POST['link'])))."'";
		else $link = 'NULL';
		
		if(isset($_POST['video'])) $video = "'".mysql_escape_string(trim(urldecode($_POST['video'])))."'";
		else $video = 'NULL';
		
		$oldlink = '';
		$sql = "SELECT `pdf` FROM {{DB}}.`publications` WHERE `id` = '".$id."';";
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editpub&p='.$id);
			die();
		}
		if(!isset($resultz[0]['pdf'])||$resultz[0]['pdf']=='') $oldlink = 'NULL';	
			else $oldlink = "'".$resultz[0]['pdf']."'";
		
		
		
		if(isset($_FILES['file']))  {
			echo "<br><br>adding file... <br><br>";
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'application/pdf')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $pdf = "'".'uploads/images/'.$secret."'";

			} else {
				echo "<br><br><br><br>settin glink....<br>";
				$pdf = $oldlink;
			}
		} else {
			echo "<br><br><br><br>settin glink....<br>";
			$pdf = $oldlink;
		}
		
		
		if(isset($_POST['ri']) && ($_POST['ri']=='on' || $_POST['ri'] == 1)) {
			$pdf ="NULL";
		}
		
		//see if our group is set
		$sql = "SELECT `order` FROM {{DB}}.`publications` WHERE `group` = '".$group."' LIMIT 0,1;";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?action=addpublication&badsql');
			die();
		}
		if(!isset($result[0]['order'])) $order = 999;
		else $order = $result[0]['order'];
		
		
		$sql = "UPDATE {{DB}}.`publications` SET 
			`title` = '".$title."',
			`author` = '".$auth."',
			`group` = '".$group."',
			`order` = '".$order."',
			`desc` = '".$desc."',
			`link` = ".$link.",
			`pdf` = ".$pdf.",
			`video` = ".$video."
	
			WHERE `id` = '".$id."';
			";
			
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editpub&p='.$id.'&failedsql');
			die();
		}
		header('Location: ./');
	} else if ($action == 'delpub' ) {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./?deletefail');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`publications` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./?deletefail');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./?deletefail');
			die();
		}
		$sql="DELETE FROM {{DB}}.`publications` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?deletefail');
			die();
		}
		header('Location: ./');
	} else if ($action == 'addz' ) {
	echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["name"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
						
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Add a photo to the Gallery: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="setaddz" />
							<label>Name</label><br>
							<input type="text" name="name" />
							<label>&nbsp; </label><br>
							<label>Description</label><Br>
							<input type="text" name="desc" /><br>
							<br>
							<label>Image</label><Br>
							<input name="file" type="file" /><br>
							<label>Image should be close to 150x200.</label><br>
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'setaddz' ) {
		echo "adding software...";
		if(!isset($_POST['name'])||$_POST['name']=='') {
			header('Location: ./?action=addz');
			die();
		}
		if(!isset($_POST['desc'])||$_POST['desc']=='') {
			header('Location: ./?action=addz');
			die();
		}
		
		$title = mysql_escape_string(trim(urldecode($_POST['name'])));
		$data = mysql_escape_string(trim(urldecode($_POST['desc'])));
		
		if(isset($_FILES['file']))  {
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = 'uploads/images/'.$secret;

			}
		} else $link = 'images/person_missing.png';
		
		$sql = "INSERT INTO {{DB}}.`people` (`name`,`desc`, `picture`)VALUES('".$title."','".$data."','".$link."');";
		
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?action=addz&badsql');
			die();
		}
		header('Location: ./');
		
	} else if ($action == 'editz') {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`people` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./');
			die();
		}
		
		echo '<!DOCTYPE html>
			<html lang="en">
			<head>
			  <meta charset="utf-8" />
			
			  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
			       Remove this if you use the .htaccess -->
			  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
			
			  <title>Admin</title>
			  <meta name="description" content="" />
			  <meta name="author" content="Rayce Stipanovich" />
			
			  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
			
			  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
			  <script type="text/javascript" src="../js/jquery-1.7.2.min.js"></script>
			  <script type="text/javascript" src="../js/jquery-ui-1.8.21.custom.min.js"></script>
			  
			  <link rel="stylesheet" href="../redactor/redactor.css" />
			  <script type="text/javascript" src="../redactor/redactor.js"></script>
			  
			  <link type="text/css" href="../css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
			  <link type="text/css" href="../css/main.css" rel="stylesheet" media="screen" />
			</head>
			
			<body>
			<script type="text/javascript">
					
					$(function() {
						
						$("#admin_area").click(function(){
							window.location = "./";
						});
						$("#admin_logout").click(function(){
							$.post(\'./\', "action=logout", function(data) {
							  window.location = "../";
							});
						});
						$(\'#redactor_content\').redactor({
							imageUpload: "./index.php?action=uploadimage"
						});
						$(\'#savebutton\').click(function(){
							var title=document.forms["edithome"]["name"].value;
							if (title==null || title.length <2)
							{
								alert("Please enter a title.");
								return false;
							}
							
							$("#cureditform").submit();
							return false;
						});
						
						
					});
				</script>	
				<div id="bodydiv">
					<div class="editpagediv adddiv">
						Edit Gallery Photo: <button id="savebutton">Save</button>
						<br><br>
						<form name="edithome" id="cureditform" action="./" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="action" value="seteditz" />
							<label>Name</label><br>
							<input type="text" name="name" value="',$result[0]['name'],'" />
							<label>&nbsp; </label><br>
							<label>Description</label><Br>
							<input type="text" name="desc" value="',$result[0]['desc'],'" /><br>
							<br>
							<label>Thumbnail Image</label><Br>
							<input name="file" type="file" /><br>
							<label>Image should be close to 150x200.</label><br>
							<input type="hidden" name="p" value="',mysql_escape_string(trim(urldecode($_GET['p']))),'">
						</form>
					</div>
				</div>
				<div id="admin_area">Admin</div><div id="admin_logout">Logout</div>
			</body>
			</html>';
	} else if ($action == 'seteditz' ) {
		if(!isset($_POST['name'])||$_POST['name']=='') {
			header('Location: ./?badsql');
			die();
		}
		if(!isset($_POST['desc'])||$_POST['desc']=='') {
			header('Location: ./?badsql');
			die();
		}

		$id = mysql_escape_string(trim(urldecode($_POST['p'])));
		$title = mysql_escape_string(trim(urldecode($_POST['name'])));
		$data = mysql_escape_string(trim(urldecode($_POST['desc'])));

		$oldlink = '';
		$sql = "SELECT `picture` FROM {{DB}}.`people` WHERE `id` = '".$id."';";
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editz&p='.$id);
			die();
		}
		if(!isset($resultz[0]['picture'])||$resultz[0]['picture']=='') $oldlink = 'NULL';	
			else $oldlink = "'".$resultz[0]['picture']."'";

		$link = 'hmmm';
		
		
		if(isset($_FILES['file']))  {
			echo "<br><br>adding file... <br><br>";
			$dir = $config['upload_dir'];
	 
			$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
		 
			if ($_FILES['file']['type'] == 'image/png' 
			|| $_FILES['file']['type'] == 'image/jpg' 
			|| $_FILES['file']['type'] == 'image/gif' 
			|| $_FILES['file']['type'] == 'image/jpeg'
			|| $_FILES['file']['type'] == 'image/pjpeg')
			{	
			    // setting file's mysterious name
			    $secret = md5(date('YmdHis')).$_FILES['file']['name'];
			    $file = $dir.$secret;
			 
			    // copying
			    copy($_FILES['file']['tmp_name'], $file);
			 
			    // displaying file
			    $link = "'".'uploads/images/'.$secret."'";

			} else {
				echo "<br><br><br><br>settin glink....<br>";
				$link = $oldlink;
			}
		} else {
			echo "<br><br><br><br>settin glink....<br>";
			$link = $oldlink;
		}
		echo $oldlink.'<br>';
		echo $link.'<br>';
		echo "=======<br>";
		
		if(isset($_POST['ri']) && ($_POST['ri']=='on' || $_POST['ri'] == 1)) {
			$link ="'images/missing.png'";
		}
		
		$sql = "UPDATE {{DB}}.`people` SET 
			`name` = '".$title."',
			`desc` = '".$data."',
			`picture` = ".$link." 
			WHERE `id` = '".$id."';
			";
			
			
		$resultz = queryDB($sql);
		if(!$resultz) {
			header('Location: ./?action=editz&p='.$id.'&failedsql');
			die();
		}
		header('Location: ./');
	} else if ($action == 'delz' ) {
		if(!isset($_GET['p'])||$_GET['p']=='') {
			header('Location: ./?deletefail');
			die();
		}
		$id = mysql_escape_string(trim(urldecode($_GET['p'])));
		$sql="SELECT * FROM {{DB}}.`people` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result || !isset($result[0]['id'])) {
			header('Location: ./?deletefail');
			die();
		}
		$c = count($result);
		if($c<1) {
			header('Location: ./?deletefail');
			die();
		}
		$sql="DELETE FROM {{DB}}.`people` WHERE `id` = '".$id."';";
		$result = queryDB($sql);
		if(!$result) {
			header('Location: ./?deletefail');
			die();
		}
		header('Location: ./');
	} else if (isLoggedIn() && $action == 'uploadimage' ) {
		// files storage folder
		$dir = $config['upload_dir'];
	 
		$_FILES['file']['type'] = strtolower($_FILES['file']['type']);
	 
		if ($_FILES['file']['type'] == 'image/png' 
		|| $_FILES['file']['type'] == 'image/jpg' 
		|| $_FILES['file']['type'] == 'image/gif' 
		|| $_FILES['file']['type'] == 'image/jpeg'
		|| $_FILES['file']['type'] == 'image/pjpeg')
		{	
		    // setting file's mysterious name
		    $secret = md5(date('YmdHis')).'__'.$_FILES['file']['name'];
		    $file = $dir.$secret;
		 
		    // copying
		    copy($_FILES['file']['tmp_name'], $file);
		 
		    // displaying file
		    $array = array(
		        'filelink' => " ".$config['images_url'].$secret
		    );
			
		    echo stripslashes(json_encode($array));  
		}
 
	}
?>