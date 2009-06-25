<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');



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
	
	
	
	public function setRefill(Array $refiller = array()){
		if( count($refiller) == 0 )	$refiller = $_POST;
		
		if( isset($refiller[$this->name]) && is_array($refiller[$this->name]) ){
			$this->selectedValues = $refiller[$this->name];
			$this->selected = array();
			$this->selectedIndices = array();
		}
		
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
				 '<input type="checkbox" id="'.$checkId.'"'.$this->printNameArray().$this->printCssClasses().' value="'.$value.'"'.($this->isSelectedOption($index, $value, $text) ? ' checked="checked"' : '').$this->printTabIndex().'/>'
					.'&nbsp;'.Label::getInline($text, $checkId)->doRender()
				.((($index % $this->width) == 0) ? '<br/>' : '&nbsp;&nbsp;&nbsp;')
			;
		}
	
		return
			 '<div class="'.parent::WRAPCLASS.'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.$options
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>