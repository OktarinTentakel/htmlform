<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';



//---|class----------

/**
 * Wraps a form-submit-button.
 *
 * This element is not wrapped into a row, but should be inserted into a container-widget.
 *
 * @author Sebastian Schlapkohl
 * @version 1.0
 * @package formelements
 * @subpackage control-widgets
 */
class InputSubmit extends FormElement{
	// ***
	/**
	 * the button caption
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
	 * Factory method for InputSubmit, returns new instance.
	 * Factories are used to make instant chaining possible.
	 *
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputSubmit new InputSubmit-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputSubmit($name, $id);
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
	 * Returns if the submit-button was used for the last occurred
	 * form-submit.
	 *
	 * @return Boolean submit-button has used for last submit yes/no
	 */
	public function getValue(){
		$refiller = $this->determineRefiller();
		return isset($refiller[''.$this->name]);
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
				 .' type="submit"'
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