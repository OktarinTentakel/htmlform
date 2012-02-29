<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Wraps a group of checkboxes.
 * HtmlForm doesn't deal with single checkboxes but always with groups of them. Checkboxes are meant to be a form
 * of multi-select-widget. Of course you can use this for creating single checkboxes, but be aware that the whole
 * thing will still handle as if n boxes where present.
 * 
 * If you want you implement a binary decision (yes/no), which checkboxes are often used for, I'd rather suggest the use
 * of accordingly labeled radio-buttons, to make things easier and to use widgets in the way the were intended to.
 * 
 * Setting a title or css-classes for this element will not result in anything. Instead, set titles and classes for the options
 * themselves, by using the appropriate methods.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.95 beta
 * @package formelements
 * @subpackage value-widgets
 */
class InputCheckbox extends FormElement{
	
	// ***
	private $options;
	private $optionCssClasses;
	private $optionTitles;
	private $selected;
	private $selectedValues;
	private $selectedIndices;
	private $width;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 */
	protected function __construct($name){
		parent::__construct($name, '');
		
		$this->options = array();
		$this->optionCssClasses = array();
		$this->optionTitles = array();
		$this->selected = array();
		$this->selectedValues = array();
		$this->selectedIndices = array();
		$this->width = 1;
	}
	
	
	
	/**
	 * Factory method for InputCheckbox, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @return InputCheckbox new InputCheckbox-instance
	 */
	public static function get($name){
		$res = new InputCheckbox($name);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets all available options of the checkbox-group.
	 * The options have to be given in the form of an associative array, where keys are the box-values and
	 * values are the label-texts for each box.
	 * array('val1' => 'nice checkbox', 'val2' => 'not so nice checkbox', ...)
	 * 
	 * @param Array[String] $options the options for the checkbox-group
	 * @return InputCheckbox method owner
	 */
	public function setOptions(Array $options){
		$this->options = $options;
		return $this;
	}
	
	
	
	/**
	 * Sets css-classes to set for the options.
	 * This could be used for an even-odd-pattern for example. One specialty of this
	 * functionality: the classes cycle. If you have 4 options for example and you define
	 * two classes of "even" and "odd", there will be two subsequent groups of "even" and "odd".
	 * 
	 * The order of the classes is that in which the options have been defined.
	 * 
	 * @param Array[String] $classes css-classes to apply to the options
	 * @return InputCheckbox method owner
	 */
	public function setOptionCssClasses(Array $classes){
		$this->optionCssClasses = $classes;
		return $this;
	}
	
	
	
	/**
	 * Sets html-title to set for the options.
	 * The same speciality here as with the classes: they cycle, if not enough were defined for all options.
	 * If you have 4 options and you define two classes, there will be two subsequent groups of of both titles.
	 * 
	 * The order of the titles is that in which the options have been defined.
	 * 
	 * @param Array[String] $classes html-titles to apply to the options
	 * @return InputCheckbox method owner
	 */
	public function setOptionTitles(Array $titles){
		$this->optionTitles = $titles;
		return $this;
	}
	
	
	
	/**
	 * Sets selected options by their text.
	 * 
	 * @param Array[String] $selected texts of the selected options
	 * @return InputCheckbox method owner
	 */
	public function setSelected(Array $selected){
		$this->selected = $selected;
		return $this;
	}
	
	
	
	/**
	 * Sets a single selected option by its text.
	 * 
	 * @param String $selected the text of the selected option
	 * @return InputCheckbox method owner
	 */
	public function setSelectedSingle($selected){
		$this->selected = array("$selected");
		return $this;
	}
	
	
	
	/**
	 * Sets selected options by their values.
	 * 
	 * @param Array[String] $selected values of the selected options
	 * @return InputCheckbox method owner
	 */
	public function setSelectedValues(Array $selected){
		$this->selectedValues = $selected;
		return $this;
	}
	
	
	
	/**
	 * Sets a single selected option by its value.
	 * 
	 * @param String $selected the value of the selected option
	 * @return InputCheckbox method owner
	 */
	public function setSelectedValue($selected){
		$this->selectedValues = array($selected);
		return $this;
	}
	
	
	
	/**
	 * Sets the selected options by their indizes.
	 * Indices start with 1.
	 * 
	 * @param Array[uint] $selected indices of the selected options
	 * @return InputCheckbox method owner
	 */
	public function setSelectedIndices(Array $selected){
		$this->selectedIndices = $selected;
		return $this;
	}
	
	
	
	/**
	 * Sets a single selected option by its index.
	 * Indices start with 1.
	 * 
	 * @param uint $selected the index of the selected option
	 * @return InputCheckbox method owner
	 */
	public function setSelectedIndex($selected){
		$this->selectedIndices = array($selected);
		return $this;
	}
	
	
	
	/**
	 * Set the amount of columns for the checkbox-display.
	 * The width defines how many checkbox-label-pairs will be put into one row.
	 * 
	 * @param uint $width the amount of checkboxes next to each other in one row
	 * @return InputCheckbox method owner
	 */
	public function setWidth($width){
		$this->width = (integer) $width;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the current value of the element.
	 * An InputCheckbox always returns an array of strings, which is compiled from
	 * the selected options.
	 * 
	 * @return Array[String] the values of all currently selected options
	 */
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
			(in_array("$value", $this->selectedValues))
			||
			(in_array("$text", $this->selected))
		);
	}
	
	
	
