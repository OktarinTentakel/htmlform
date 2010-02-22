<?php

//---|includes----------

require_once('htmlform.inputtext.class.php');
require_once('htmlform.label.class.php');

require_once('htmlform.tools.class.php');



//---|class----------

class InputFile extends InputText{
	// ***
	private $accept;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->accept = '';
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new InputFile($name, $id);
		return $res;
	}
	// ***
	
	
	
	//--|setter----------
	
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