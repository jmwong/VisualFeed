<?php 
require_once 'lib/display.php';
Display::setTitle("somethin");
echo Display::top();
?>
		<h1 class="span-24 last" id="logo"><img src="img/logo.png" /></h1>
		<h4 class="span-24 last">Sit back and enjoy as we take a visual view of you and your friend's facebook lives!</h4>
		<div id="wall" class="span-20"></div>
		<a href="index.php">vsual</a>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	
<?php 

$cleanUrl = 'http://'.$_SERVER['HTTP_HOST'].'/visualfeed/lib/getFeed.php?session='.$_GET['session'];
$fullUrl =  urlencode($cleanUrl);

$fullUrl = getFullurl('/visualfeed/lib/getFeed.php');
$script = <<<EOQ

	
    var flashvars = {
		showDescription: "true",
		feed: "$fullUrl",
        //feed: "api://www.flickr.com/"
		//feed: "http://api.flickr.com%2Fservices%2Ffeeds%2Fphotos_public.gne%3Ftags%3Dhackathon%26lang%3Den-us%26format%3Drss_200"
    };
    var params = {
         allowFullScreen: "true",
         allowscriptaccess: "always"
    };
 
    $(document).ready(function(){     	
    		$('#wall').flash({
    	    	swf:'cooliris.swf',
    	    	flashvars: flashvars,
    	    	params: params,
    	    	width: '100%',
    	    	height: '100%',
        	});
        	
        	
    });
    
  /*
        var cooliris = {
            onEmbedInitialized : function() {
                alert("cooliris.embed is now available");
            }
        };*/
        
        
        
   


    
    //swfobject.embedSWF("http://apps.cooliris.com/embed/cooliris.swf",
    /*
	swfobject.embedSWF("cooliris.swf",
        "wall", "100%", "100%", "9.0.0", "",
        flashvars, params);
      */  
       
EOQ;

echo Display::bottom($script);?>