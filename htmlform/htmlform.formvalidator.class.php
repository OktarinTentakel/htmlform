<?php

//--|includes----------

require_once('htmlform.tools.class.php');



//--|class----------

/**
 * A FormValidator defines a set of rules a widget-value has to fulfill before being able to be called valid.
 * Validation is an all-or-nothing game. If just one validator fails inside a structure being validated the whole
 * construct is deemed invalid. Keep that in mind when setting up the rules for a validator.
 * 
 * Depending on the nature of a formelement a validator is being provided with either one value or a bunch of values.
 * You don't have to care about this, the validator will handle this for you. Just use the rules as they seem logical
 * and if problems arise, read the description for the rule setter for further details.
 * 
 * If you validate after a form is completely constructed you also won't have to worry about utf-8 or language settings
 * for messages.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.8 beta
 * @package validation
 */
class FormValidator{
	// ***
	const MESSAGECLASS = 'htmlform_message_div';
	
	private $messageLanguage;
	private $messageQueue;
	private $customErrorMessage;
	private $forceErrorMessageOutput;
	private $fieldName;
	
	private $rules;
	private $values;
	private $isValid;
	
	private $needsUtf8Safety;
	
	private function __construct(){
		$this->messageLanguage = 'english';
		$this->messageQueue = array();
		$this->customErrorMessage = '';
		$this->forceErrorMessageOutput = false;
		$this->fieldName = '';
		
		$this->rules = array();
		$this->values = array();
		$this->isValid = true;
		
		$this->needsUtf8Safety = true;
	}
	
	
	
	public function get(){
		$res = new FormValidator();
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setMessageLanguage($language){
		$this->messageLanguage = $language;
		return $this;
	}
	
	
	
	public function setErrorMessage($message){
		$this->customErrorMessage = "$message";
		return $this;
	}
	
	
	
	public function setAutoErrorMessagesAsCustom(){
		$this->forceErrorMessageOutput = true;
		$this->customErrorMessage = '';
		return $this;
	}
	
	
	
	public function setFieldName($name){
		$this->fieldName = $name;
		return $this;
	}
	
	
	
	public function setValue($value){
		$this->values = array("$value");
		return $this;
	}
	
	
	
	public function setValues(Array $values){
		$this->values = $values;
		return $this;
	}
	
	
	
	public function setUtf8Safety($needed = true){
		$this->needsUtf8Safety = $needed;
	}
	
	
	
	public function setCustomCase($customResult){
		$this->rules['customcase'] = $customResult;
		return $this;
	}
	
	
	
	public function setRequired(){
		$this->rules['required'] = true;
		return $this;
	}
	
	
	
	public function setOptional(){
		$this->rules['optional'] = true;
		return $this;
	}
	
	
	
	public function setMinLength($minlength){
		$this->rules['minlength'] =  (integer)$minlength;
		return $this;
	}
	
	
	
	public function setMaxLength($maxlength){
		$this->rules['maxlength'] =  (integer)$maxlength;
		return $this;
	}
	
	
	
	public function setRangeLength(Array $range){
		$this->rules['rangelength'] =  $range;
		return $this;
	}
	
	
	
	public function setMin($min){
		$this->rules['min'] = (integer)$min;
		return $this;
	}
	
	
	
	public function setMax($max){
		$this->rules['max'] = (integer)$max;
		return $this;
	}
	
	
	
	public function setRange(Array $range){
		$this->rules['range'] = $range;
		return $this;
	}
	
	
	
	public function setEmail(){
		$this->rules['email'] = true;
		return $this;
	}
	
	
	
	public function setUrl(){
		$this->rules['url'] = true;
		return $this;
	}
	
	
	
	public function setDate(){
		$this->rules['date'] = true;
		return $this;
	}
	
	
	
	public function setDateISO(){
		$this->rules['dateISO'] = true;
		return $this;
	}
	
	
	
	public function setDateDE(){
		$this->rules['dateDE'] = true;
		return $this;
	}
	
	
	
	public function setNumber(){
		$this->rules['number'] = true;
		return $this;
	}
	
	
	
	public function setNumberDE(){
		$this->rules['numberDE'] = true;
		return $this;
	}
	
	
	
	public function setDigits(){
		$this->rules['digits'] = true;
		return $this;
	}
	
	
	
	public function setCreditcard(){
		$this->rules['creditcard'] = true;
		return $this;
	}
	
	
	
	public function setCharacterClass($regExCharacterClass){
		$this->rules['characterclass'] = $regExCharacterClass;
		return $this;
	}
	
	
	
	//---|rules----------
	
	private function customcase($customRes){
		$res = true;
		
		if( is_string($customRes) ){
			if( $customRes != '' ){
				$this->messageQueue[] = $customRes;
				$res = false;
			}
		} elseif( ($this->fieldName != '') && (($customRes == false) || ($customRes == null)) ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_CUSTOMCASE);
			$res = false;
		}
		
		return $res;
	}
	
	
	
	private function required($X){
		$res = true;
		
		if( count($this->values) == 1 ){
			$res = !($this->values[0] == '');
		} else {
			$res = !(count($this->values) == 0);
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_REQUIRED);
		}
		
