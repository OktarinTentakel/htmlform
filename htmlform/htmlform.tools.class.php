<?php

class HtmlFormTools {

	public static function array_insert($array, $index, $value){
		return array_merge(
			array_slice($array, 0, $index), 
			array($value), 
			array_slice($array, $index)
		);
	}
	
	
	
	private static function stripslashes_deep($data) {
		$data = is_array($data) ? array_map(array('self', 'stripslashes_deep'), $data) : stripslashes($data);
		return $data;
	}
	
	
	
	public static function undoMagicQuotes($data = null){
		if( get_magic_quotes_gpc() ){
			if( is_null($data) ) $data = $_REQUEST;
		
			if( is_array($data) ){
				return array_map(array('self', 'stripslashes_deep'), $data);
			} else {
				return stripslashes($data);
			}
		}
		
		return $data;
	}
	
	
	
	private static function utf8EnvironmentSet(){
		return (
			(ini_get('default_charset') == 'UTF-8')
			&& (ini_get('mbstring.internal_encoding') == 'UTF-8')
			&& (ini_get('mbstring.http_output') == 'UTF-8')
		);
	}
	
	
	
	public static function auto_strlen($string, $needsBinarySafety = false){
		if( $needsBinarySafety && self::utf8EnvironmentSet() ){
			return mb_strlen($string);
		} else {
			return strlen($string);
		}
	}
	
	
	
	public static function auto_strpos($haystack, $needle, $needsBinarySafety = false){
		if( $needsBinarySafety && self::utf8EnvironmentSet() ){
			return mb_strpos($haystack, $needle);
		} else {
			return strpos($haystack, $needle);
		}
	}
	
	
	
	public static function auto_preg_match($pattern, $string, $needsBinarySafety = false){
		return preg_match($pattern.($needsBinarySafety ? 'u' : ''), $string);
	}
	
	
	
	public static function auto_preg_replace($pattern, $replacement, $string, $needsBinarySafety = false){
		return preg_replace($pattern.($needsBinarySafety ? 'u' : ''), $replacement, $string);
	}
	
	
	
	public static function auto_htmlspecialchars($string, $needsUtf8 = false){
		return htmlspecialchars($string, ENT_COMPAT, $needsUtf8 ? 'UTF-8' : 'ISO-8859-1', false);
	}

}

?>