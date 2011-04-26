<?php
require_once 'facebook.php';


class JaminFb extends Facebook{
	public function postToFeed($params){
		$default = array(
			'message' => '',
			'picture' => '',
			'link' => '',
			'name' => '',
			'caption' => '',
			'description' => '',
			'source' => ''
		);
		
		extract(array_merge($default, $params));
		
		$result = $this->api(
		    '/me/feed/',
			'post',
		    array(
		    	'access_token' => $this->getSession()->access_token, 
		    	'message' => $message,
		    	'picture' => $picture,
		    	'link' => $link,
		    	'name' => $name,
		    	'caption' => $caption,
		    	'description' => $description,
		    	'source' => $source
		    )
		);
		
		return $result;
	}
}