	//---|functionality----------
	
	/**
	 * Tries to refill the selected options from existing data.
	 * This data can eiter be one of the method-arrays dependent on the
	 * method the surrounding form uses or a supplied array of name-value-pairs.
	 * 
	 * @param Array[String]|null $refiller data to use as the refill source
	 * @param Boolean $condition expression which defines if the refill will take place or not, to make it conditional so to speak
	 * @return InputCheckbox method owner
	 */
	public function refill($refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
	
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) && is_array($refiller[$this->name]) ){
				$this->selectedValues = HtmlFormTools::undoMagicQuotes($refiller[$this->name]);
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
	
	
	
	/**
	 * Starts the validation-process for the element.
	 * Calculates the validity-status, based on the currently selected options, by applying the rules
	 * of a present validator. If there is none, the element is always valid.
	 * 
	 * @return Boolean element is currently valid yes/no
	 */
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
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		
		$index = 0;
		$options = '';
		foreach( $this->options as $value => $text ){
			$index++;
			$checkId = $this->masterForm->getId().'_checkbox_'.$this->name.'_'.$value;
			$options .=
				'<input'
					.' type="checkbox"'
					.' id="'.$checkId.'"'
					.$this->printNameArray()
					.' value="'.HtmlFormTools::auto_htmlspecialchars($value, $this->needsUtf8Safety()).'"'
					.($this->isSelectedOption($index, $value, $text) ? ' checked="checked"' : '')
					.((count($this->optionCssClasses) > 0) ? ' class="'.$this->optionCssClasses[(($index - 1) % count($this->optionCssClasses))].'"'  : $this->printCssClasses())
					.(((count($this->optionTitles) > 0) && !empty($this->optionTitles[(($index - 1) % count($this->optionTitles))])) ? ' title="'.$this->optionTitles[(($index - 1) % count($this->optionTitles))].'"'  : '')
					.$this->printTabIndex()
					.$this->printDisabled()
					.$this->masterForm->printSlash()
				.'>'
				.'&nbsp;'.Label::getInline($text, $checkId)->doRender()
				.((($index % $this->width) == 0) ? '<br'.$this->masterForm->printSlash().'>' : '&nbsp;&nbsp;&nbsp;')
			;
		}
	
		return
			'<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'"'.$this->printJavascriptEventHandler().'>'
					.$options
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
			.$this->printJavascriptValidationCode()
		;
	}
}

?>