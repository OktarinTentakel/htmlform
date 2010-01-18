<?php

//---|class----------

class FormValueSet {
	// ***
	private $values;
	
	public function __construct(){
		$this->values = array();
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setValue($name, $value){
		$this->values["$name"] = $value;
	}
	
	
	
	//---|magic----------
	
	public function __call($name, Array $args = array()){
		return isset($this->values["$name"]) ? $this->values["$name"] : null;
	}
	
	
	
	public function __get($name){
		return isset($this->values["$name"]) ? $this->values["$name"] : null;
	}
}

?>