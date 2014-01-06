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
	
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame 
       Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

  <title>IMMEDIA Event Production</title>
  <meta name="description" content="" />
  <meta name="author" content="Rayce C. Stipanovich" />

  <meta name="viewport" content="width=device-width; initial-scale=1.0" />

  <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
  <link rel="shortcut icon" href="/favicon.ico" />
  <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
  <link rel="stylesheet" href="./css/main.css" />
  <script type="text/javascript" src="./js/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="./js/jquery-ui-1.10.2.custom.min.js"></script>
  <script type="text/javascript" src="./js/jquery.h.m.js"></script>
  <script type="text/javascript">
	var currentpage = 1;
	var slideshowSeconds = 10;
	var photos = new Array();
	var ignoremouse = false;
	
	<?php
	//echo our photo backgrounds
	$sql="SELECT * FROM {{DB}}.`software` ORDER BY `manual_order`;";
	$result = queryDB($sql);
	
	$i=0;
	$c = count($result);
	while($i<$c) {
		$photourl = $result[$i]['picture'];
		if($photourl==''||$photourl==NULL||$photourl=='NULL'){
			$photourl = 'images/missing.png';
		}
		
		echo 'photos['.$i.'] = "'.$photourl.'";
		';		
		$i++;
	}?>
	
	var currentBG = 0;
	
	function randomizeBackground() {
		var bg = photos[currentBG];
		$("#background").css("background-image", 'url("'+bg+'")');
		
		if (photos.length == 1) $("#background").fadeIn(1000);
		else
		setTimeout(function(){
			$("#background").fadeIn(1000);
			currentBG++;
			if (currentBG==photos.length) currentBG =0;
			setTimeout(function(){
				$("#background").fadeOut(1000);
				setTimeout(function(){
					randomizeBackground();
				}, 1000);
			}, slideshowSeconds*1000);
		}, 200);
	}
	
	function goToPage(pagenumber) {
		var offset = (pagenumber-1)*-800;
		var traveltime = Math.abs(pagenumber-currentpage)*200+150;
		$("#pages").animate({marginLeft:offset}, traveltime, 'swing');
		currentpage=pagenumber;
		$('body,html').animate({
			scrollTop: 0
		}, 500);
	}
	
	function showPopUP(success, message) {
		Recaptcha.reload();
		if (success) {
			$('#popupWindow').fadeIn(300);
			$('#popupTitle').html('Success!');
			$('#popupErrorText').html(message);
			$('body,html').animate({
				scrollTop: 0
			}, 500);
			setTimeout(function(){
				$("#contactModal").fadeOut(500);
				$('#contactContent').fadeOut(500);
				$("#immedialogo").css('position', 'absolute');
				setTimeout(function(){
					resetContactForm();
				}, 5000);
			}, 2000);
		} else {
			$('#popupWindow').fadeIn(300);
			$('#popupTitle').html('An Error Occured');
			$('#popupErrorText').html(message);
		}
		setTimeout(function(){
			$('#popupWindow').fadeOut(1000);
		}, 1000);
	}
	
	function IsEmail(email) {
	  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  return regex.test(email);
	}
	
	function submitContactForm() {
		//TIME FOR LOCAL VALIDATION AND SUBMISSION
		var name = $("#text-name").val();
		var org = $("#text-org").val();
		var phone = $("#text-phone").val();
		var email = $("#text-email").val();
		var dates = $("#text-dates").val();
		var name = $("#text-name").val();
		var loc = $("#text-loc").val();
		var desc = $("#text-description").val();
		
		//checkboxes
		var sound = $("#check-sound").prop('checked');
		var lighting = $("#check-lighting").prop('checked');
		var video = $("#check-video").prop('checked');
		var heating = $("#check-heating").prop('checked');
		var ac = $("#check-ac").prop('checked');
		var staging = $("#check-staging").prop('checked');
		var search = $("#check-search").prop('checked');
		var power = $("#check-power").prop('checked');
		var rental = $("#check-rental").prop('checked');
		
		//validate stuff
		if (name.length < 4) {
			showPopUP(false, 'Please enter a valid name.');
			return;
		}
		if (org.length < 1) {
			showPopUP(false, 'Please enter a valid organization title.');
			return;
		}
		if (phone.length < 10) {
			showPopUP(false, 'Please enter a valid phone number.');
			return;
		}
		if (email.length < 6 || IsEmail(email) == false) {
			showPopUP(false, 'Please enter a valid email address.');
			return;
		}
		if (dates.length < 1) {
			showPopUP(false, 'Please enter an event date.');
			return;
		}
		if (loc.length < 1) {
			showPopUP(false, 'Please enter an event location.');
			return;
		}
		if (desc.length < 1) {
			showPopUP(false, 'Please enter an event description.');
			return;
		}
		
		//send it out!
		var datas = '&name=' + name + '&org=' + org + '&phone=' + phone + '&email=' + email + '&dates=' + dates + '&loc=' + loc + '&desc=' + desc;
		//checkboxes
		datas = datas + '&check-sound=' + sound + '&check-lighting=' + lighting + '&check-video=' + video + '&check-heating=' + heating + '&check-ac=' + ac + '&check-staging=' + staging + '&check-search=' + search  + '&check-power=' + power + '&check-rental=' + rental;
		 
		//captcha
		datas = datas + '&recaptcha_response_field=' + $("#recaptcha_response_field").val() + '&recaptcha_challenge_field=' + $("#recaptcha_challenge_field").val();

		//start the ajax
        $.ajax({
            url: "./contactForm.php", 
            type: "post",
            data: datas,     
            cache: false,
            dataType: "json",
            success: function (data) {              
                if (data.error) {
                	showPopUP(false, data.message);
                } else {
                	if (data.success){
                		showPopUP(true, 'Thank you for your feedback.');
                		resetContactForm();
                	} else {
                		showPopUP(false, 'Something went wrong on our end.');
                	}
                }
            },
            error: function () { 
            	showPopUP(false, 'Something went wrong on our end.');
            }
        });
    }
	function resetContactForm() {
		$("input[type=text], textarea").val("");
		$("input[type=text], textarea").val("");
		$('input:checkbox').iCheck('uncheck');
		Recaptcha.reload();
	}
	
  
	$(document).ready(function() {
		
		<?php if (isLoggedIn()) echo '
				$("#admin_area").click(function(){
					window.location = "./admin/";
				});
				$("#admin_logout").click(function(){
					$.post(\'./admin/\', "action=logout", function(data) {
					  window.location = "./";
					});
				});
				'; ?>
		
		$(window).on('hashchange', function() {
		  var hash = window.location.hash.replace('#','');
		  $('.navlink, .services_navlink').each(function(){
		  	if (''+$(this).attr('id').replace('nav_s_','').replace('nav_','') == hash) {
				goToPage($(this).attr('rel'));
		  	}
		  });
		});
		
		$(".navlinkspecial").hover(function(){
			if (!ignoremouse) {
				$("#nav_services").addClass("navlinkspecial_highlight");
				$("#services_nav").stop().slideDown("fast");
			}
		},function(){
			if (!ignoremouse) {
				$("#services_nav").stop().slideUp("fast");
				if($("#nav_services").hasClass('navlinkspecial_highlight')) $("#nav_services").toggleClass("navlinkspecial_highlight",300);
				else $("#nav_services").removeClass('navlinkspecial_highlight');
			}
			ignoremouse=false;
		}).click(function(){
			if (!ignoremouse) {
				$("#nav_services").toggleClass("navlinkspecial_highlight",100);
				$("#services_nav").stop().slideDown("fast");
			}
		});
		
		$(".navlink").hover(function(){
			ignoremouse=false;
			$(this).toggleClass("navlink_h", 100);
		},function() {
			ignoremouse=false;
			$(this).toggleClass("navlink_h", 300);
		});
		
		$(".navlink, .services_navlink").click(function(){
			ignoremouse=true;
			$("#nav_services").removeClass("navlinkspecial_highlight");
			$("#services_nav").stop().slideUp("fast");
			window.location.hash = $(this).attr('id').replace('nav_s_','').replace('nav_','');
			goToPage($(this).attr('rel'));
		});
		$("#background").hide();
		$("#services_nav").hide();
		randomizeBackground();
		
		$("#popupWindow").hide();
		$("#contactModal").hide();
		$('#contactContent').hide();
		$("#contactLinkDiv").click(function(){
			$('body,html').animate({
				scrollTop: 0
			}, 500);
			setTimeout(function(){
					$("#contactModal").fadeIn(500);
					$('#contactContent').fadeIn(500);
					$("#immedialogo").css('position', 'fixed');
				}, 500);
		});
		
		$("#contactModal").click(function(e){
			e.stopPropagation();
			$('body,html').animate({
				scrollTop: 0
			}, 500);
			setTimeout(function(){
				$("#contactModal").fadeOut(500);
				$('#contactContent').fadeOut(500);
				$("#immedialogo").css('position', 'absolute');
				setTimeout(function(){
					resetContactForm();
				}, 500);
			}, 500);
		});
		$('#contactContent').click(function(e){
			//do nothing for now
		});
		
		$("#contactLinkDiv").hover(function(){
			$(this).toggleClass("buttonHover", 100);
		},function() {
			$(this).toggleClass("buttonHover", 100);
		});
		
		$("#submitContact").hover(function(){
			$(this).toggleClass("buttonH", 100);
		},function() {
			$(this).toggleClass("buttonH", 100);
		});
		$("#submitContact").click(function(){
			//submit our form
			submitContactForm();
		});
		
		//handle url parsing
		if(window.location.hash) {
		  var hash = window.location.hash.replace('#','');
		  $('.navlink, .services_navlink').each(function(){
		  	if (''+$(this).attr('id').replace('nav_s_','').replace('nav_','') == hash) {
		  		var offset = ($(this).attr('rel')-1)*-800;
				$("#pages").css('margin-left',offset);
				currentpage=$(this).attr('rel');
		  	}
		  });
		}
		
		//Checkboxes
		  $('input:checkbox').each(function(){
		      var self = $(this),
		      label = self.next(),
		      label_text = label.text();
		
		      label.remove();
		      self.iCheck({
		        checkboxClass: 'icheckbox_line',
		        radioClass: 'iradio_line',
		        insert: '<div class="icheck_line-icon"></div>' + label_text
		    });
		  });
	});
  </script>
  <script src="jbcore/juicebox.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
  <script>
	new juicebox({
		containerId : "juicebox-container",
		galleryWidth: "780",
		galleryHeight: "600",
		backgroundColor: "rgba(34,34,34,0)",
		useThumbDots : true,
		showExpandButton: false,
		showThumbsButton: false,
		configUrl: "photoGallery.php"
	});
  </script>
  <script>
	function initialize() {
	  var myLatlng = new google.maps.LatLng(42.229296,-71.785891);
	  var mapOptions = {
	    zoom: 16,
	    center: myLatlng
	  }
	  var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	
	  var marker = new google.maps.Marker({
	      position: myLatlng,
	      map: map,
	      title: 'Immedia LLP'
	  });
	}
	
	google.maps.event.addDomListener(window, 'load', initialize);

  </script>
  <link href="css/skins/line/line.css" rel="stylesheet">
  <script src="js/jquery.icheck.min.js"></script>
  <script type="text/javascript">
	 var RecaptchaOptions = {
	    theme : 'blackglass'
	 };
 </script>
  <!--[if gte IE 9]>
	  <style type="text/css">
	    .gradient {
	       filter: none;
	    }
	  </style>
  <![endif]-->
