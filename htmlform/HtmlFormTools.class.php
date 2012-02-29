<?php

/**
 * Static helper class for all kinds of random functionality.
 * Especially houses method-overwrites for utf-8 security in this case.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.99 beta
 * @package tools
 */
class HtmlFormTools {

	/**
	 * Insert a value into an existing array at a specific index, pushing all following values back.
	 * 
	 * @param Array $array the array to insert the value into
	 * @param uint $index array-index at which to include
	 * @param * $value value to insert at index
	 * @return Array the array with the included value
	 */
	public static function array_insert(Array $array, $index, $value){
		return array_merge(
			array_slice($array, 0, $index), 
			array($value), 
			array_slice($array, $index)
		);
	}
	
	
	
	/**
	 * Removes masking slashes from a string.
	 * Also works recursively on an array of random depth.
	 * 
	 * @param String/Array $data string or array to remove slashes from
	 * @return String/Array cleansed data
	 */
	private static function stripslashes_deep($data) {
		$data = is_array($data) ? array_map(array('self', 'stripslashes_deep'), $data) : stripslashes($data);
		return $data;
	}
	
	
	
	/**
	 * Counteracts the effect of magic quotes if they are activated.
	 * 
	 * @param String/Array $data data to remove magic quotes from
	 * @return String/Array cleansed data
	 */
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
	
	
	
	/**
	 * Returns if at least the minimal UTF-8-environment is set for PHP.
	 * 
	 * @return Boolean yes/no answer
	 */
	private static function utf8EnvironmentSet(){
		return (
			(ini_get('default_charset') == 'UTF-8')
			&& (ini_get('mbstring.internal_encoding') == 'UTF-8')
			&& (ini_get('mbstring.http_output') == 'UTF-8')
		);
	}
	
	
	
	/**
	 * Override-method for strlen that handles utf-8-multibyte-security.
	 * 
	 * @param String $string string to be measured
	 * @param Boolean $needsBinarySafety determines if utf-8 is active
	 * @return uint length of string
	 */
	public static function auto_strlen($string, $needsBinarySafety = false){
		if( $needsBinarySafety && self::utf8EnvironmentSet() ){
			return mb_strlen($string);
		} else {
			return strlen($string);
		}
	}
	
	
	
	/**
	 * Override-method for strpos that handles utf-8-multibyte-security.
	 * 
	 * @param String $haystack string to be used
	 * @param String $needle string to be sought for
	 * @param Boolean $needsBinarySafety determines if utf-8 is active
	 * @return uint/Boolean position of needle or false 
	 */
	public static function auto_strpos($haystack, $needle, $needsBinarySafety = false){
		if( $needsBinarySafety && self::utf8EnvironmentSet() ){
			return mb_strpos($haystack, $needle);
		} else {
			return strpos($haystack, $needle);
		}
	}
	
	
	
	/**
	 * Override-method for preg_match that handles utf-8-multibyte-security.
	 * 
	 * @param String $pattern regex-pattern to use for match
	 * @param String $string string to match pattern against
	 * @param Boolean $needsBinarySafety determines if utf-8 is active
	 * @return Boolean pattern fits yes/no
	 */
	public static function auto_preg_match($pattern, $string, $needsBinarySafety = false){
		return preg_match($pattern.($needsBinarySafety ? 'u' : ''), $string);
	}
	
	
	
	/**
	 * Override-method for preg_replace that handles utf-8-multibyte-security.
	 * 
	 * @param String $pattern regex-pattern to use for replacement
	 * @param String $replacement the string to replace the matches
	 * @param String $string the string to search and replace in
	 * @param Boolean $needsBinarySafety determines if utf-8 is active
	 * @return String result with replacements
	 */
	public static function auto_preg_replace($pattern, $replacement, $string, $needsBinarySafety = false){
		return preg_replace($pattern.($needsBinarySafety ? 'u' : ''), $replacement, $string);
	}
	
	
	
	/**
	 * Override-method for htmlspecialchars that handles utf-8-multibyte-security.
	 * 
	 * @param String $string string to treat
	 * @param Boolean $needsUtf8 determines if utf-8 is active
	 * @return String string with replacements
	 */
	public static function auto_htmlspecialchars($string, $needsUtf8 = false){
		return htmlspecialchars($string, ENT_COMPAT, $needsUtf8 ? 'UTF-8' : 'ISO-8859-1', false);
	}

}

?>