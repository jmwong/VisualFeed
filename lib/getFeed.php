<?php
include_once 'config.php';
include_once 'fb_jamin.php';

define('FEEDLIMIT', 20);

$facebook = new JaminFb(array(
//$facebook = new Facebook(array(
  'appId'  => APPID,
  'secret' => APPSECRET,
  'cookie' => true,

));
//var_dump($facebook);

$session = $facebook->getSession();
$uid = null;
// Session based API call.
//var_dump($session);
if ($session) {
	try {
		$uid = $facebook->getUser();

	} catch (FacebookApiException $e) {
		error_log($e);
	}
}

if (!$uid) {
	header('Location: ../index.php');
	//echo "<script>top.location.href = '$loginUrl';</script>";
}

//adapted from facebook
function sendRequest($url, $params, $ch=null) {
	if (!$ch) {
		$ch = curl_init();
	}
	$CURL_OPTS = array(
	CURLOPT_CONNECTTIMEOUT => 10,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_TIMEOUT        => 60,
	CURLOPT_USERAGENT      => 'jamin',
	);
	$opts =$CURL_OPTS;
	$opts[CURLOPT_SSL_VERIFYPEER] = false;
	$opts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
	$opts[CURLOPT_URL] = $url;

	// disable the 'Expect: 100-continue' behaviour. This causes CURL to wait
	// for 2 seconds if the server does not support this header.
	if (isset($opts[CURLOPT_HTTPHEADER])) {
		$existing_headers = $opts[CURLOPT_HTTPHEADER];
		$existing_headers[] = 'Expect:';
		$opts[CURLOPT_HTTPHEADER] = $existing_headers;
	} else {
		$opts[CURLOPT_HTTPHEADER] = array('Expect:');
	}

	curl_setopt_array($ch, $opts);
	$result = curl_exec($ch);
	if ($result === false) {
		$e = new Exception('shit happened');
		curl_close($ch);
		throw $e;
	}
	curl_close($ch);
	return $result;
}

function newPicture($dom, $name, $status, $image, $thumb, $link){
	$newItem = $dom->createElement("item");
	$newTitle = $dom->createElement("title");
	$titleData = $dom->createTextNode($name);
	$newTitle->appendChild($titleData);

	$newDes = $dom->createElement("media:description");
	$desData = $dom->createTextNode($status);
	$newDes->appendChild($desData);

	$newLink = $dom->createElement("link");
	$linkData = $dom->createTextNode($link);
	$newLink->appendChild($linkData);

	$newContent = $dom->createElement("media:content");
	$url = $dom->createAttribute("url");
	$newContent->setAttributeNode($url);
	$newContent->setAttribute("url", $image);

	$newThumb = $dom->createElement("media:thumbnail");
	$turl = $dom->createAttribute("url");
	$newThumb->setAttributeNode($turl);
	$newThumb->setAttribute("url", $thumb);

	$newItem->appendChild($newTitle);
	$newItem->appendChild($newDes);
	$newItem->appendChild($newLink);
	if (strlen($thumb) > 0){
		$newItem->appendChild($newThumb);
	}
	$newItem->appendChild($newContent);


	return $newItem;
}


function newPrev($dom, $s, $t){
	$t = urlencode($t);
	$newPrev = $dom->createElement("atom:link");
	$prevRel = $dom->createAttribute('rel');
	$prevUrl = $dom->createAttribute('href');
	
	$newPrev->setAttributeNode($prevRel);
	$newPrev->setAttribute('rel', 'previous');
	
	$newPrev->setAttributeNode($prevUrl);
	$link = getUrlNoSession('/visualfeed/lib/getFeed.php').'?since='.$t;
	$newPrev->setAttribute('href', $link);
	//var_dump($link);
	return $newPrev;
}

function newNext($dom, $s, $t){
	$t = urlencode($t);
	
	$newPrev = $dom->createElement("atom:link");
	$prevRel = $dom->createAttribute('rel');
	$prevUrl = $dom->createAttribute('href');
	
	$newPrev->setAttributeNode($prevRel);
	$newPrev->setAttribute('rel', 'next');
	
	$newPrev->setAttributeNode($prevUrl);
	
	//$link = getCleanUrl('/visualfeed/lib/getFeed.php').'&until='.$t;
	$link = getUrlNoSession('/visualfeed/lib/getFeed.php').'?until='.$t;
	$newPrev->setAttribute('href', $link);
	//var_dump($newPrev->getAttribute('href'));

	return $newPrev;
}
	

	/*
 	<atom:link rel="previous" href="getFeed.rss?session=$s&until=$t" />
      	<atom:link rel="next" href="getFeed.rss?session=$s&since=$t" />
 */



