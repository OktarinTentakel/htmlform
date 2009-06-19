<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');



//---|class----------

class InputRadio extends FormElement{
	// ***
	private $options;
	private $selected;
	private $selectedValue;
	private $selectedIndex;
	private $width;
	
	protected function __construct($name){
		parent::__construct($name, '');
		
		$this->options = array();
		$this->selected = null;
		$this->selectedValue = null;
		$this->selectedIndex = null;
		$this->width = 1;
	}
	
	
	
	public static function get($name){
		$res = new InputRadio($name);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setOptions(Array $options){
		$this->options = $options;
		return $this;
	}
	
	
	
	public function setSelected($selected){
		$this->selected = "$selected";
		return $this;
	}
	
	
	
	public function setSelectedValue($selected){
		$this->selectedValue = "$selected";
		return $this;
	}
	
	
	
	public function setSelectedIndex($selected){
		$this->selectedIndices = "$selected";
		return $this;
	}
	
	
	
	public function setWidth($width){
		$this->width = (integer) $width;
		return $this;
	}
	
	
	
	//---|questions----------
	
	private function isSelectedOption($index, $value, $text){
		return(
			($index == $this->selectedIndex)
			||
			($value == $this->selectedValue)
			||
			($text == $this->selected)
		);
	}
	
	
	
	//---|functionality----------
	
	public function validate(){
		parent::validate();
		
		if( !is_null($this->validator) ){
			$val = '';
			
			if( !is_null($this->selectedIndex) ){
				$val = $this->options[$this->selectedIndex-1];
			} elseif( !is_null($this->selectedValue) ){
				$val = $this->selectedValue;
			} elseif( !is_null($this->selected) ){
				if( $tmp = array_search($this->selected, $this->options) ){
					$val =  $tmp;
				}
			}
			
			$this->validator->setValue($val);
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
			$radioId = $this->masterForm->getId().'_radio_'.$value;
			$options .=
				 '<input type="radio" id="'.$radioId.'"'.$this->printName().$this->printCssClasses().' value="'.$value.'"'.($this->isSelectedOption($index, $value, $text) ? ' checked="checked"' : '').$this->printTabIndex().$this->masterForm->printSlash().'>'
					.'&nbsp;'.Label::getInline($text, $radioId)->doRender()
				.((($index % $this->width) == 0) ? '<br'.$this->masterForm->printSlash().'>' : '&nbsp;&nbsp;&nbsp;')
			;
		}
	
		return
			 '<div class="'.parent::WRAPCLASS.'">'
				.$label
				.'<div class="htmlform_widget_div">'
					.$options
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>