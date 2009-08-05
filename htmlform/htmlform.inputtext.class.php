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
	
	public function refill(Array $refiller = array()){
		if( count($refiller) == 0 )	$refiller = $_POST;
		
		if( isset($refiller[$this->name]) && !is_array($refiller[$this->name]) ){
			$this->text = ''.$refiller[$this->name];
		}
		
		return $this;
	}
	
	
	
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
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<input'.$this->printId().$this->printName().' type="text" value="'.$this->text.'"'.$this->printCssClasses().$this->printTabindex().$this->masterForm->printSlash().'>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>