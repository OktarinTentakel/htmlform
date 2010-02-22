<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');

require_once('htmlform.tools.class.php');



//---|class----------

class Select extends FormElement{
	// ***
	private $options;
	private $optionCssClasses;
	private $selected;
	private $selectedValues;
	private $selectedIndices;
	private $size;
	private $multiple;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->options = array();
		$this->optionCssClasses = array();
		$this->selected = array();
		$this->selectedValues = array();
		$this->selectedIndices = array();
		$this->size = 1;
		$this->multiple = false;
	}
	
	
	
	public static function get($name, $id = ''){
		$res = new Select($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setOptions(Array $options){
		$this->options = $options;
		return $this;
	}
	
	
	
	public function setOptionCssClasses(Array $classes){
		$this->optionCssClasses = $classes;
		return $this;
	}
	
	
	
	public function setSelected(Array $selected){
		$this->selected = $selected;
		return $this;
	}
	
	
	
	public function setSelectedSingle($selected){
		$this->selected = array("$selected");
		return $this;
	}
	
	
	
	public function setSelectedValues(Array $selected){
		$this->selectedValues = $selected;
		return $this;
	}
	
	
	
	public function setSelectedValue($selected){
		$this->selectedValues = array($selected);
		return $this;
	}
	
	
	
	public function setSelectedIndices(Array $selected){
		$this->selectedIndices = $selected;
		return $this;
	}
	
	
	
	public function setSelectedIndex($selected){
		$this->selectedIndices = array($selected);
		return $this;
	}
	
	
	
	public function setSize($size){
		$this->size = (integer) $size;
		return $this;
	}
	
	
	
	public function setSingle(){
		$this->multiple = false;
		return $this;
	}
	
	
	
	public function setMultiple(){
		$this->multiple = true;
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getValue(){
		$values = array();
		$index = 0;
		foreach( $this->options as $value => $text ){
			$index++;
			if( $this->isSelectedOption($index, $value, $text) ){
				$values[] = $value;
			}
		}
		
		if( $this->multiple ){
			return $values;
		} else {
			return isset($values[0]) ? $values[0] : '';
		}
	}
	
	
	
	//---|questions----------
	
	private function isSelectedOption($index, $value, $text){
		return(
			(in_array($index, $this->selectedIndices))
			||
			(in_array("$value", $this->selectedValues))
			||
			(in_array("$text", $this->selected))
		);
	}
	
	
	
	//---|functionality----------
	
	public function refill(Array $refiller = array()){
		if( count($refiller) == 0 )	$refiller = $_POST;
		
		if( isset($refiller[$this->name]) && is_array($refiller[$this->name]) ){
			$this->selectedValues = HtmlFormTools::undoMagicQuotes($refiller[$this->name]);
			$this->selected = array();
			$this->selectedIndices = array();
		} elseif( ($this->masterForm != null) && $this->masterForm->hasBeenSent() ) {
			$this->selectedValues = array();
			$this->selected = array();
			$this->selectedIndices = array();
		}
		
		return $this;
	}
	
	
	
	public function validate(){
		parent::validate();
		
		if( !is_null($this->validator) ){
			$vals = array();
			
			foreach( $this->selectedIndices as $index ){
				$vals[] = $this->options[$index-1];
			}
			
			$vals = array_merge($vals, $this->selectedValues);
			
			foreach( $this->selected as $text ){
				if( $tmp = array_search($text, $this->options) ){
					$vals[] =  $tmp;
				}
			}
			
			$this->validator->setValues($vals);
			$this->isValid = $this->validator->process();
		}
		
		return $this->isValid;
	}
	
	
	
	//---|output----------
	
	private function printMultiple(){
		return ($this->multiple ? ' multiple="multiple"' : '');
	}
	
	
	
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		
		$index = 0;
		$options = '';
		foreach( $this->options as $value => $text ){
			$index++;
			
			$options .=
				 '<option'
					.' value="'.HtmlFormTools::auto_htmlspecialchars($value, $this->needsUtf8Safety()).'"'
					.((count($this->optionCssClasses) > 0) ? ' class="'.$this->optionCssClasses[(($index - 1) % count($this->optionCssClasses))].'"'  : '')
					.($this->isSelectedOption($index, $value, $text) ? ' selected="selected"' : '')
				.'>'
					.HtmlFormTools::auto_htmlspecialchars($text, $this->needsUtf8Safety())
				.'</option>'
			;
		}
	
		return
			 '<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<select'
						.$this->printId()
						.$this->printNameArray()
						.$this->printTitle()
						.' size="'.$this->size.'"'
						.$this->printMultiple()
						.$this->printCssClasses()
						.$this->printJsEventHandler()
						.$this->printTabindex()
						.$this->printDisabled()
					.'>'
						.$options
					.'</select>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>