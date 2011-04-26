<?php
require_once 'lib/config.php';
require_once 'lib/display.php'; 
require_once 'lib/fb_jamin.php';
require_once 'lib/facebook.php';


$facebook = new JaminFb(array(
//$facebook = new Facebook(array(
  'appId'  => APPID,
  'secret' => APPSECRET,
  'cookie' => true,
));

//var_dump($facebook);

$session = $facebook->getSession();
$uid = null;
$loginUrl = null;
// Session based API call.
//var_dump($session);
if ($session) {
  try {
    $uid = $facebook->getUser();

  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($uid) {

  //$logoutUrl = $facebook->getLogoutUrl();
  $s = $_GET['session'];	
  header("Location: visual.php?session=$s");
  
} 
else {
	
	$loginUrl = $facebook->getLoginUrl(array(
		'req_perms' => 'read_stream'
	 ));
    //echo "<script>top.location.href = '$loginUrl';</script>"; 
}

?>
<?php echo Display::top();?>
		<h1 class="span-24 last" id="logo"><img src="img/logo.png" /></h1>
		<h4 class="span-24 last">Sit back and enjoy as we take a visual view of you and your friend's facebook lives!</h4>
		<br />
		<br />
		<p>Sit back and enjoy as we take a visual view of you and your friend's facebook lives! First, we grab your feed. Using a python trender by ___ to isolate keywords in the status, and using yahoo, we grab a picture that should match your status. Then we compose these pictures and statuses into a RSS feed and send it to an app created by a third party, Cooliris. This app then displays it in a cool way. Current functionality is limited to English and could include video in the future as well</p>
		<div class="prepend-10 prepend-top"><a href="<?php echo $loginUrl; ?>"><img src="img/login-button.png" /></a></div>
	<a href="visual.php">vsual</a>
<?php 
$fb_appId = $facebook->getAppId();
$s = json_encode($session);
 $script = <<<EOQ
  window.fbAsyncInit = function() {
    FB.init({
    	appId: '$fb_appId', 
    	session: '$s',
    	status: true, 
    	cookie: true,
        xfbml: true});
 
    // whenever the user logs in, we refresh the page
    /*
    FB.Event.subscribe('auth.login', function() {
      window.location.href = "visual.php"
    });*/
  };
EOQ;

echo Display::bottom($script);

?>	
