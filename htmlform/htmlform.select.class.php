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
	private $optionTitles;
	private $optGroups;
	private $selected;
	private $selectedValues;
	private $selectedIndices;
	private $size;
	private $multiple;
	
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->options = array();
		$this->optionCssClasses = array();
		$this->optionTitles = array();
		$this->optGroups = array();
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
		foreach( $options as $name => $value ){
			if( is_array($value) ){
				$this->optGroups["$name"] = array();
				foreach( $value as $subName => $subValue ){
					$this->options["$subName"] = "$subValue";
					$this->optGroups["$name"][] = count($this->options);
				}
			} else {
				$this->options["$name"] = "$value";
			}
		}
		
		return $this;
	}
	
	
	
	public function setOptionCssClasses(Array $classes){
		$this->optionCssClasses = $classes;
		return $this;
	}
	
	
	
	public function setOptionTitles(Array $titles){
		$this->optionTitles = $titles;
		return $this;
	}
	
	
	
	public function setSelected(Array $selected){
		if( !$this->multiple ){
			$this->resetSelection();
			$this->selected = !empty($selected) ? array(''.$selected[0]) : array();
		} else {
			$this->selected = $selected;
		}
		
		return $this;
	}
	
	
	
	public function setSelectedSingle($selected){
		if( !$this->multiple ) $this->resetSelection();
		$this->selected = array("$selected");
		return $this;
	}
	
	
	
	public function setSelectedValues(Array $selected){
		if( !$this->multiple ){
			$this->resetSelection();
			$this->selectedValues = !empty($selected) ? array(''.$selected[0]) : array();
		} else {
			$this->selectedValues = $selected;
		}
		
		return $this;
	}
	
	
	
	public function setSelectedValue($selected){
		if( !$this->multiple ) $this->resetSelection();
		$this->selectedValues = array("$selected");
		return $this;
	}
	
	
	
	public function setSelectedIndices(Array $selected){
		if( !$this->multiple ){
			$this->resetSelection();
			$this->selectedIndices = !empty($selected) ? array($selected[0]) : array();
		} else {
			$this->selectedIndices = $selected;
		}
		
		return $this;
	}
	
	
	
	public function setSelectedIndex($selected){
		if( !$this->multiple ) $this->resetSelection();
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
		$defaultValue = null;
		$index = 0;
		
		foreach( $this->options as $value => $text ){
			$index++;
			
			if( $index == 1 ){
				$defaultValue = $value;
			}
			
			if( $this->isSelectedOption($index, $value, $text) ){
				$values[] = $value;
			}
		}
		
		if( (count($values) == 0) && !$this->multiple ){
			$values[] = $defaultValue;
		}
		
		if( $this->multiple ){
			return $values;
		} else {
			return $values[0];
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
	
	public function refill(Array $refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
		
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) ){
				$values = HtmlFormTools::undoMagicQuotes($refiller[$this->name]);
				
				$this->selectedValues = is_array($refiller[$this->name]) ? $values : array($values);
				$this->selected = array();
				$this->selectedIndices = array();
			} elseif( ($this->masterForm != null) && $this->masterForm->hasBeenSent() ) {
				$this->selectedValues = array();
				$this->selected = array();
				$this->selectedIndices = array();
			}
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
	
	
	
	private function resetSelection(){
		$this->selectedIndices = array();
		$this->selectedValues = array();
		$this->selected = array();
	}
	
	
	
	//---|output----------
	
	private function printMultiple(){
		return ($this->multiple ? ' multiple="multiple"' : '');
	}
	
	
	
	private function printOption($index, $value, $text){
		return
			'<option'
				.' value="'.HtmlFormTools::auto_htmlspecialchars($value, $this->needsUtf8Safety()).'"'
				.((count($this->optionCssClasses) > 0) ? ' class="'.$this->optionCssClasses[(($index - 1) % count($this->optionCssClasses))].'"'  : '')
				.(((count($this->optionTitles) > 0) && !empty($this->optionTitles[(($index - 1) % count($this->optionTitles))])) ? ' title="'.$this->optionTitles[(($index - 1) % count($this->optionTitles))].'"'  : '')
				.($this->isSelectedOption($index, $value, $text) ? ' selected="selected"' : '')
			.'>'
				.HtmlFormTools::auto_htmlspecialchars($text, $this->needsUtf8Safety())
			.'</option>'
		;
	}
	
	
	
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		
		$index = 0;
		$optGroups = '';
		$options = '';
		foreach( $this->options as $value => $text ){
			$index++;
			$isInOptGroup = false;
			
			foreach( $this->optGroups as $optGroupLabel => $optGroupIndices ){
				$pos = array_search($index, $optGroupIndices);
				if( $pos !== false ){
					$isInOptGroup = true;
				
					if( count($optGroupIndices) > 1 ){
						if( $pos == 0 ){
							$optGroups .=
								'<optgroup label="'.HtmlFormTools::auto_htmlspecialchars($optGroupLabel, $this->needsUtf8Safety()).'">'
								.$this->printOption($index, $value, $text)
							;
						} elseif( $pos == (count($optGroupIndices)-1) ){
							$optGroups .=
								$this->printOption($index, $value, $text)
								.'</optgroup>'
							;
						} else {
							$optGroups .= $this->printOption($index, $value, $text);
						}
					} else {
						$optGroups .=
							'<optgroup label="'.HtmlFormTools::auto_htmlspecialchars($optGroupLabel, $this->needsUtf8Safety()).'">'
							.$this->printOption($index, $value, $text)
							.'</optgroup>'
						;
					}
				}
			}
			
			if( !$isInOptGroup ){
				$options .=	$this->printOption($index, $value, $text);
			}
		}
	
		return
			 '<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<select'
						.$this->printId()
						.($this->multiple ? $this->printNameArray() : $this->printName())
						.$this->printTitle()
						.' size="'.$this->size.'"'
						.$this->printMultiple()
						.$this->printCssClasses()
						.$this->printJsEventHandler()
						.$this->printTabindex()
						.$this->printDisabled()
					.'>'
						.$optGroups
						.$options
					.'</select>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>