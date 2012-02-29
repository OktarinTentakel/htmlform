<?php

//---|includes----------

require_once 'htmlform.inputbutton.class.php';



//---|class----------

/**
 * Wraps a form-reset-button.
 * Clicking this button resets the form to the state it was in at the last pageload. This widget may be of little
 * help when developing with HtmlForm, since each form-cycle is a reload, refreshing the reset-state. But there are ways
 * to make this work, so it's included for means of completion.
 * 
 * This element is not wrapped into a row, but should be inserted into a container-widget.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.95 beta
 * @package formelements
 * @subpackage control-widgets
 */
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
				.$this->printJavascriptEventHandler()
				.$this->printTabindex()
				.$this->printDisabled()
				.$this->masterForm->printSlash()
			.'>'
		;
	}
	
}


?>