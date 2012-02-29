<?php

//---|includes----------

require_once 'htmlform.formelement.absclass.php';



//---|class----------

/**
 * Wraps a common form-button.
 * By this an input[type=button] is meant and not a normal button, which isn't associated to forms in any way
 * (at least this one's an input :P). This class is as simple as it gets, a name and standard-attributes. That's it.
 * 
 * This element is not wrapped into a row, but should be inserted into a container-widget.
 * 
 * Be sure to set a javascript-handler, otherwise the buttons won't really do anything.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.95 beta
 * @package formelements
 * @subpackage control-widgets
 */
class InputButton extends FormElement{
	// ***
	/**
	 * the button-caption
	 * @var String
	 */
	protected $caption;
	
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
	 * Factory method for InputButton, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputButton new InputButton-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputButton($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the button-caption.
	 * 
	 * @param String $caption the button-caption to display
	 * @return InputButton method owner
	 */
	public function setCaption($caption){
		$this->caption = "$caption";
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Since InputButton doesn't hold any value, this method will always return null.
	 * 
	 * @return null
	 */
	public function getValue(){
		return null;
	}
	
	
	
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
				.' type="button"'
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