</head>
<body>
	  <div id="background"> </div>
	  <div id="topgradient" class="gradient"> </div>
	  <div id="immedialogo"> </div>
	  <div id="bodycontainer">
	  	<div id="navigation">
	  		<div class="navlink" id="nav_home" rel="1">HOME</div>
	  		<div class="navlink" id="nav_about" rel="2">GALLERY</div>
	  		<div class="navlink" id="nav_news" rel="3">NEWS</div>
	  		<div class="navlinkspecial" id="nav_services">SERVICES
	  			<div id="services_nav">
			  		<div class="services_navlink" id="nav_s_lighting" rel="4">LIGHTING</div>
			  		<div class="services_navlink" id="nav_s_sound" rel="5">SOUND</div>
			  		<div class="services_navlink" id="nav_s_searchlights" rel="6">SEARCHLIGHTS</div>
			  		<div class="services_navlink" id="nav_s_power" rel="7">POWER</div>
			  		<div class="services_navlink" id="nav_s_climate" rel="8">CLIMATE CONTROL</div>
			  		<div class="services_navlink" id="nav_s_video" rel="9">VIDEO</div>
			  	</div>
	  		</div>
	  		<div class="navlink" id="nav_sales" rel="10">SALES</div>
	  		<div class="navlink" id="nav_contact" rel="11">CONTACT</div>
	  	</div>
	  	<div id="content">
	  		<div id="pages">
		  		<div class="pagediv" id="page_home" >
		  			<p class="page_title">Welcome</p>
		  			<div class="page_content">
		  				<?php printPage('home'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_about" >
		  			<p class="page_title">Gallery</p>
		  			<div class="page_content">
						<div id="juicebox-container"></div>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_news" >
		  			<p class="page_title">News</p>
		  			<div class="page_content">
		  				<?php printPage('contact'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_lighting" >
		  			<p class="page_title">Lighting Services</p>
		  			<div class="page_content">
		  				<?php printPage('lighting'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_sound" >
		  			<p class="page_title">Audio Reinforcement Services</p>
		  			<div class="page_content">
		  				<?php printPage('sound'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_searchlights" >
		  			<p class="page_title">Searchlight Services</p>
		  			<div class="page_content">
		  				<?php printPage('searchlights'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_power" >
		  			<p class="page_title">Power Services</p>
		  			<div class="page_content">
		  				<?php printPage('power'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_climatecontrol" >
		  			<p class="page_title">Climate Control Services</p>
		  			<div class="page_content">
		  				<?php printPage('climatecontrol'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_video" >
		  			<p class="page_title">Video Services</p>
		  			<div class="page_content">
		  				<?php printPage('video'); ?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_sales" >
		  			<p class="page_title">Equipment Sales</p>
		  			<div class="page_content salesContainer">
		  				<?php
							//echo our photo backgrounds
							$sql="SELECT * FROM {{DB}}.`projects` ORDER BY `manual_order`;";
							$result = queryDB($sql);
							
							$i=0;
							$c = count($result);
							if ($c==0) echo "
							<center><p>We currently do not have any items for sale.</p></center>
							";
							while($i<$c) {
								$photourl = $result[$i]['picture'];
								if($photourl==''||$photourl==NULL||$photourl=='NULL'){
									$photourl = 'images/missing.png';
								}
								$price = stripcslashes($result[$i]['price']);
								$quant = stripcslashes($result[$i]['short_desc']);
								$desc = stripcslashes($result[$i]['desc']);
								$title = stripcslashes($result[$i]['title']);
						echo '
						<div class="saleDiv">
		  					<div class="itemPhoto" style="background:url('.$photourl.') no-repeat center center;background-size:cover;"></div>
		  					<div class="InfoBox" style="background:#660066;">
		  						<p class="iCost">Unit Price</p>
		  						<p class="iPrice">'.$price.'</p>
		  						<p class="iCount">Number Available</p>
		  						<p class="iQuant">'.$quant.'</p>
		  					</div>
		  					<p class="ItemName">'.$title.'</p>
		  					<p class="itemDescT">Item Description:</p>
		  					<p class="itemDesc">'.$desc.'</p>
		  				</div>
		  				';
	
								$i++;
							}
						?>
		  			</div>
		  		</div>
		  		<div class="pagediv" id="page_contact" >
		  			<p class="page_title">Contact Us</p>
		  			<div class="page_content">
		  				<p>If you would like to contact us for quotes, rentals, or general feedback, please use the link below.</p>
		  				<br>
		  				<div id="contactLinkDiv" class="contactLinkDiv">
		  					<p>Send us a Message</p>
		  				</div>
		  				<br>
		  				<center>
		  				<p>IMMEDIA LLP • 1075 MILLBURY STREET • WORCESTER, MASS.</p>
						<p>PHONE • 1 - 800 - 874 - 3337</p>
						<p>FAX • 1 - 800 - 874 - 3337</p>
						<p>OPEN M-F 9-5, SA 10-12PM</p>
						<p>EMAIL • <a href="mailto:sales@immedia1.com" alt="sales@immedia1.com">sales@immedia1.com</a></p>
						<div id="map-canvas"></div></div>
						</center>
		  		</div>
	  		</div>
	  	</div>
	  </div>
	  <div id="contactModal"></div>
	  <div id="contactContent">
	  	<p class="page_title">Send us a Message</p>
	  	<form action="">
	  		<div class="clearfix"></div>
	  		<label class="prettyLabel">Your Name</label>
	  		<label class="prettyLabel">Organization</label>
	  		<input class="textInput" type="text" name="name" id="text-name">
	  		<input class="textInput" type="text" name="org" id="text-org">
	  		<div class="clearfix"></div>
	  		<label class="prettyLabel">Phone</label>
	  		<label class="prettyLabel">Email</label>
	  		<input class="textInput" type="text" name="phone" id="text-phone">
	  		<input class="textInput" type="text" name="email" id="text-email">
	  		<div class="clearfix"></div>
	  		<label class="prettyLabel">Date(s) of Event</label>
	  		<label class="prettyLabel">Event Location</label>
	  		<input class="textInput" type="text" name="dates" id="text-dates">
	  		<input class="textInput" type="text" name="loc" id="text-loc">
	  		<div class="clearfix"></div>
	  		<label class="prettyLabel">Event Requirements</label>
			<div class="clearfix"></div>
	  		<input type="checkbox" name="check-sound" id="check-sound">
			<label>Sound</label>
			<input type="checkbox" name="check-lighting" id="check-lighting">
			<label>Lighting</label>
			<input type="checkbox" name="check-video" id="check-video">
			<label>Video</label>
			<input type="checkbox" name="check-heating" id="check-heating">
			<label>Heating</label>
			<input type="checkbox" name="check-ac" id="check-ac">
			<label>Air Conditioning</label>
			<input type="checkbox" name="check-staging" id="check-staging">
			<label>Staging</label>
			<input type="checkbox" name="check-search" id="check-search">
			<label>Searchlights</label>
			<input type="checkbox" name="check-power" id="check-power">
			<label>Power</label>
			<input type="checkbox" name="check-rental" id="check-rental">
			<label>Equipment Rental</label>
			<div class="clearfix"></div>
			<label class="prettyLabel">Event Description</label>
			<label class="prettyLabel" id="CaptchaLabel"></label>
			<div class="clearfix"></div>
	  		<textarea class="CinputText" name="EventDescription" id="text-description"></textarea>
	  		<?php
	  		  global $config;
	          require_once('recaptchalib.php');
	          $publickey = $config['captcha_public_key'];
	          echo recaptcha_get_html($publickey);
	        ?>
	        <div id="submitContact" class="submitC">
	        	<p class="submitLabel">Submit</p>
	        </div>
	  	</form>
	  </div>
	  <div id="popupWindow">
	  	<center>
	  	<p id="popupTitle">An Error Occured</p>
	  	<p id="popupErrorText">An Error Occured</p>
	  	</center>
	  </div>
	  <?php if (isLoggedIn()) echo '
	  <div id="admin_area">Admin</div><div id="admin_logout">Logout</div>'; 
	  ?>
</body>
</html>
<?php exit(); ?>
