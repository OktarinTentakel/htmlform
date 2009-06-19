<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//---|class----------

class InputSubmit extends FormElement{
	// ***
	private $caption;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->caption = '';
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new InputSubmit($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setCaption($caption){
		$this->caption = "$caption";
		return $this;
	}
	
	
	
	//---|output----------
	
	public function doRender(){
		return
			'<input'.$this->printId().$this->printName().' type="submit" value="'.$this->caption.'"'.$this->printCssClasses().$this->printTabindex().$this->masterForm->printSlash().'>'
		;
	}
}

?>