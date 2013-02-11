<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Wraps a select.
 * This class implements a select in all its variations, be it as single-select, mulit-select, with or without optgroups.
 * You name it. It's simply a very percise object-build of a select box.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package formelements
 * @subpackage value-widgets
 */
class Select extends FormElement{
	// ***
	private $options;
	private $optionCssClasses;
	private $optionTitles;
	private $optGroups;
	private $selected;
	private $subDisabled;
	private $size;
	private $multiple;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->options = array();
		$this->optionCssClasses = array();
		$this->optionTitles = array();
		$this->optGroups = array();
		$this->selected = array();
		$this->subDisabled = array();
		$this->size = 1;
		$this->multiple = false;
	}
	
	
	
	/**
	 * Factory method for Select, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return Select new Select-instance
	 */
	public static function get($name, $id = ''){
		$res = new Select($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets all available options of the select.
	 * The options have to be given in the form of an associative array, where keys are the option-values and
	 * values are the texts for each option.
	 * array('val1' => 'nice option', 'val2' => 'not so nice option', ...)
	 * 
	 * @param Array[String] $options the options for the select
	 * @return Select method owner
	 */
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
	 * Sets selected single/multiple options by index/value/text.
	 * 
	 * @param * $selected single index/value/text or array of indices/values/texts to select
	 * @return Select method owner
	 */
	public function setSelected($selected){
		if( !is_array($selected) ){
			$selected = array("$selected");
		}

		if( !$this->multiple ){
			$this->selected = !empty($selected) ? array(''.$selected[0]) : array();
		} else {
			$this->selected = $selected;
		}
		
		return $this;
	}
	
	
	
	/**
	 * Sets the amount of rows the select should have, especially needed for multi-selects.
	 * 
	 * @param uint $size amount of rows
	 * @return Select method owner
	 */
	public function setSize($size){
		$this->size = (integer) $size;
		return $this;
	}
	
	
	
	/**
	 * Set the select to being a single-select.
	 * 
	 * @return Select method owner
	 */
	public function setSingle(){
		$this->multiple = false;
		return $this;
	}
	
	
	
	/**
	 * Set the select to being a multi-select.
	 * 
	 * @return Select method owner
	 */
	public function setMultiple(){
		$this->multiple = true;
		return $this;
	}
	
	
	
	/**
	 * Set the element disabled, or set single options disabled.
	 * 
	 * @param OPTIONAL * $subDisabled single/multiple indices/values/texts to disable, multiple values must be enclosed in an array
	 * 
	 * @return FormElement method owner
	 */
	public function setDisabled(){
		$params = func_get_arg(0);

		if( $params === false ){
			$this->disabled = true;
		} else {
			$this->subDisabled =  is_array($params) ? $params : array("$params");
		}
		
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the current value of the element.
	 * A Select returns either a single string, if it's a single-select or
	 * and array of string when being multi-selectable. Both are compiled
	 * from the currently selected options of course.
	 * 
	 * @return String/Array[String] the value(s) of (all) currently selected option(s)
	 */
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
			(in_array($index, $this->selected, true))
			||
			(in_array("$value", $this->selected, true))
			||
			(in_array("$text", $this->selected, true))
		);
	}



	private function isDisabledOption($index, $value, $text){
		return(
			(in_array($index, $this->subDisabled, true))
			||
			(in_array("$value", $this->subDisabled, true))
			||
			(in_array("$text", $this->subDisabled, true))
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
	 * @return Select method owner
	 */
	public function refill($refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
		
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) ){
				$values = HtmlFormTools::undoMagicQuotes($refiller[$this->name]);
				$this->setSelected(is_array($refiller[$this->name]) ? $values : array($values));
			} elseif( ($this->masterForm != null) && $this->masterForm->hasBeenSent() ) {
				$this->selected = array();
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

			foreach( $this->selected as $selected ){
				if( is_int($selected) && isset($this->options[$index-1]) ){
					$vals[] = $this->options[$index-1];
				} else {
					$selected = "$selected";

					if( $val = array_search($selected, $this->options) ){
						$vals[] =  $val;
					} elseif( isset($this->options[$selected]) ){
						$vals[] = $selected;
					}
				}
			}
			
			$this->validator->setValues(array_unique($vals));
			$this->isValid = $this->validator->process();
		}
		
		return $this->isValid;
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
				.($this->isDisabledOption($index, $value, $text) ? ' disabled="disabled"' : '')
			.'>'
				.HtmlFormTools::auto_htmlspecialchars($text, $this->needsUtf8Safety())
			.'</option>'
		;
	}
	
	
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
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
						.$this->printJavascriptEventHandler()
						.$this->printTabindex()
						.$this->printDisabled()
					.'>'
						.$optGroups
						.$options
					.'</select>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
			.$this->printJavascriptValidationCode()
		;
	}
}

?>