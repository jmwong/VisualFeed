<?php
require_once 'lib/html.php';
require_once 'lib/config.php';

class Display{
	
	private static $title = "Visual Feed";
	
	public static function setTitle($t){
		self::$title = $t;
	}
	
	public static function top(){
		$t = self::$title;
 		return <<<EOQ
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>$t</title>
		<link rel="stylesheet" href="css/blueprint/screen.css" />
		<link rel="stylesheet" href="css/main.css" />
	</head>
	<body>
		<div id="wrapper" class="container">
EOQ;

	}
	


	public static function bottom($script = ''){
		
		$jquery = Html::js('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min');
		$jq_swfObject = Html::js('js/jquery.swfobject.1-1-1.min');
		
		$fb_appId = APPID;
		return <<<EOQ
	 </div>	
	 
	 <div id="fb-root">
	 </div>
	$jquery
	$jq_swfObject 
	
	 <script> 

		$script
	  
		  (function() {
		    var e = document.createElement('script'); e.async = true;
		    e.src = document.location.protocol +
		      '//connect.facebook.net/en_US/all.js';
		    document.getElementById('fb-root').appendChild(e);
		  }());
  
	</script>


	
	</body>
</html>
EOQ;
	}
}
?>