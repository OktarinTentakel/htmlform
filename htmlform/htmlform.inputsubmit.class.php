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
	
	
	
	//---|getter----------
	
	public function getValue(){
		return isset($_REQUEST[''.$this->name]);
	}
	
	
	
	//---|output----------
	
	public function doRender(){
		return
			 '<input'
				 .$this->printId()
				 .$this->printName()
				 .' type="submit"'
				 .' value="'.$this->caption.'"'
				 .$this->printTitle()
				 .$this->printCssClasses()
				 .$this->printJsEventHandler()
				 .$this->printTabindex()
				 .$this->printDisabled()
				 .$this->masterForm->printSlash()
			.'>'
		;
	}
}

?>