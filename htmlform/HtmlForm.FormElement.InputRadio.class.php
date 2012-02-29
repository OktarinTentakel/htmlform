<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Wraps a group of radiobuttons.
 * HtmlForm doesn't deal with single radiobuttons but always with groups of them. But nonetheless I still do recommend
 * radiobuttons for binary choices as well, since that's one the use-cases they've been designed for, instead of using
 * a checkbox. Otherwise the buttons excatly behave like you would expect them to. Many options, one choice.
 * 
 * Setting a title or css-classes for this element will not result in anything. Instead, set titles and classes for the options
 * themselves, by using the appropriate methods.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.95 beta
 * @package formelements
 * @subpackage value-widgets
 */
class InputRadio extends FormElement{
	
	// ***
	private $options;
	private $optionCssClasses;
	private $optionTitles;
	private $selected;
	private $selectedValue;
	private $selectedIndex;
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
		$this->selected = null;
		$this->selectedValue = null;
		$this->selectedIndex = null;
		$this->width = 1;
	}
	
	
	
	/**
	 * Factory method for InputRadio, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @return InputRadio new InputRadio-instance
	 */
	public static function get($name){
		$res = new InputRadio($name);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets all available options of the radio-group.
	 * The options have to be given in the form of an associative array, where keys are the radio-values and
	 * values are the label-texts for each radiobutton.
	 * array('val1' => 'nice radiobutton', 'val2' => 'not so nice radiobutton', ...)
	 * 
	 * @param Array[String] $options the options for the radio-group
	 * @return InputRadio method owner
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
	 * @return Select method owner
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
	 * @return Select method owner
	 */
	public function setOptionTitles(Array $titles){
		$this->optionTitles = $titles;
		return $this;
	}
	
	
	
	/**
	 * Sets the selected option by its text.
	 * 
	 * @param String $selected the text of the selected option
	 * @return InputRadio method owner
	 */
	public function setSelected($selected){
		$this->resetSelection();
		$this->selected = "$selected";
		return $this;
	}
	
	
	
	/**
	 * Sets the selected option by its value.
	 * 
	 * @param String $selected the value of the selected option
	 * @return InputRadio method owner
	 */
	public function setSelectedValue($selected){
		$this->resetSelection();
		$this->selectedValue = "$selected";
		return $this;
	}
	
	
	
	/**
	 * Sets the selected option by its index.
	 * Indices start with 1.
	 * 
	 * @param uint $selected the index of the selected option
	 * @return InputRadio method owner
	 */
	public function setSelectedIndex($selected){
		$this->resetSelection();
		$this->selectedIndex = $selected;
		return $this;
	}
	
	
	
	/**
	 * Set the amount of columns for the radiobutton-display.
	 * The width defines how many radio-label-pairs will be put into one row.
	 * 
	 * @param uint $width the amount of radiobuttons next to each other in one row
	 * @return InputRadio method owner
	 */
	public function setWidth($width){
		$this->width = (integer) $width;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the current value of the element.
	 * An InputRadio always returns a strings, which is taken from the selected option.
	 * 
	 * @return String the value of the currently selected option
	 */
	public function getValue(){
		$index = 0;
		foreach( $this->options as $value => $text ){
			$index++;
			if( $this->isSelectedOption($index, $value, $text) ){
				return $value;
			}
		}
		
		return null;
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
	
	/**
	 * Tries to refill the selecte option from existing data.
	 * This data can eiter be one of the method-arrays dependent on the
	 * method the surrounding form uses or a supplied array of name-value-pairs.
	 * 
	 * @param Array[String]|null $refiller data to use as the refill source
	 * @param Boolean $condition expression which defines if the refill will take place or not, to make it conditional so to speak
	 * @return InputRadio method owner
	 */
	public function refill($refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
	
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) && !is_array($refiller[$this->name]) ){
				$this->selectedValue = ''.HtmlFormTools::undoMagicQuotes($refiller[$this->name]);
				$this->selected = null;
				$this->selectedIndex = null;
			} elseif( $this->masterForm != null && $this->masterForm->hasBeenSent() ) {
				$this->selectedValues = null;
				$this->selected = null;
				$this->selectedIndices = null;
			}
		}
		
		return $this;
	}
	
	
	
	/**
	 * Starts the validation-process for the element.
	 * Calculates the validity-status, based on the currently selected option, by applying the rules
	 * of a present validator. If there is none, the element is always valid.
	 * 
	 * @return Boolean element is currently valid yes/no
	 */
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
	
	
	
	private function resetSelection(){
		$this->selectedIndex = null;
		$this->selectedValue = null;
		$this->selected = null;
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
			$radioId = $this->masterForm->getId().'_radio_'.$this->name.'_'.$value;
			$options .=
				'<input'
					.' type="radio"'
					.' id="'.$radioId.'"'
					.$this->printName()
					.' value="'.HtmlFormTools::auto_htmlspecialchars($value, $this->needsUtf8Safety()).'"'
					.($this->isSelectedOption($index, $value, $text) ? ' checked="checked"' : '')
					.((count($this->optionCssClasses) > 0) ? ' class="'.$this->optionCssClasses[(($index - 1) % count($this->optionCssClasses))].'"'  : $this->printCssClasses())
					.(((count($this->optionTitles) > 0) && !empty($this->optionTitles[(($index - 1) % count($this->optionTitles))])) ? ' title="'.$this->optionTitles[(($index - 1) % count($this->optionTitles))].'"'  : '')
					.$this->printTabIndex()
					.$this->printDisabled()
					.$this->masterForm->printSlash()
				.'>'
				.'&nbsp;'.Label::getInline($text, $radioId)->doRender()
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