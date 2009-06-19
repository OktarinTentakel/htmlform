<?php

class FormValidator{
	// ***
	private $rules;
	private $values;
	private $isValid;
	
	private function __construct(){
		$this->rules = array();
		$this->values = array();
		$this->isValid = true;
	}
	
	
	
	public function get(){
		$res = new FormValidator();
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setValue($value){
		$this->values = array("$value");
		return $this;
	}
	
	
	
	public function setValues(Array $values){
		$this->values = $values;
		return $this;
	}
	
	
	
	public function setRequired(){
		$this->rules['required'] = true;
		return $this;
	}
	
	
	
	public function setMinLength($minlength){
		$this->rules['minlength'] =  (integer) $minlength;
		return $this;
	}
	
	
	
	public function setMaxLength($maxlength){
		$this->rules['maxlength'] =  (integer) $maxlength;
		return $this;
	}
	
	
	
	//---|rules----------
	
	private function required($required){
		$res = false;
		
		foreach( $this->values as $val ){
			$res = ($val != '');
			if( $res ) break;
		}
		
		return $res;
	}
	
	
	
	private function minlength($minlength){
		if( count($this->values) == 1 ){
			return strlen($this->values[0]) >= $minlength;
		} else {
			return count($this->values) >= $minlength;
		}
	}
	
	
	
	private function maxlength($maxlength){
		if( count($this->values) == 1 ){
			return strlen($this->values[0]) <= $maxlength;
		} else {
			return count($this->values) <= $maxlength;
		}
	}
	
	
	
	//---|functionality----------
	
	public function process(){
		if( count($this->values) > 0 ){
			foreach( $this->rules as $function => $param ){
				$this->isValid = $this->isValid && $this->$function($param);
			}
		}
		
		return $this->isValid;
	}
}

?>