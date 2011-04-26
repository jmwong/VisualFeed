<?php
define('APPID', '');
define ('APPSECRET', '');

$facebook = null;

function getFullUrl($php_file){
	return urlencode(getCleanUrl($php_file));
}

function getCleanUrl($php_file){
	$u = 'http://'.$_SERVER['HTTP_HOST'].$php_file.'?session='.$_GET['session'];
	return $u;
}

function getUrlNoSession($php_file){
	return 'http://'.$_SERVER['HTTP_HOST'].$php_file;
}
?>