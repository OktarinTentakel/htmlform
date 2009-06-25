<?php

abstract class FormElement{
	// ***
	const WRAPCLASS = 'htmlform_row_div';
	const WIDGETCLASS = 'htmlform_widget_div';
	
	protected $masterForm;
	protected $masterElement;
	
	protected $validator;
	protected $isValid;
	
	protected $id;
	protected $name;
	protected $cssClasses;
	protected $label;
	protected $subElements;
	
	protected function __construct($name, $id = ''){
		$this->masterForm = null;
		$this->masterElement = null;
		
		$this->validator = null;
		$this->isValid = true;
		
		$this->id = "$id";
		$this->name = "$name";
		$this->cssClasses = '';
		$this->label = '';
		$this->subElements = null;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setMasterForm(HtmlForm $master){
		$this->masterForm = $master;
		foreach( $this->getSubElements() as $se ){
			$se->setMasterForm($master);
		}
		return $this;
	}
	
	
	
	public function setMasterElement(FormElement $master){
		$this->masterElement = $master;
		return $this;
	}
	
	
	
	public function setValidator(FormValidator $validator){
		if( $this->label != '' ) $validator->setFieldName($this->label);
		$this->validator = $validator;
		return $this;
	}
	
	
	
	public function setId($id){
		$this->id = "$id";
		return $this;
	}
	
	
	
	public function setCssClasses($cssClasses){
		$this->cssClasses = "$cssClasses";
		return $this;
	}
	
	
	
	public function setLabel($label){
		$this->label = "$label";
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getMasterForm(){
		return $this->masterForm;
	}
	
	
	
	public function getMasterElement(){
		return $this->masterElement;
	}
	
	
	
	public function getId(){
		return $this->id;
	}
	
	
	
	public function getName(){
		return $this->name;
	}
	
	
	
	public function getLabel(){
		return $this->label;
	}
	
	
	
	public function getSubElements(){
		return is_array($this->subElements) ? $this->subElements : array();
	}
	
	
	
	//---|functionality----------
	
	// >>>
	public function validate(){
		if( !is_null($this->validator) ) $this->validator->setMessageLanguage($this->masterForm->getLanguage());
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$this->isValid = $this->isValid and $element->validate();
			}
		}
		
		return $this->isValid;
	}
	// >>>
	
	
	
	public function addElement(FormElement $element){
		if( is_array($this->subElements) ){
			$element->setMasterElement($this);
			$this->subElements[] = $element;
		}
		return $this;
	}
	
	
	
	//---|output----------
	
	protected function printId(){
		return (($this->id != '') ? ' id="'.$this->id.'"' : '');
	}
	
	
	
	protected function printName(){
		return (($this->name != '') ? ' name="'.$this->name.'"' : '');
	}
	
	
	
	protected function printNameArray(){
		return (($this->name != '') ? ' name="'.$this->name.'[]"' : '');
	}
	
	
	
	protected function printCssClasses(){
		return (($this->cssClasses != '') ? ' class="'.$this->cssClasses.'"' : '');
	}
	
	
	
	protected function printTabIndex(){
		$res = ' tabindex="'.$this->masterForm->getTabIndex().'"';
		$this->masterForm->incTabIndex();
		return $res;
	}
	
	
	
	public function printMessages(){
		$msg = '';
		
		if( !is_null($this->validator) ){
			$msg .= $this->validator->printMessageQueue();
		}
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$msg .= $element->printMessages();
			}
		}
		
		return $msg;
	}
	
	
	
	// ###
	abstract public function doRender();
	// ###
}

?>