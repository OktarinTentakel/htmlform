<?php

//--|includes---------

require_once('htmlform.tools.class.php');



//--|class----------

abstract class FormElement{
	// ***
	const WRAPCLASS = 'htmlform_row_div';
	const WIDGETCLASS = 'htmlform_widget_div';
	const ERRORCLASS = 'htmlform_error';
	
	protected $masterForm;
	protected $masterElement;
	
	protected $validator;
	protected $isValid;
	
	protected $id;
	protected $name;
	protected $cssClasses;
	protected $label;
	protected $jsEventHandler;
	protected $subElements;
	protected $disabled;
	
	protected function __construct($name, $id = ''){
		$this->masterForm = null;
		$this->masterElement = null;
		
		$this->validator = null;
		$this->isValid = true;
		
		$this->id = "$id";
		$this->name = "$name";
		$this->cssClasses = '';
		$this->label = '';
		$this->jsEventHandler = '';
		$this->subElements = null;
		$this->disabled = false;
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
		$this->isValid = false;
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
	
	
	
	public function setJsEventHandler($handler, $reaction){
		$this->jsEventHandler = $handler.'="'.$reaction.'"';
		return $this;
	}
	
	
	
	public function setDisabled(){
		$this->disabled = true;
		return $this;
	}
	
	
	
	public function setUsable($expression){
		$this->disabled = !$expression;
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getMasterForm(){
		return $this->masterForm;
	}
	
	
	
	public function getMasterElement(){
		return $this->masterElement;
	}
	
	
	
	public function getValidator(){
		return $this->validator;
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
	
	
	
	public function getValueSet(FormValueSet $addedSet = null){
		$valueSet = is_null($addedSet) ? new FormValueSet() : $addedSet;
		
		if( !is_null($this->getValue()) ){
			$valueSet->setValue($this->name, $this->getValue());
		}
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$valueSet = $element->getValueSet($valueSet);
			}
		}
		
		return $valueSet;
	}
	
	
	
	// ###
	abstract public function getValue();
	// ###
	
	
	
	//---|questions----------
	
	public function isValid(){
		return $this->isValid;
	}
	
	
	
	//---|functionality----------
	
	// >>>
	public function validate(){
		if( !is_null($this->validator) ) $this->validator->setMessageLanguage($this->masterForm->getLanguage());
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$elEval = $element->validate();
				$this->isValid = $this->isValid && $elEval;
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
	
	
	
	public function insertElementAfter($targetElementName, FormElement $element){
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $index => $oldElement ){
				if( $oldElement->getName() == "$targetElementName" ){
					$element->setMasterElement($this);
					$this->subElements = HtmlFormTools::array_insert($this->subElements, ($index + 1), $element);
					break;
				}
			}
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
		if( 
			!$this->isValid 
			&& $this->masterForm->usesReducedErrorMarking() 
			&& $this->masterForm->hasBeenSent()
		){
			$this->cssClasses .= ($this->cssClasses == '') ? self::ERRORCLASS : ' '.self::ERRORCLASS;
		}
		
		return (($this->cssClasses != '') ? ' class="'.$this->cssClasses.'"' : '');
	}
	
	
	
	protected function printWrapperClasses(){
		return
			self::WRAPCLASS
			.(
				(!$this->isValid 
				 && !$this->masterForm->usesReducedErrorMarking()
				 && $this->masterForm->hasBeenSent())
					? ' '.self::ERRORCLASS 
					: ''
			)
		;
	}
	
	
	
	protected function printJsEventHandler(){
		return (($this->jsEventHandler != '') ? ' '.$this->jsEventHandler : '');
	}
	
	
	
	protected function printTabIndex(){
		$res = ' tabindex="'.$this->masterForm->getTabIndex().'"';
		$this->masterForm->incTabIndex();
		return $res;
	}
	
	
	
	protected function printDisabled(){
		return $this->disabled ? ' disabled="disabled"' : '';
	}
	
	
	
	public function printMessages($onlyCustomMessages = false){
		$msg = '';
		
		if( !is_null($this->validator) ){
			$msg .= $this->validator->printMessageQueue($onlyCustomMessages);
		}
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$msg .= $element->printMessages($onlyCustomMessages);
			}
		}
		
		return $msg;
	}
	
	
	
	// ###
	abstract public function doRender();
	// ###
}

?>