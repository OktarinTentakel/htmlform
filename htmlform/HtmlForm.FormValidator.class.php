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
 * @version 0.95 beta
 * @package validation
 */
class FormValidator {
	
	/**
	 * css-class for individual validation-error-messages
	 * @var String
	 */
	const MESSAGECLASS = 'htmlform_message_div';
	
	// ***
	private $id;
	
	private $messageLanguage;
	private $messageQueue;
	private $customErrorMessage;
	private $forceErrorMessageOutput;
	private $fieldName;
	
	private $rules;
	private $values;
	private $isValid;
	
	private $usesJavascriptValidation;
	private $selector;
	private $errorSelector;
	
	private $needsUtf8Safety;
	
	private function __construct(){
		$this->id = uniqid();
		
		$this->messageLanguage = 'english';
		$this->messageQueue = array();
		$this->customErrorMessage = '';
		$this->forceErrorMessageOutput = false;
		$this->fieldName = '';
		
		$this->rules = array();
		$this->values = array();
		$this->isValid = true;
		
		$this->usesJavascriptValidation = false;
		$this->selector = '';
		$this->errorSelector = '';
		
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
		$this->fieldName = "$name";
		return $this;
	}
	
	
	
	/**
	 * Tells the validator the name of the element's data in the submit-resultset.
	 * This is normally identical to the html-name-attribute's value of the rendered element.
	 * 
	 * @return FormValidator method owner
	 */
	public function setDataName($dataName){
		if( $this->selector == '' ){
			$this->selector = "[name=".$dataName."], [name=\"".$dataName."[]\"]";
		}
		
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
	 * You can also provide an Array, where the first element is the PHP-expression, while the second element
	 * must be valid JS-code to return the validation result from. The JS-validation-code has to set the var res
	 * (don't redeclare the var) to the result of the validation.
	 * 
	 * You can even set this handler up for async validation by not setting res, but instead calling customCaseAsyncFinalize(res)
	 * at the end of the async request. Here two examples for special-JS-code:
	 * 
	 * sequential:
	 * ->setCustomCase(array(true, 'res = $('body .class_'+$(this).val()).length > 0;'))
	 * 
	 * async:
	 * ->setCustomCase(array(true, '$.getJSON('/service:validation/something', {a:'b'}, function(data){ customCaseAsyncFinalize(data.res) });'))
	 * 
	 * @param Boolean/String/Array $customResult the precalculated result of the custom case 
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
	 * (d)d/(m)m/yyyy 
	 * 
	 * @return FormValidator method owner
	 */
	public function setDate(){
		$this->rules['date'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be standard american time.
	 * (h)h:mm(:ss)am|pm
	 *
	 * @return FormValidator method owner
	 */
	public function setTime(){
		$this->rules['time'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be standard american datetime.
	 * Either dd/mm/yyyy + (h)h:mm(:ss)am|pm
	 *
	 * @return FormValidator method owner
	 */
	public function setDateTime(){
		$this->rules['datetime'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard iso-date.
	 * yyyy-mm-dd
	 * 
	 * @return FormValidator method owner
	 */
	public function setDateISO(){
		$this->rules['dateISO'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be standard iso-time.
	 * hh:mm:ss
	 *
	 * @return FormValidator method owner
	 */
	public function setTimeISO(){
		$this->rules['timeISO'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be standard iso-datetime.
	 * yyyy-mm-dd(T| )hh:mm:ss
	 *
	 * @return FormValidator method owner
	 */
	public function setDateTimeISO(){
		$this->rules['datetimeISO'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard german date.
	 * (d)d.(m)m.yyyy
	 * 
	 * @return FormValidator method owner
	 */
	public function setDateDE(){
		$this->rules['dateDE'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard german time.
	 * hh:mm:ss(h)
	 *
	 * @return FormValidator method owner
	 */
	public function setTimeDE(){
		$this->rules['timeDE'] = true;
		return $this;
	}
	
	
	
	/**
	 * Adds a rule to the validator's ruleset - The widget's value must be a standard german datetime.
	 * (d)d.(m)m.yyyy hh:mm:ss(h)
	 *
	 * @return FormValidator method owner
	 */
	public function setDateTimeDE(){
		$this->rules['datetimeDE'] = true;
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
	
	private function customcase($customRes, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			if( is_array($customRes) ){
				$customRes = array_shift($customRes);
			}
			
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
		} else {
			$code = "
				var customCaseMessageCompile = function(customRes){
					if( $.type(customRes) == 'boolean' ){
						if( !customRes && ".$this->exprToJsBool(defined('MSG_CUSTOMCASE'))." ){
							if( \$label.length > 0 ){
								lastMsg = '".MSG_CUSTOMCASE."';
								$.each([['%name%', labelTxt]], function(index, value){
									var rex = new RegExp(value[0], 'g');
									lastMsg = lastMsg.replace(rex, value[1]);
								});
							}
						}
					} else {
						if( ''+customRes != '' ){
							lastMsg = ''+customRes;
							res = false;
						} else {
							res = true;
						}
					}
				};
			";
			
			if( is_array($customRes) && (count($customRes) < 2) ){
				$customRes = array_shift($customRes);
			}
			
			if( !is_array($customRes) ){
				$code .= "
					res = ".(
						((is_string($customRes) && ($customRes != '')) || is_null($customRes))
						? 'false'
						: (
							is_bool($customRes)
							? $this->exprToJsBool($customRes)
							: 'true'
						)
					).";
				";
			} else {
				$userCode = $customRes[1];
				
				$code .= "
					var customCaseAsyncFinalize = function(res){
						asyncError = ($.type(res) == 'boolean') ? res : (''+res == '');
						
						if( asyncError ){
							HTMLFORM.validation.markError(errorSelector, false);
							customCaseMessageCompile(res);
						}
					};
					
					".$userCode."
				";
			}
			
			return $code."
				customCaseMessageCompile(res);
			";
		}
	}
	
	
	
	private function required($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
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
		} else {
			return "
				if( values.length == 1 ){
					res = !(values[0] == '');
				} else {
					res = !(values.length == 0);
				}
				
				if( !res && ".$this->exprToJsBool(defined('MSG_REQUIRED'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_REQUIRED."';
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function notempty($additionalEmptyValues, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
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
		} else {
			$emptyValueJsArray = '[\'\'';
			foreach( $additionalEmptyValues as $additionalEmptyValue ){
				$emptyValueJsArray .= ", '$additionalEmptyValue'";
			}
			$emptyValueJsArray .= ']';
			
			return "
				var notEmptyEmptyValues = ".$emptyValueJsArray.";
			
				if( values.length == 1 ){
					res = ($.inArray($.trim(values[0]), notEmptyEmptyValues) == -1);
				} else {
					res = !(values.length == 0);
				}
				
				if( !res && ".$this->exprToJsBool(defined('MSG_NOTEMPTY'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_NOTEMPTY."';
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function optional($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			return true;
		} else {
			return '';
		}
	}
	
	
	
	private function minlength($minlength, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
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
		} else {
			$algorithmCode = "
				if( values.length == 1 ){
					res = values[0].length >= ".$minlength.";
				} else {
					res = (values.length >= ".$minlength.");
				}
			";
			
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_MINLENGTH'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_MINLENGTH."';
							$.each(
								[
									['%name%', labelTxt],
									['%count%', '".$minlength."']
								],
								function(index, value){
									var rex = new RegExp(value[0], 'g');
									lastMsg = lastMsg.replace(rex, value[1]);
								}
							);
						}
					}
				";
			}
		}
	}
	
	
	
	private function maxlength($maxlength, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
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
		} else {
			$algorithmCode = "
				if( values.length == 1 ){
					res = values[0].length <= ".$maxlength.";
				} else {
					res = (values.length <= ".$maxlength.");
				}
			";
			
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_MAXLENGTH'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_MAXLENGTH."';
							$.each(
								[
									['%name%', labelTxt],
									['%count%', '".$maxlength."']
								],
								function(index, value){
									var rex = new RegExp(value[0], 'g');
									lastMsg = lastMsg.replace(rex, value[1]);
								}
							);
						}
					}
				";
			}
		}
	}
	
	
	
	private function rangelength(Array $range, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			if( count($range) >= 2 ){
				$res = $this->minlength((integer)$range[0], false, true) && $this->maxlength((integer)$range[1], false, true);
				
				if( !$res && ($this->fieldName != '') ){
					$this->messageQueue[] = str_replace(
						array('%name%', '%min%', '%max%'),
						array($this->fieldName, (integer)$range[0], (integer)$range[1]),
						MSG_RANGELENGTH
					);
				}
			}
			
			return $res;
		} else {
			return "
				".(
					(count($range) >= 2)
					? "
						var rangeLengthMinLength = function(){
							var res = true;
							".$this->minlength((integer)$range[0], true, true)."
							return res;
						};
						
						var rangeLengthMaxLength = function(){
							var res = true;
							".$this->maxlength((integer)$range[1], true, true)."
							return res;
						};
						
						res = rangeLengthMinLength() && rangeLengthMaxLength();
						
						if( !res && ".$this->exprToJsBool(defined('MSG_RANGELENGTH'))." ){
							if( \$label.length > 0 ){
								lastMsg = '".MSG_RANGELENGTH."';
								$.each(
									[
										['%name%', labelTxt],
										['%min%', '".((integer)$range[0])."'],
										['%max%', '".((integer)$range[1])."']
									],
									function(index, value){
										var rex = new RegExp(value[0], 'g');
										lastMsg = lastMsg.replace(rex, value[1]);
									}
								);
							}
						}
					"
					: ''
				)."
			";
		}
	}
	
	
	
	private function min($min, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			foreach( $this->values as $val ){
				$res = (is_numeric($val) && ((integer)$val >= $min));
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace(array('%name%', '%count%'), array($this->fieldName, $min), MSG_MIN);
			}
			
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					res = res && ($.isNumeric(value) && (parseInt(value) >= ".$min."));
					if( !res ) return false;
				});
			";
				
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_MIN'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_MIN."';
							$.each(
								[
									['%name%', labelTxt],
									['%count%', '".$min."']
								],
								function(index, value){
									var rex = new RegExp(value[0], 'g');
									lastMsg = lastMsg.replace(rex, value[1]);
								}
							);
						}
					}
				";
			}
		}
	}
	
	
	
	private function max($max, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			foreach( $this->values as $val ){
				$res = (is_numeric($val) && ((integer)$val <= $max));
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace(array('%name%', '%count%'), array($this->fieldName, $max), MSG_MAX);
			}
			
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					res = res && ($.isNumeric(value) && (parseInt(value) <= ".$max."));
					if( !res ) return false;
				});
			";
				
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_MAX'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_MAX."';
							$.each(
								[
									['%name%', labelTxt],
									['%count%', '".$max."']
								],
								function(index, value){
									var rex = new RegExp(value[0], 'g');
									lastMsg = lastMsg.replace(rex, value[1]);
								}
							);
						}
					}
				";
			}
		}
	}
	
	
	
	private function range($range, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			if( count($range) >= 2 ){
				$res = $this->min((integer)$range[0], false, true) && $this->max((integer)$range[1], false, true);
				
				if( !$res && ($this->fieldName != '') ){
					$this->messageQueue[] = str_replace(
						array('%name%', '%min%', '%max%'),
						array($this->fieldName, (integer)$range[0], (integer)$range[1]),
						MSG_RANGE
					);
				}
			}		
			
			return $res;
		} else {
			return "
				".(
					(count($range) >= 2)
					? "
						var rangeMin = function(){
							var res = true;
							".$this->min((integer)$range[0], true, true)."
							return res;
						};
						
						var rangeMax = function(){
							var res = true;
							".$this->max((integer)$range[1], true, true)."
							return res;
						};
						
						res = rangeMin() && rangeMax();
						
						if( !res && ".$this->exprToJsBool(defined('MSG_RANGE'))." ){
							if( \$label.length > 0 ){
								lastMsg = '".MSG_RANGE."';
								$.each(
									[
										['%name%', labelTxt],
										['%min%', '".((integer)$range[0])."'],
										['%max%', '".((integer)$range[1])."']
									],
									function(index, value){
										var rex = new RegExp(value[0], 'g');
										lastMsg = lastMsg.replace(rex, value[1]);
									}
								);
							}
						}
					"
					: ''
				)."
			";
		}
	}
	
	
	
	private function email($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			foreach( $this->values as $email ){
				//gucken ob @ da und ob Längen stimmen
				if( !HtmlFormTools::auto_preg_match('/^[^@]{1,64}@[^@]{1,255}$/', $email, $this->needsUtf8Safety) ){
					$res = false;
				}
				
				if( $res ){
					//in Teilbereiche splitten
					$email_array = explode("@", $email);
					$local_array = explode(".", $email_array[0]);
					
					for ($i = 0; $i < sizeof($local_array); $i++) {
						if( !HtmlFormTools::auto_preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i], $this->needsUtf8Safety) ){
							$res = false;
						}
					}
					
					//Behandlung von IP-Domain
					if( !HtmlFormTools::auto_preg_match('/^\[?[0-9\.]+\]?$/', $email_array[1], $this->needsUtf8Safety) ){
						$domain_array = explode(".", $email_array[1]);
						
						//falsche Anzahl von Bereichen?
						if( sizeof($domain_array) < 2 ){
							$res = false;
						}
						
						//Bereiche checken
						for( $i = 0; $i < sizeof($domain_array); $i++ ){
							if( !HtmlFormTools::auto_preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]{2,5}))$/', $domain_array[$i], $this->needsUtf8Safety) ){
								$res = false;
							}
						}
					}
				}
				
				if( !$res ){
					break;
				}
			}
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_EMAIL);
			}
			
			return $res;
		} else {
			return "
				$.each(values, function(index, value){
					var ruleRes = /^[^@]{1,64}@[^@]{1,255}$/.test(value);
				
					if( ruleRes ){
						var email_array = value.split('@');
						var local_array = email_array[0].split('.');
						
						for ( var i = 0; i < local_array.length; i++) {
							if( !(/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\\\"[^(\\|\\\")]{0,62}\\\"))$/.test(local_array[i])) ){
								ruleRes = false;
							}
						}
						
						var domain_array = '';
						if( !(/^\[?[0-9\.]+\]?$/.test(email_array[1])) ){
							domain_array = email_array[1].split('.');
							
							if( domain_array.length < 2 ){
								ruleRes = false;
							}
							
							for( i = 0; i < domain_array.length; i++ ){
								if( !(/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]{2,5}))$/.test(domain_array[i])) ){
									ruleRes = false;
								}
							}
						}
					}
					
					res = res && ruleRes;
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_EMAIL'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_EMAIL."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function url($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
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
		} else {
			return "
				$.each(values, function(index, value){
					res = res && /^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?([a-z0-9+\$_-]+\.)*[a-z0-9+\$_-]{2,3}(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@\/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$/.test(value);
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_URL'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_URL."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function date($X, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			$dateregex = '/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/';
			
			foreach( $this->values as $date ){
				if( $internal ){
					$datetimeParts = explode(' ', $date, 2);
					
					if( count($datetimeParts) >= 2 ){
						$date = trim($datetimeParts[0]);
					} else {
						$date = '';
					}
				}
				
				$dateArray = explode('/', $date);
				
				if(
					($date == '')
					|| !HtmlFormTools::auto_preg_match($dateregex, $date, $this->needsUtf8Safety)
					|| (strtotime($dateArray[2].'-'.$dateArray[0].'-'.$dateArray[1]) === false)
					|| !checkdate(intval($dateArray[0]), intval($dateArray[1]), intval($dateArray[2]))
				){
					$res = false;
				}
				
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATE);
			}
			
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					".($internal 
						? "
							var datetimeParts = value.split(' ');
							if( datetimeParts.length >= 2 ){
								value = $.trim(datetimeParts[0]);
							} else {
								value = '';
							}
						"
						: ''
					)."
					
					var formatValid = /^(0?[1-9]|1[0-2])\/(0?[1-9]|[1-2][0-9]|3[0-1])\/([1-2][0-9]{3})$/.test(value);
					var splitValue = formatValid ? value.split('/') : null;
					
					if( formatValid ){
						for( var i = 0; i < 2; i++ ){
							if( splitValue[i].length  < 2 ){
								splitValue[i] = '0'+splitValue[i];
							}
						}
					}
					
					var date = formatValid ? new Date(splitValue[2]+'-'+splitValue[0]+'-'+splitValue[1]) : null;
					var ruleRes = 
						((date !== null) && (splitValue !== null))
						? (
							!/Invalid|NaN/.test(date)
							&& (parseInt(splitValue[0]) == (date.getMonth() + 1))
							&& (parseInt(splitValue[1]) == (date.getDate()))
							&& (parseInt(splitValue[2]) == (date.getFullYear()))
						)
						: false
					;
					
					res = res && ruleRes;
				});
			";
			
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_DATE'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_DATE."';
							
							$.each([['%name%', labelTxt]], function(index, value){
								var rex = new RegExp(value[0], 'g');
								lastMsg = lastMsg.replace(rex, value[1]);
							});
						}
					}
				";
			}
		}
	}
	
	
	
	private function time($X, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
				
			$timeregex = '/^((0?[0-9]|1[0-1])\:[0-5][0-9](\:[0-5][0-9])? ?(am|AM|pm|PM)|12\:[0-5][0-9](\:[0-5][0-9])? ?(pm|PM))$/';
				
			foreach( $this->values as $time ){
				if( $internal ){
					$datetimeParts = explode(' ', $time, 2);
						
					if( count($datetimeParts) >= 2 ){
						$time = trim($datetimeParts[1]);
					} else {
						$time = '';
					}
				}
				
				if(
					($time == '')
					|| !HtmlFormTools::auto_preg_match($timeregex, $time, $this->needsUtf8Safety)
					|| (strtotime($time) === false)
				){
					$res = false;
				}
				
				if( !$res ) break;
			}
				
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_TIME);
			}
				
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					".($internal 
						? "
							var datetimeParts = value.split(' ');
							if( datetimeParts.length >= 2 ){
								value = $.trim(datetimeParts[1]);
								if( datetimeParts.length >= 3 ){
									value += ' '+$.trim(datetimeParts[2])
								}
							} else {
								value = '';
							}
						"
						: ''
					)."
					
					var ruleRes = /^((0?[0-9]|1[0-1])\:[0-5][0-9](\:[0-5][0-9])? ?(am|AM|pm|PM)|12\:[0-5][0-9](\:[0-5][0-9])? ?(pm|PM))$/.test(value);
					res = res && ruleRes;
				});
			";
				
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_TIME'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_TIME."';
							
							$.each([['%name%', labelTxt]], function(index, value){
								var rex = new RegExp(value[0], 'g');
								lastMsg = lastMsg.replace(rex, value[1]);
							});
						}
					}
				";
			}
		}
	}
	
	
	
	private function datetime($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = $this->date(null, false, true) && $this->time(null, false, true);
				
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATETIME);
			}
	
			return $res;
		} else {
			return "
				var datetimeDate = function(){
					var res = true;
					".$this->date(null, true, true)."
					return res;
				};
				
				var datetimeTime = function(){
					var res = true;
					".$this->time(null, true, true)."
					return res;
				};
				
				res = datetimeDate() && datetimeTime();
				
				if( !res && ".$this->exprToJsBool(defined('MSG_DATETIME'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_DATETIME."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function dateISO($X, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			$dateregex = '/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}$/';
			
			foreach( $this->values as $date ){
				if( $internal ){
					$datetimeParts = explode(' ', str_replace('T', ' ', $date), 2);
						
					if( count($datetimeParts) >= 2 ){
						$date = trim($datetimeParts[0]);
					} else {
						$date = '';
					}
				}
				
				$dateArray = explode('-', $date);
				
				if(
					($date == '')
					|| !HtmlFormTools::auto_preg_match($dateregex, $date, $this->needsUtf8Safety) 
					|| (strtotime($date) === false)
					|| !checkdate(intval($dateArray[1]), intval($dateArray[2]), intval($dateArray[0]))
				){
					$res = false;
				}
				
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATE_ISO);
			}
			
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					".($internal 
						? "
							var datetimeParts = value.replace(/T/, ' ').split(' ');
							if( datetimeParts.length >= 2 ){
								value = $.trim(datetimeParts[0]);
							} else {
								value = '';
							}
						"
						: ''
					)."
								
					var formatValid = /^([1-2][0-9]{3})\-(0?[1-9]|1[0-2])\-(0?[1-9]|[1-2][0-9]|3[0-1])$/.test(value);
					var splitValue = formatValid ? value.split('-') : null;
					
					if( formatValid ){
						for( var i = 1; i < 3; i++ ){
							if( splitValue[i].length  < 2 ){
								splitValue[i] = '0'+splitValue[i];
							}
						}
					}
					
					var date = formatValid ? new Date(splitValue.join('-')) : null;
					var ruleRes = 
						((date !== null) && (splitValue !== null))
						? (
							!/Invalid|NaN/.test(date)
							&& (parseInt(splitValue[0]) == (date.getFullYear()))
							&& (parseInt(splitValue[1]) == (date.getMonth() + 1))
							&& (parseInt(splitValue[2]) == (date.getDate()))
						)
						: false
					;
					
					res = res && ruleRes;
				});
			";
				
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_DATE_ISO'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_DATE_ISO."';
							
							$.each([['%name%', labelTxt]], function(index, value){
								var rex = new RegExp(value[0], 'g');
								lastMsg = lastMsg.replace(rex, value[1]);
							});
						}
					}
				";
			}
		}
	}
	
	
	
	private function timeISO($X, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
	
			$timeregex = '/^([0-1][0-9]|2[0-3])\:[0-5][0-9]\:[0-5][0-9]$/';
	
			foreach( $this->values as $time ){
				if( $internal ){
					$datetimeParts = explode(' ', str_replace('T', ' ', $time), 2);
	
					if( count($datetimeParts) >= 2 ){
						$time = trim($datetimeParts[1]);
					} else {
						$time = '';
					}
				}
	
				if(
					($time == '')
					|| !HtmlFormTools::auto_preg_match($timeregex, $time, $this->needsUtf8Safety)
					|| (strtotime($time) === false)
				){
					$res = false;
				}
	
				if( !$res ) break;
			}
	
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_TIME_ISO);
			}
	
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					".($internal
						? "
							var datetimeParts = value.replace(/T/, ' ').split(' ');
							if( datetimeParts.length >= 2 ){
								value = $.trim(datetimeParts[1]);
							} else {
								value = '';
							}
						"
						: ''
					)."
					
					var ruleRes = /^([0-1][0-9]|2[0-3])\:[0-5][0-9]\:[0-5][0-9]$/.test(value);
					res = res && ruleRes;
				});
			";
	
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_TIME_ISO'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_TIME_ISO."';
							
							$.each([['%name%', labelTxt]], function(index, value){
								var rex = new RegExp(value[0], 'g');
								lastMsg = lastMsg.replace(rex, value[1]);
							});
						}
					}
				";
			}
		}
	}
	
	
	
	private function datetimeISO($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = $this->dateISO(null, false, true) && $this->timeISO(null, false, true);
	
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATETIME_ISO);
			}
	
			return $res;
		} else {
			return "
				var datetimeIsoDate = function(){
					var res = true;
					".$this->dateISO(null, true, true)."
					return res;
				};
				
				var datetimeIsoTime = function(){
					var res = true;
					".$this->timeISO(null, true, true)."
					return res;
				};
				
				res = datetimeIsoDate() && datetimeIsoTime();
				
				if( !res && ".$this->exprToJsBool(defined('MSG_DATETIME_ISO'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_DATETIME_ISO."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function dateDE($X, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			$dateregex = '/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{3,4}$/';
			
			foreach( $this->values as $date ){
				if( $internal ){
					$datetimeParts = explode(' ', $date, 2);
				
					if( count($datetimeParts) >= 2 ){
						$date = trim($datetimeParts[0]);
					} else {
						$date = '';
					}
				}
				
				$dateArray = explode('.', $date);
				
				if(
					($date == '')
					|| !HtmlFormTools::auto_preg_match($dateregex, $date, $this->needsUtf8Safety)
					|| (strtotime($dateArray[2].'-'.$dateArray[1].'-'.$dateArray[0]) === false)
					|| !checkdate(intval($dateArray[1]), intval($dateArray[0]), intval($dateArray[2]))
				){
					$res = false;
				}
				
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATE_DE);
			}
			
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					".($internal 
						? "
							var datetimeParts = value.split(' ');
							if( datetimeParts.length >= 2 ){
								value = $.trim(datetimeParts[0]);
							} else {
								value = '';
							}
						"
						: ''
					)."
											
					var formatValid = /^(0?[1-9]|[1-2][0-9]|3[0-1])\.(0?[1-9]|1[0-2])\.([1-2][0-9]{3})$/.test(value);
					var splitValue = formatValid ? value.split('.') : null;
					
					if( formatValid ){
						for( var i = 0; i < 2; i++ ){
							if( splitValue[i].length  < 2 ){
								splitValue[i] = '0'+splitValue[i];
							}
						}
					}
					
					var date = formatValid ? new Date(splitValue[2]+'-'+splitValue[1]+'-'+splitValue[0]) : null;
					var ruleRes = 
						((date !== null) && (splitValue !== null))
						? (
							!/Invalid|NaN/.test(date)
							&& (parseInt(splitValue[0]) == (date.getDate()))
							&& (parseInt(splitValue[1]) == (date.getMonth() + 1))
							&& (parseInt(splitValue[2]) == (date.getFullYear()))
						)
						: false
					;
					
					res = res && ruleRes;
				});
			";
			
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
					".$algorithmCode."
					
					if( !res && ".$this->exprToJsBool(defined('MSG_DATE_DE'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_DATE_DE."';
							
							$.each([['%name%', labelTxt]], function(index, value){
								var rex = new RegExp(value[0], 'g');
								lastMsg = lastMsg.replace(rex, value[1]);
							});
						}
					}
				";
			}
		}
	}
	
	
	
	private function timeDE($X, $renderJavascriptCode = false, $internal = false){
		if( !$renderJavascriptCode ){
			$res = true;
	
			$timeregex = '/^([0-1][0-9]|2[0-3])\:[0-5][0-9](\:[0-5][0-9])?h?$/';
	
			foreach( $this->values as $time ){
				if( $internal ){
					$datetimeParts = explode(' ', $time, 2);
	
					if( count($datetimeParts) >= 2 ){
						$time = trim($datetimeParts[1]);
					} else {
						$time = '';
					}
				}
	
				if(
					($time == '')
					|| !HtmlFormTools::auto_preg_match($timeregex, $time, $this->needsUtf8Safety)
					|| (strtotime($time) === false)
				){
					$res = false;
				}
	
				if( !$res ) break;
			}
	
			if( !$res && ($this->fieldName != '') && !$internal ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_TIME_DE);
			}
	
			return $res;
		} else {
			$algorithmCode = "
				$.each(values, function(index, value){
					".($internal
						? "
							var datetimeParts = value.split(' ');
							if( datetimeParts.length >= 2 ){
								value = $.trim(datetimeParts[1]);
							} else {
								value = '';
							}
						"
						: ''
					)."
						
					var ruleRes = /^([0-1][0-9]|2[0-3])\:[0-5][0-9](\:[0-5][0-9])?h?$/.test(value);
					res = res && ruleRes;
				});
			";
	
			if( $internal ){
				return $algorithmCode;
			} else {
				return "
						".$algorithmCode."
						
						if( !res && ".$this->exprToJsBool(defined('MSG_TIME_DE'))." ){
							if( \$label.length > 0 ){
								lastMsg = '".MSG_TIME_DE."';
								
								$.each([['%name%', labelTxt]], function(index, value){
									var rex = new RegExp(value[0], 'g');
									lastMsg = lastMsg.replace(rex, value[1]);
								});
							}
						}
					";
			}
		}
	}
	
	
	
	private function datetimeDE($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = $this->dateDE(null, false, true) && $this->timeDE(null, false, true);
	
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DATETIME_DE);
			}
	
			return $res;
		} else {
			return "
					var datetimeDeDate = function(){
						var res = true;
						".$this->dateDE(null, true, true)."
						return res;
					};
					
					var datetimeDeTime = function(){
						var res = true;
						".$this->timeDE(null, true, true)."
						return res;
					};
					
					res = datetimeDeDate() && datetimeDeTime();
					
					if( !res && ".$this->exprToJsBool(defined('MSG_DATETIME_DE'))." ){
						if( \$label.length > 0 ){
							lastMsg = '".MSG_DATETIME_DE."';
							
							$.each([['%name%', labelTxt]], function(index, value){
								var rex = new RegExp(value[0], 'g');
								lastMsg = lastMsg.replace(rex, value[1]);
							});
						}
					}
				";
		}
	}
	
	
	
	private function number($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			foreach( $this->values as $number ){
				if( ($number != (string)(integer)$number) && ($number != (string)(float)$number) ) $res = false;
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_NUMBER);
			}
			
			return $res;
		} else {
			return "
				$.each(values, function(index, value){
					res = res && ((value == ''+parseInt(value)) || (value == ''+parseFloat(value)));
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_NUMBER'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_NUMBER."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function numberDE($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
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
		} else {
			return "
				$.each(values, function(index, value){
					var ruleRes = /^[0-9]+(\,[0-9]+)?$/.test(value);
					
					if( ruleRes ){
						value = value.replace(/\,/g, '.');
						ruleRes = ((value == ''+parseInt(value)) || (value == ''+parseFloat(value)));
					}
					
					res = res && ruleRes;
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_NUMBER_DE'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_NUMBER_DE."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function digits($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			foreach( $this->values as $val ){
				if( !HtmlFormTools::auto_preg_match('/^[0-9]+$/', $val, $this->needsUtf8Safety) ) $res = false;
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_DIGITS);
			}
			
			return $res;
		} else {
			return "
				$.each(values, function(index, value){
					res = res && /^[0-9]+$/.test(value);
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_DIGITS'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_DIGITS."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function creditcard($X, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			$creditCardRegEx = '/^[0-9]{3,4}\-[0-9]{4}\-[0-9]{4}\-[0-9]{4}$/';
			
			foreach( $this->values as $creditCardNumber ){
				if( !HtmlFormTools::auto_preg_match($creditCardRegEx, $creditCardNumber, $this->needsUtf8Safety) ) $res = false;
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace('%name%', $this->fieldName, MSG_CREDITCARD);
			}
			
			return $res;
		} else {
			return "
				$.each(values, function(index, value){
					res = res && /^[0-9]{3,4}\-[0-9]{4}\-[0-9]{4}\-[0-9]{4}$/.test(value);
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_CREDITCARD'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_CREDITCARD."';
						
						$.each([['%name%', labelTxt]], function(index, value){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
	}
	
	
	
	private function characterclass($class, $renderJavascriptCode = false){
		if( !$renderJavascriptCode ){
			$res = true;
			
			$characterClassRegEx = "^[$class]*$";
			
			foreach( $this->values as $value ){
				if( !HtmlFormTools::auto_preg_match('/'.$characterClassRegEx.'/', $value, $this->needsUtf8Safety) ) $res = false;
				if( !$res ) break;
			}
			
			if( !$res && ($this->fieldName != '') ){
				$this->messageQueue[] = str_replace(array('%name%', '%class%'), array($this->fieldName, $class), MSG_CHARACTERCLASS);
			}
			
			return $res;
		} else {
			return "
				var characterClassRegEx = /^[".$class."]*$/;
			
				$.each(values, function(index, value){
					res = res && characterClassRegEx.test(value);
				});
				
				if( !res && ".$this->exprToJsBool(defined('MSG_CHARACTERCLASS'))." ){
					if( \$label.length > 0 ){
						lastMsg = '".MSG_CHARACTERCLASS."';
						
						$.each(
							[
								['%name%', labelTxt],
								['%class%', '".$class."']
							],
							function(index, value
						){
							var rex = new RegExp(value[0], 'g');
							lastMsg = lastMsg.replace(rex, value[1]);
						});
					}
				}
			";
		}
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
				require_once 'messages/'.$this->messageLanguage.'.inc.php';
			}
			
			foreach( $this->rules as $function => $param ){
				$caseValidity = $this->$function($param);
				$this->isValid = $this->isValid && $caseValidity;
			}
		}
		
		return $this->isValid;
	}
	
	
	
	/**
	 * Prepares the validator for output of JS-valiation-code later on.
	 * 
	 * @param String|null $selector jQuery-selector to specifically select elements to drag values from, normally the selector will be constructed by html-name-attribute
	 * @param String|null $errorSelector jQuery-selector to specifically select elements to error-mark in case of a failed validation, normally identical to selector
	 * @param Boolean $prepareMessages defines if language messages shall be required, only has to be the case once, deactivating this is simply a question of performance
	 * 
	 * @return FormValidator method owner
	 */
	public function activateJavascriptValidation($selector = null, $errorSelector = null, $prepareMessages = true){
		$this->usesJavascriptValidation = true;
		
		if( !is_null($selector) && ($this->selector == '') ){
			$this->selector = "$selector";
		}
		
		if( !is_null($errorSelector) ){
			$this->errorSelector = "$errorSelector";
		}
		
		if( $prepareMessages ){
			require_once 'messages/'.$this->messageLanguage.'.inc.php';
		}

		return $this;
	}
	
	
	
	private function exprToJsBool($expression){
		return $expression ? 'true' : 'false';
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
				$msg .= '<div class="'.self::MESSAGECLASS.' msg_'.$this->id.'">'.$m.'</div>';
			}
		} elseif( $showOnlyCustomErrorMessage ){
			$msg .= '<div class="'.self::MESSAGECLASS.' msg_'.$this->id.'">'.$this->customErrorMessage.'</div>';
		}
		
		return $msg;
	}
	
	
	
	/**
	 * Compiles and returns the JS-validation-code for this validator, to be printed beneath the validated form elements
	 * in the source later on. This code contains a sequence of single tests, that will be executed on every update of the
	 * connected elements, which validate the value(s) one by one.
	 * 
	 * At the moment, there is no smart procedure to reuse code parts in several validations. Each fragment is specifically and
	 * autonomously constructed for each validator.
	 * 
	 * @return String the JS-code to insert into the form-rendering, that validates the values of this validator according to its rules on the fly
	 */
	public function printJavascriptValidationCode(){
		if( $this->usesJavascriptValidation ){
			$optionalValueJsArray = '[\'\'';
			if( isset($this->rules['optional']) ){
				foreach( $this->rules['optional'] as $optionalValue ){
					$optionalValueJsArray .= ", '$optionalValue'";
				}
			}
			$optionalValueJsArray .= ']';
			
			$javascriptValidationCode = "
				<script type=\"text/javascript\">
					HTMLFORM.jquery('".$this->selector."').on('change blur', function(){
						var \$ = HTMLFORM.jquery;
					
						var isValid = true;
						var res = true;				
						
						var lastMsg = '';
						var errorSelector = '".(($this->errorSelector != '') ? $this->errorSelector : $this->selector)."';
	
						var asyncError = false;
						
						var showOnlyAutoErrorMessages = ".$this->exprToJsBool(($this->customErrorMessage == '') || $this->forceErrorMessageOutput).";
						var showOnlyCustomErrorMessage = ".$this->exprToJsBool($this->customErrorMessage != '').";
						
						var values = HTMLFORM.data.getAsObj($(this).closest('form'))[$(this).attr('name').replace(/\[\]/, '')];
						if( !$.isArray(values) ){
							values = [values];
						}
						
						var isOptional = ".($this->exprToJsBool(isset($this->rules['optional']))).";
						var hasNonOptionalValue = false;
						if( isOptional ){
							var optionalValues = ".$optionalValueJsArray.";
							$.each(values, function(index, value){
								if( $.inArray(value, optionalValues) == -1 ){
									hasNonOptionalValue = true;
									return false;
								}
							});
						}
						
						HTMLFORM.validation.removeErrorMessages('".$this->id."');
						
						if( !isOptional || hasNonOptionalValue ){
							var \$label = $(this).closest('.".FormElement::WIDGETCLASS."').prev().children('label').first();
							var labelTxt = (\$label.length > 0) ? \$label.text() : '';
			";
			foreach( $this->rules as $function => $param ){
				$javascriptValidationCode .= "
							res = true;
				
							".$this->$function($param, true)."
							
							if( showOnlyAutoErrorMessages && (lastMsg != '') ){
								HTMLFORM.validation.handleErrorMessage('".$this->id."', lastMsg);
							}
							
							isValid = isValid && res;
							lastMsg = '';
				";
			}
			$javascriptValidationCode .= "
							if( !isValid && showOnlyCustomErrorMessage ){
								HTMLFORM.validation.handleErrorMessage('".$this->id."', '".$this->customErrorMessage."');
							}
						}
						
						if( asyncError == false ){
							HTMLFORM.validation.markError($(errorSelector), isValid);
						}
					});
				</script>
			";
			
			return $javascriptValidationCode;
		} else {
			return '';
		}
	}
}

?>