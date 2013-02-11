<?php

//---|includes----------

require_once 'HtmlForm.FormElement.InputText.class.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Wraps a password-text-input.
 * Used to obfuscate text-input while typing. For user-set passwords it's always a god idea to
 * make the user retype his new password in a second identical input. Remove possibility to
 * copy and paste with javascript for god tier.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package formelements
 * @subpackage value-widgets
 */
class InputPassword extends InputText{
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
	}
	
	
	
	/**
	 * Factory method for InputPassword, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputPassword new InputPassword-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputPassword($name, $id);
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
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
	
		return
			 '<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<input'
						.$this->printId()
						.$this->printName()
						.' type="password"'
						.' value="'.HtmlFormTools::auto_htmlspecialchars($this->text, $this->needsUtf8Safety()).'"'
						.$this->printTitle()
						.$this->printSize()
						.$this->printMaxLength()
						.$this->printCssClasses()
						.$this->printJavascriptEventHandler()
						.$this->printTabindex()
						.$this->printReadonly()
						.$this->printDisabled()
						.$this->masterForm->printSlash()
					.'>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
			.$this->printJavascriptValidationCode()
		;
	}
}

?>