		return $res;
	}
	
	
	
	private function optional($X){
		return true;
	}
	
	
	
	private function minlength($minlength, $internal = false){
		$res = true;
		
		if( count($this->values) == 1 ){
			$res = (HtmlFormTools::auto_strlen($this->values[0], $this->needsUtf8Safety) >= $minlength);
		} else {
			$res = (count($this->values) >= $minlength);
		}
		
		if( !$res && ($this->fieldName != '') && !$internal ){
			$this->messageQueue[] = str_replace(array('%name%', '%count%'), array($this->fieldName, $minlength), MSG_MINLENGTH);
		}
		
		return $res;
	}
	
	
	
	private function maxlength($maxlength, $internal = false){
		$res = true;
		
		if( count($this->values) == 1 ){
			$res = (HtmlFormTools::auto_strlen($this->values[0], $this->needsUtf8Safety) <= $maxlength);
		} else {
			$res = (count($this->values) <= $maxlength);
		}
		
		if( !$res && ($this->fieldName != '') && !$internal ){
			$this->messageQueue[] = str_replace(array('%name%', '%count%'), array($this->fieldName, $maxlength), MSG_MAXLENGTH);
		}
		
		return $res;
	}
	
	
	
	private function rangelength(Array $range){
		$res = true;
		
		if( count($range) >= 2 ){
			$res = $this->minlength((integer)$range[0], true) && $this->maxlength((integer)$range[1], true);
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace(
					array('%name%', '%min%', '%max%'),
					array($this->fieldName, (integer)$range[0], (integer)$range[1]),
					MSG_RANGELENGTH
				);
			}
		}		
		
		return $res;
	}
	
	
	
	private function min($min, $internal = false){
		$res = true;
		
		foreach( $this->values as $val ){
			$res = (is_numeric($val) && ((integer)$val >= $min));
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') && !$internal ){
			$this->messageQueue[] = str_replace(array('%name%', '%count%'), array($this->fieldName, $min), MSG_MIN);
		}
		
		return $res;
	}
	
	
	
	private function max($max, $internal = false){
		$res = true;
		
		foreach( $this->values as $val ){
			$res = (is_numeric($val) && ((integer)$val <= $max));
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') && !$internal ){
			$this->messageQueue[] = str_replace(array('%name%', '%count%'), array($this->fieldName, $max), MSG_MAX);
		}
		
		return $res;
	}
	
	
	
	private function range($range){
		$res = true;
		
		if( count($range) >= 2 ){
			$res = $this->min((integer)$range[0], true) && $this->max((integer)$range[1], true);
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace(
					array('%name%', '%min%', '%max%'),
					array($this->fieldName, (integer)$range[0], (integer)$range[1]),
					MSG_RANGE
				);
			}
		}		
		
		return $res;
	}
	
	
	
	private function email($X){
		$res = true;
		
		foreach( $this->values as $email ){
			//gucken ob @ da und ob L�ngen stimmen
			if (!HtmlFormTools::auto_preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email, $this->needsUtf8Safety)) {
				$res = false;
			}
			
			//in Teilbereiche splitten
			$email_array = explode("@", $email);
			$local_array = explode(".", $email_array[0]);
			
			for ($i = 0; $i < sizeof($local_array); $i++) {
				if (!HtmlFormTools::auto_preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i], $this->needsUtf8Safety)) {
					$res = false;
				}
			}
			
			//Behandlung von IP-Domain
			if (!HtmlFormTools::auto_preg_match('/^\[?[0-9\.]+\]?$/', $email_array[1], $this->needsUtf8Safety)) {
				$domain_array = explode(".", $email_array[1]);
				
				//falsche Anzahl von Bereichen?
				if (sizeof($domain_array) < 2) {
					$res = false;
				}
				
				//Bereiche checken
				for ($i = 0; $i < sizeof($domain_array); $i++) {
					if (!HtmlFormTools::auto_preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]{2,5}))$/', $domain_array[$i], $this->needsUtf8Safety)) {
						$res = false;
					}
				}
			}
			
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_EMAIL);
		}
		
		return $res;
	}
	
	
	
	private function url($X){
		$res = true;
		
		// Zugriffsart
		$urlregex = "^(https?|ftp)\:\/\/";

		// optionale Angaben zu User und Passwort
		$urlregex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";

		// Hostname oder IP-Angabe
		//$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*";  // http://x = allowed (ex. http://localhost, http://routerlogin)
		//$urlregex .= "[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)+";  // http://x.x = minimum
		$urlregex .= "([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}";  // http://x.xx(x) = minimum
		//use only one of the above

		// optionale Portangabe
		$urlregex .= "(\:[0-9]{2,5})?";
		// optionale Pfadangabe
		$urlregex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
		// optionaler GET-Query
		$urlregex .= "(\?[a-z+&\$_.-][a-z0-9;:@\/&%=+\$_.-]*)?";
		// optionaler Seitenanker
		$urlregex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
		
		foreach( $this->values as $url ){
			if( !HtmlFormTools::auto_preg_match('/'.$urlregex.'/i', $url, $this->needsUtf8Safety) ) $res = false;
			if( $res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_URL);
		}
		
		return $res;
	}
	
	
	
	private function date($X){
		$res = true;
		
		$dateregex = '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/i';
		
		foreach( $this->values as $date ){
			if( !HtmlFormTools::auto_preg_match($dateregex, $date, $this->needsUtf8Safety) || ( strtotime($date) === false ) ) $res = false;
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATE);
		}
		
		return $res;
	}
	
	
	
	private function dateISO($X){
		$res = true;
		
		$dateregex = '/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/i';
		
		foreach( $this->values as $date ){
			$dateArray = explode('-', $date);
			
			if(
				!HtmlFormTools::auto_preg_match($dateregex, $date, $this->needsUtf8Safety) 
				|| (strtotime($dateArray[1].'/'.$dateArray[2].'/'.$dateArray[0]) === false)
			){
				$res = false;
			}
			
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATE_ISO);
		}
		
		return $res;
	}
	
	
	
	private function dateDE($X){
		$res = true;
		
		$dateregex = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{3,4}$/i';
		
		foreach( $this->values as $date ){
			$dateArray = explode('.', $date);
			
			if( 
				!HtmlFormTools::auto_preg_match($dateregex, $date, $this->needsUtf8Safety)
				|| (strtotime($dateArray[1].'/'.$dateArray[0].'/'.$dateArray[2]) === false)
			){
				$res = false;
			}
			
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATE_DE);
		}
		
		return $res;
	}
	
	
	
	private function number($X){
		$res = true;
		
		foreach( $this->values as $number ){
			if( ($number != (string)(integer)$number) && ($number != (string)(float)$number) ) $res = false;
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_NUMBER);
		}
		
		return $res;
	}
	
	
	
	private function numberDE($X){
		$res = true;
		
		foreach( $this->values as $number ){
			$numberUS = str_replace(',', '.', $number);
			
			if( 
				(HtmlFormTools::auto_strpos($number, '.', $this->needsUtf8Safety) !== false)
				|| ( HtmlFormTools::auto_strpos($number, ',', $this->needsUtf8Safety) == 0 )
				|| ( ($number != (string)(integer)$number) && ($numberUS != (string)(float)$numberUS) ) 
			){
				$res = false;
			}
			
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_NUMBER_DE);
		}
		
		return $res;
	}
	
	
	
	private function digits($X){
		$res = true;
		
		foreach( $this->values as $val ){
			if( !HtmlFormTools::auto_preg_match('/^[0-9]+$/i', $val, $this->needsUtf8Safety) ) $res = false;
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DIGITS);
		}
		
		return $res;
	}
	
	
	
	private function creditcard($X){
		$res = true;
		
		$creditCardRegEx = '/^[0-9]{3,4}\-[0-9]{4}\-[0-9]{4}\-[0-9]{4}$/i';
		
		foreach( $this->values as $creditCardNumber ){
			if( !HtmlFormTools::auto_preg_match($creditCardRegEx, $creditCardNumber, $this->needsUtf8Safety) ) $res = false;
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_CREDITCARD);
		}
		
		return $res;
	}
	
	
	
	private function characterclass($class){
		$res = true;
		
		$characterClassRegEx = "^[$class]*$";
		
		foreach( $this->values as $value ){
			if( !HtmlFormTools::auto_preg_match('/'.$characterClassRegEx.'/i', $value, $this->needsUtf8Safety) ) $res = false;
			if( !$res ) break;
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace(array('%name%', '%class%'), array($this->fieldName, $class), MSG_CHARACTERCLASS);
		}
		
		return $res;
	}
	
	
	
	//--|questions---------
	
	private function hasValuesToValidate(){
		foreach( $this->values as $val ){
			if( "$val" != "" ){
				return true;
			}
		}
	
		return false;
	}
	
	
	
	//---|functionality----------
	
	public function process(){
		if( !(!$this->hasValuesToValidate() && isset($this->rules['optional'])) ){	
			if( $this->fieldName != '' ){
				require_once('messages/'.$this->messageLanguage.'.inc.php');
			}
			
			foreach( $this->rules as $function => $param ){
				$caseValidity = $this->$function($param);
				$this->isValid = $this->isValid && $caseValidity;
			}
		}
		
		return $this->isValid;
	}
	
	
	
	//---|output----------
	
	public function printMessageQueue($onlyCustomMessages = false){
		$msg = '';
		
		$showOnlyAutoErrorMessages = ($this->customErrorMessage == '') && (!$onlyCustomMessages || $this->forceErrorMessageOutput);
		$showOnlyCustomErrorMessage = ($this->customErrorMessage != '') && !$this->isValid;
		
		if( $showOnlyAutoErrorMessages ){
			foreach( $this->messageQueue as $m ){
				$msg .= '<div class="'.self::MESSAGECLASS.'">'.$m.'</div>';
			}
		} elseif( $showOnlyCustomErrorMessage ){
			$msg .= '<div class="'.self::MESSAGECLASS.'">'.$this->customErrorMessage.'</div>';
		}
		
		return $msg;
	}
}

?>