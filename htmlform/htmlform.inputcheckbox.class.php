<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');

require_once('htmlform.tools.class.php');



//---|class----------

class InputCheckbox extends FormElement{
	// ***
	private $options;
	private $selected;
	private $selectedValues;
	private $selectedIndices;
	private $width;
	
	protected function __construct($name){
		parent::__construct($name, '');
		
		$this->options = array();
		$this->selected = array();
		$this->selectedValues = array();
		$this->selectedIndices = array();
		$this->width = 1;
	}
	
	
	
	public static function get($name){
		$res = new InputCheckbox($name);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setOptions(Array $options){
		$this->options = $options;
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
	
	
	
	public function setWidth($width){
		$this->width = (integer) $width;
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
		
		return $values;
	}
	
	
	
	//---|questions----------
	
	private function isSelectedOption($index, $value, $text){
		return(
			(in_array($index, $this->selectedIndices))
			||
			(in_array($value, $this->selectedValues))
			||
			(in_array($text, $this->selected))
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
	
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		
		$index = 0;
		$options = '';
		foreach( $this->options as $value => $text ){
			$index++;
			$checkId = $this->masterForm->getId().'_checkbox_'.$value;
			$options .=
				'<input'
					.' type="checkbox"'
					.' id="'.$checkId.'"'
					.$this->printNameArray()
					.$this->printCssClasses()
					.' value="'.HtmlFormTools::auto_htmlspecialchars($value, $this->needsUtf8Safety()).'"'
					.($this->isSelectedOption($index, $value, $text) ? ' checked="checked"' : '')
					.$this->printTabIndex()
					.$this->printDisabled()
					.$this->masterForm->printSlash()
				.'>'
				.'&nbsp;'.Label::getInline($text, $checkId)->doRender()
				.((($index % $this->width) == 0) ? '<br/>' : '&nbsp;&nbsp;&nbsp;')
			;
		}
	
		return
			 '<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'"'.$this->printJsEventHandler().'>'
					.$options
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>