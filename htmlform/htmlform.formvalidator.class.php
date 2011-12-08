<?php

//--|includes----------

require_once 'htmlform.tools.class.php';



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
 * @version 0.85 beta
 * @package validation
 */
class FormValidator{
	
	/**
	 * css-class for individual validation-error-messages
	 * @var String
	 */
	const MESSAGECLASS = 'htmlform_message_div';
	
	// ***
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
	
	
	
	/**
	 * Factory method for FormValidator, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @return FormValidator new FormValidator-instance
	 */
	public static function get(){
		$res = new FormValidator();
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the language for all validation-error-messages take from message dictionaries.
	 * The available languages and their names are defined by the dictionaries present
	 * in /messages.
	 * 
	 * @see messages/english.inc.php
	 * @param String $language the name of the language to use
	 * @return FormValidator method owner
	 */
	public function setMessageLanguage($language){
		$this->messageLanguage = $language;
		return $this;
	}
	
	
	
	/**
	 * Sets a custom error message for the validator, overwriting all standard messages from
	 * the dictionaries.
	 * Use this to further describe special error cases, to aid your users.
	 * 
	 * @param String $message the message to display in case of a validation error
	 * @return FormValidator method owner
	 */
	public function setErrorMessage($message){
		$this->customErrorMessage = "$message";
		return $this;
	}
	
	
	
	/**
	 * Tells the validator to treat standard messages as custom in the context of this validator.
	 * This method has the purpose to use standard messages selectively in a form where standard
	 * messages are disabled by default.
	 * 
	 * @return FormValidator method owner
	 */
	public function setAutoErrorMessagesAsCustom(){
		$this->forceErrorMessageOutput = true;
		$this->customErrorMessage = '';
		return $this;
	}
	
	
	
	/**
	 * Tells the validator the name of the element in validates.
	 * In most cases this is the value of the widget's label.
	 * This information is necessary for standard-message-display and normally provided
	 * automatically when inserting a validator into an element.
	 * 
	 * @param String $name the description of the widget the validator validates
	 * @return FormValidator method owner
	 */
	public function setFieldName($name){
		$this->fieldName = $name;
		return $this;
	}
	
	
	
	/**
	 * Set the value the validator should validate.
	 * Used for widgets with single values.
	 * 
	 * @param String $value current value of the connected widget
	 * @return FormValidator method owner
	 */
	public function setValue($value){
		$this->values = array("$value");
		return $this;
	}
	
	
	
	/**
	 * Set the values the validator should validate.
	 * Used for widgets with multiple values.
	 * 
	 * @param array $values
	 * @return FormValidator method owner
	 */
	public function setValues(Array $values){
		$this->values = $values;
		return $this;
	}
	
	
	
	/**
	 * Tells the validator to treat all validated values as utf-8-encoded or vice versa.
	 * 
	 * @param Boolean $needed multibyte-security needed yes/no
	 * @return FormValidator method owner
	 */
	public function setUtf8Safety($needed = true){
		$this->needsUtf8Safety = $needed;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - Includes a predefined validation-result into the ruleset.
	 * The result may have two characteristics:
	 * Eiter it's a boolean value, simply telling the validator if the test has succeeded
	 * or failed. Or it's a string, with an empty string being a successful test and a
	 * non-empty string being a failed test, where the string is the error message to display.
	 * This is not a custom message, but rather an override for the standard message, which is
	 * rather nondescript for custom cases.
	 * 
	 * @param Boolean/String $customResult the precalculated result of the custom case 
	 * @return FormValidator method owner
	 */
	public function setCustomCase($customResult){
		$this->rules['customcase'] = $customResult;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget needs to have a value.
	 * Either this means that the value can't be an empty string or that the amount
	 * of values mustn't be 0.
	 * 
	 * @return FormValidator method owner
	 */
	public function setRequired(){
		$this->rules['required'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget needs to have a non-empty value.
	 * Either this means that the value can't be an empty string or one consisting of whitespace
	 * or that the amount of values mustn't be 0.
	 * 
	 * @param Array $additionalEmptyValues array of additional widget values to be considered empty besides an empty string
	 * @return FormValidator method owner
	 */
	public function setNotEmpty(Array $additionalEmptyValues = array()){
		$this->rules['notempty'] = $additionalEmptyValues;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value becomes optional, either its not set at all, or it get
	 * validated by all other present rules.
	 * 
	 * @param Array $additionalEmptyValues array of additional widget values to be considered empty besides an empty string
	 * @return FormValidator method owner
	 */
	public function setOptional(Array $additionalEmptyValues = array()){
		$this->rules['optional'] = $additionalEmptyValues;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The string-representation of the widget's value needs
	 * to have a certain length. 
	 * For multiple values this method uses the amount of values.
	 * 
	 * @param uint $minlength the minimum length the value has to have
	 * @return FormValidator method owner
	 */
	public function setMinLength($minlength){
		$this->rules['minlength'] =  (integer)$minlength;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The string-representation of the widget's value may only
	 * have a certain length.
	 * For multiple values this method uses the amount of values. 
	 * 
	 * @param uint $maxlength the maximum length the value can have
	 * @return FormValidator method owner
	 */
	public function setMaxLength($maxlength){
		$this->rules['maxlength'] =  (integer)$maxlength;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The string-representation of the widget's value must have
	 * a minimum length and must not exceed a maximum length.
	 * In general this is a shortcut for setMinLength()+setMaxLength().
	 * For multiple values this method uses the amount of values.
	 * 
	 * @param Array[uint] $range first element is min, second is max 
	 * @return FormValidator method owner
	 */
	public function setRangeLength(Array $range){
		$this->rules['rangelength'] =  $range;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The number-representation of the widget's value must
	 * have a minimum numeric value.
	 * For multiple values this rule will check each value individually.
	 * 
	 * @param int $min the minimum numeric value
	 * @return FormValidator method owner
	 */
	public function setMin($min){
		$this->rules['min'] = (integer)$min;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The number-representation of the widget's value can
	 * only have a maximum numeric value.
	 * For multiple values this rule will check each value individually. 
	 * 
	 * @param int $max the maximum numeric value
	 * @return FormValidator method owner
	 */
	public function setMax($max){
		$this->rules['max'] = (integer)$max;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The number-representation of the widget's value
	 * must have a minimum and can only be of a maximum numeric value.
	 * In general this is a shortcut for setMin()+setMax().
	 * For multiple values this rule will check each value individually.
	 * 
	 * @param Array[int] $range
	 * @return FormValidator method owner
	 */
	public function setRange(Array $range){
		$this->rules['range'] = $range;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a valid eMail-address.
	 * This is no run-of-the-mill check, but quite elaborate.
	 * 
	 * @return FormValidator method owner
	 */
	public function setEmail(){
		$this->rules['email'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a valid url.
	 * This is no run-of-the-mill check, but quite elaborate.
	 * Url has to look something like this.
	 * [http(s)/ftp][subdomain/domain][domain/tld]([tld])([port])([query])([anchor])
	 * 
	 * @return FormValidator method owner
	 */
	public function setUrl(){
		$this->rules['url'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard american date.
	 * Either dd/mm/yyyy or mm/dd/yyyy. 
	 * 
	 * @return FormValidator method owner
	 */
	public function setDate(){
		$this->rules['date'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard iso-date.
	 * dd-mm-yyyy 
	 * 
	 * @return FormValidator method owner
	 */
	public function setDateISO(){
		$this->rules['dateISO'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard german date.
	 * dd.mm.yyyy
	 * 
	 * @return FormValidator method owner
	 */
	public function setDateDE(){
		$this->rules['dateDE'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard english decimal number.
	 * 123(.456) 
	 * 
	 * @return FormValidator method owner
	 */
	public function setNumber(){
		$this->rules['number'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard german decimal number.
	 * 123(,456)
	 * 
	 * @return FormValidator method owner
	 */
	public function setNumberDE(){
		$this->rules['numberDE'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset -	The number-representation of the widget's value must be only
	 * digits without any other characters.
	 * 1234567890  
	 * 
	 * @return FormValidator method owner
	 */
	public function setDigits(){
		$this->rules['digits'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset -	The widget's value has have the form of a valid creditcard-number.
	 * (1)234-1234-1234-1234 
	 * 
	 * @return FormValidator method owner
	 */
	public function setCreditcard(){
		$this->rules['creditcard'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must only consist of characters defined in a given
	 * regex-character-class.
	 * a-zA-Z0-9äüöÄÜÖß for example
	 * 
	 * @param String $regExCharacterClass the regex-character class to check against
	 * @return FormValidator method owner
	 */
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
	
	
	
	private function notempty($additionalEmptyValues){
		$res = true;
		
		$emptyValues = array('');
		foreach( $additionalEmptyValues as $additionalEmptyValue ){
			$emptyValues[] = "$additionalEmptyValue";
		}
		
		if( count($this->values) == 1 ){
			$res = !in_array(trim($this->values[0]), $emptyValues);
		} else {
			$res = !(count($this->values) == 0);
		}
		
		if( !$res && ($this->fieldName != '') ){
			$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_NOTEMPTY);
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
	
	private function hasValuesToValidate(Array $additionalEmptyValues = array()){
		$emptyValues = array('');
		foreach( $additionalEmptyValues as $additionalEmptyValue ){
			$emptyValues[] = "$additionalEmptyValue";
		}
	
		foreach( $this->values as $val ){
			if( !in_array("$val", $emptyValues) ){
				return true;
			}
		}
	
		return false;
	}
	
	
	
	//---|functionality----------
	
	/**
	 * Starts the validation if the given values accourding to all set rules of the validator.
	 * Normally called automatically by the widget the values originate from.
	 * 
	 * @return Boolean value(s) is/are valid yes/no
	 */
	public function process(){
		if( !isset($this->rules['optional']) || $this->hasValuesToValidate($this->rules['optional']) ){	
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
	
	/**
	 * Return all aggregated error messages of the validator.
	 * This method will only return something wortwhile after process() has been called, since before that
	 * there are no messages queued.
	 * 
	 * @param Boolean $onlyCustomMessages sets if all standard messages should be used or only custom ones
	 * @return String all compiled messages of the validator
	 */
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