function getYahooTerms($string){
	$return = sendRequest('http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction',
	array(
				'appid' => 'fpRlW0TV34EDeFmoOVVy3CMxJFL06cCrgsy.JTtr8AKKfrNJznqRzVLhu97mole4RBkITYkXCFXx',
				'context' => $string,
				'output'=> 'php',
	));
	$data = unserialize($return);
	$words = $data['ResultSet']['Result'];
	if (count ($words) <= 0 || strlen($words[0]) <= 1){
		return null;
	}
	return $words;
}


function getFlickr($tag){

	$flickrParams = array(
	'api_key' => '2a1b376ea5b8312b07ad204db25610da',
	'method' => 'flickr.photos.search',
	'format' => 'php_serial',
	'sort' => 'relevance',
	'per_page' => 1,
	'page' => 1,
	'text' => $tag,

	);

	$encoded_params = array();
	foreach ($flickrParams as $k => $v){
		$encoded_params[] = urlencode($k).'='.urlencode($v);
	}

	$url = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);
	$rsp = file_get_contents($url);
	$rsp_obj = unserialize($rsp);

	if ($rsp_obj['stat'] == 'ok'){

		$total = $rsp_obj['photos']['total'];
		if ($total <= 0){
			return null;
		}
		
		$photo = $rsp_obj['photos']['photo'][0];
		$farmId = $photo['farm'];
		$serverId = $photo['server'];
		$id = $photo['id'];
		$secret = $photo['secret'];
		$thumbUrl = 'http://farm'.$farmId.'.static.flickr.com/'.$serverId.'/'.$id.'_'.$secret.'_m.jpg';
		$imageUrl = 'http://farm'.$farmId.'.static.flickr.com/'.$serverId.'/'.$id.'_'.$secret.'_m.jpg';

		return array('thumb' => $thumbUrl, 'image' => $imageUrl);
	}
	else{
		return null;
	}
}

$u = getUrlNoSession('/');
$template = <<<xml
<?xml version="1.0" encoding="utf-8" standalone="yes"?>
      <rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/"
      xmlns:atom="http://www.w3.org/2005/Atom">
      <channel>

        <title>Visual Feed</title>
          <description>FB Project</description>
          <link>$u</link>
         

      </channel>
      </rss>
xml;


$dom = new DOMDocument('1.0');
$dom->substituteEntities = false;
$dom->loadXML($template);
$channel = $dom->getElementsByTagName('channel')->item(0);


function formatTime($timeString){
	return strtotime($timeString);
}


$startTime = 'until='.mktime();
if ($_GET['until']){
	$startTime = 'until='.formatTime($_GET['until']);
}
else if($_GET['since']){
	$startTime = 'since='.formatTime($_GET['since']);
}


 $feed = $facebook->api('/me/home?limit='.FEEDLIMIT.'&'.$startTime);

$prevTime = $feed['data'][0]['created_time'];
$nextTime = $feed['data'][count($feed['data']) - 1]['created_time'];
 foreach ($feed['data'] as $item){
 	$post = $item['message'];
 	$keywords = getYahooTerms($post);
 	if ($keywords){
 		$index = rand(0, count($keywords) - 1);
 		//$post = 'Keyword: ' . $keywords[$index] . '/n' . $post;
 		$image = getFlickr($keywords[$index]);
 		if ($image){
 			$channel->appendChild(newPicture($dom, $item['from']['name'], $post, $image['image'], $image['thumb'], $item['actions'][0]['link']));			
 		}
 	}
 }
 
 $channel->appendChild(newNext($dom, $s, $nextTime));
 $channel->appendChild(newPrev($dom, $s, $prevTime));
 //var_dump($dom->getElementsByTagName('atom:link')->item(0)->getAttribute('href'));
 

//$dom->formatOutput = true;

header('Content-type: text/xml; charset=utf-8');
echo $dom->saveXML();
?>