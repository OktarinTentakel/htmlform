<?php

//---|includes----------

require_once 'htmlform.inputtext.class.php';
require_once 'htmlform.label.class.php';

require_once 'htmlform.tools.class.php';



//---|class----------

/**
 * Wraps a file-upload text-input.
 * Be aware that you have to change the enctype of the form to "multipart/form-data", if you want to use this
 * element in combination with other value-elements. Otherwise there won't be entries in $_FILES to work with.
 * 
 * Refills won't work on this element, due to security restrictions of the browsers.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.85 beta
 * @package formelements
 * @subpackage value-widgets
 */
class InputFile extends InputText{
	// ***
	private $accept;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->accept = '';
	}
	
	
	
	/**
	 * Factory method for InputFile, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputFile new InputFile-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputFile($name, $id);
		return $res;
	}
	// ***
	
	
	
	//--|setter----------
	
	/**
	 * Sets the file-accept type of the element.
	 * Be aware that no current browser actively uses this setting. Nonetheless it's part of the standard
	 * and could be used for information purposes for javascript e.g.
	 * 
	 * @param String $accept html-accept-type for the element
	 * @return InputFile method owner
	 */
	public function setAccept($accept){
		if( HtmlFormTools::auto_preg_match('/^(application|audio|image|multipart|text|video)\/(\*|[a-zA-Z\-]+)$/i', $accept) ){
			$this->accept = $accept;
		}
		
		return $this;
	}
	
	
	
	//---|output----------
	
	private function printAccept(){
		return ($this->accept == '') ? '' : ' accept="'.$this->accept.'"';
	}
	
	
	
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
						.' type="file"'
						.$this->printTitle()
						.$this->printAccept()
						.$this->printSize()
						.$this->printMaxLength()
						.$this->printCssClasses()
						.$this->printJsEventHandler()
						.$this->printTabindex()
						.$this->printReadonly()
						.$this->printDisabled()
						.$this->masterForm->printSlash()
					.'>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>