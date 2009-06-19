<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');



//---|class----------

class InputText extends FormElement{
	// ***
	private $text;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->text = '';
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new InputText($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setText($text){
		$this->text = "$text";
		return $this;
	}
	
	
	
	//---|functionality----------
	
	public function validate(){
		parent::validate();
		
		if( !is_null($this->validator) ){
			$this->validator->setValue($this->text);
			$this->isValid = $this->validator->process();
		}
		
		return $this->isValid;
	}
	
	
	
	//---|output----------
	
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
	
		return
			 '<div class="'.parent::WRAPCLASS.'">'
				.$label
				.'<div class="htmlform_widget_div">'
					.'<input'.$this->printId().$this->printName().' type="text" value="'.$this->text.'"'.$this->printCssClasses().$this->printTabindex().$this->masterForm->printSlash().'>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>