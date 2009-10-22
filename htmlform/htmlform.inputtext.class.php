<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');



//---|class----------

class InputText extends FormElement{
	// ***
	protected $text;
	
	protected $size;
	protected $maxLength;
	
	protected $readonly;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->text = '';
		
		$this->size = 0;
		$this->maxLength = 0;
		
		$this->readonly = false;
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
	
	
	
	public function setSize($size){
		if( is_numeric($size) && ($size > 0) ){
			$this->size = $size;
		}
		return $this;
	}
	
	
	
	public function setMaxLength($maxLength){
		if( is_numeric($maxLength) && ($maxLength > 0) ){
			$this->maxLength = $maxLength;
		}
		return $this;
	}
	
	
	
	public function setReadonly(){
		$this->readonly = true;
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getValue(){
		return $this->text;
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
	
	protected function printSize(){
		return ($this->size > 0) ? ' size="'.$this->size.'"' : '';
	}
	
	
	
	protected function printMaxLength(){
		return ($this->maxLength > 0) ? ' maxlength="'.$this->maxLength.'"' : '';
	}
	
	
	
	protected function printReadonly(){
		return $this->readonly ? ' readonly="readonly"' : '';
	}
	
	
	
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		$wrapClasses = parent::WRAPCLASS.((!$this->isValid && !$this->masterForm->usesReducedErrorMarking()) ? ' '.parent::ERRORCLASS : '');
	
		return
			 '<div class="'.$wrapClasses.'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<input'
						.$this->printId()
						.$this->printName()
						.' type="text"'
						.' value="'.$this->text.'"'
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