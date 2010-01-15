<?php

//---|includes----------

require_once('htmlform.inputtext.class.php');
require_once('htmlform.label.class.php');

require_once('htmlform.tools.class.php');



//---|class----------

class InputPassword extends InputText{
	// ***
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new InputPassword($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|output----------
	
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