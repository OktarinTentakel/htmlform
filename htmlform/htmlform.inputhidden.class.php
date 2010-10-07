<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');

require_once('htmlform.tools.class.php');



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
	
	
	
	//---|getter----------
	
	public function getValue(){
		return $this->value;
	}
	
	
	
	//---|functionality----------
	
	public function refill(Array $refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
	
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) && !is_array($refiller[$this->name]) ){
				$this->value = ''.HtmlFormTools::undoMagicQuotes($refiller[$this->val]);
			}
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
			'<input'
				.$this->printId()
				.$this->printName()
				.' type="hidden"'
				.' value="'.HtmlFormTools::auto_htmlspecialchars($this->value, $this->needsUtf8Safety()).'"'
				.$this->masterForm->printSlash()
			.'>'
		;
	}
}

?>