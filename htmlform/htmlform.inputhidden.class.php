<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//---|class----------

class InputHidden extends FormElement{
	// ***
	private $value;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->value = '';
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new InputHidden($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setValue($val){
		$this->value = "$val";
		return $this;
	}
	
	
	
	//---|functionality----------
	
	public function refill(Array $refiller = array()){
		if( count($refiller) == 0 )	$refiller = $_POST;
		
		if( isset($refiller[$this->name]) && !is_array($refiller[$this->name]) ){
			$this->value = ''.$refiller[$this->val];
		}
		
		return $this;
	}
	
	
	
	public function validate(){
		parent::validate();
		
		if( !is_null($this->validator) ){
			$this->validator->setValue($this->value);
			$this->isValid = $this->validator->process();
		}
		
		return $this->isValid;
	}
	
	
	
	//---|output----------
	
	public function doRender(){
		return
			'<input'.$this->printId().$this->printName().' type="hidden" value="'.$this->value.'"'.$this->masterForm->printSlash().'>'
		;
	}
}

?>