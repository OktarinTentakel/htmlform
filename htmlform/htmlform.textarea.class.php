<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');



//---|class----------

class TextArea extends FormElement{
	// ***
	protected $text;
	
	protected $cols;
	protected $rows;
	
	protected $readonly;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->text = '';
		
		$this->cols = 0;
		$this->rows = 0;
		
		$this->readonly = false;
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new TextArea($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setText($text){
		$this->text = "$text";
		return $this;
	}
	
	
	
	public function setCols($cols){
		if( is_numeric($cols) && ($cols > 0) ){
			$this->cols = $cols;
		}
		return $this;
	}
	
	
	
	public function setRows($rows){
		if( is_numeric($rows) && ($rows > 0) ){
			$this->rows = $rows;
		}
		return $this;
	}
	
	
	
	public function setSize($cols, $rows){
		$this->setCols($cols);
		$this->setRows($rows);
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
	
	protected function printCols(){
		return ($this->cols > 0) ? ' cols="'.$this->cols.'"' : '';
	}
	
	
	
	protected function printRows(){
		return ($this->rows > 0) ? ' rows="'.$this->rows.'"' : '';
	}
	
	
	
	protected function printReadonly(){
		return $this->readonly ? ' readonly="readonly"' : '';
	}
	
	
	
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
	
		return
			 '<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<textarea'
						.$this->printId()
						.$this->printName()
						.$this->printCols()
						.$this->printRows()
						.$this->printCssClasses()
						.$this->printJsEventHandler()
						.$this->printTabindex()
						.$this->printReadonly()
						.$this->printDisabled()
					.'>'
						.$this->text
					.'</textarea>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>