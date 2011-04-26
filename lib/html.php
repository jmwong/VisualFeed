<?php

class Html{
	public static function css($path, $attributes=NULL){
		
		$attr = '';

		if ($attributes){
			foreach($attributes as $key=>$value){
				$attr .= $key.'="'.$value.'" ';
			}
		}
		return '<link rel="stylesheet" type="text/css" href="'.$path.'.css" '.$attr.'/>'; 
	}
	
	public static function js($path){
		return '<script src="'.$path.'.js" ></script>';
	}
}