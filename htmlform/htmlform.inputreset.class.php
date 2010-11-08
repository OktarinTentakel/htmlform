<?php

//--|includes----------

require_once 'htmlform.inputbutton.class.php';



//--|class----------

class InputReset extends InputButton {
	
	// ***
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->caption = '';
	}
	
	
	
	/**
	 * Factory method for InputReset, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputReset new InputReset-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputReset($name, $id);
		return $res;
	}
	// ***
	
	
	
//---|output----------
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		return
			'<input'
				.$this->printId()
				.$this->printName()
				.' type="reset"'
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