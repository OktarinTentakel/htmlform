<?php

//---|includes----------

require_once 'htmlform.formelement.absclass.php';

require_once 'htmlform.tools.class.php';



//---|class----------

/**
 * Wraps a hidden input.
 * If you need to get some random data into the valueset of a form, this is, like in html, they way to go.
 * This element, doesn't display, but only carries a value by the same means a text-input would, just without the
 * editing possibilites.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.8 beta
 * @package formelements
 * @subpackage value-widgets
 */
class InputHidden extends FormElement{
	// ***
	private $value;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->value = '';
	}
	
	
	
	/**
	 * Factory method for InputHidden, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputHidden new InputHidden-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputHidden($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Directly sets the value for the element.
	 * 
	 * @param String $val new value for the element
	 * @return InputHidden method owner
	 */
	public function setValue($val){
		$this->value = "$val";
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the currently set value of the element.
	 * 
	 * @return String currently set value of the element
	 */
	public function getValue(){
		return $this->value;
	}
	
	
	
	//---|functionality----------
	
	/**
	 * Tries to refill the element-value from existing data.
	 * This data can eiter be one of the method-arrays dependent on the
	 * method the surrounding form uses or a supplied array of name-value-pairs.
	 * 
	 * @param Array[String] $refiller data to use as the refill source
	 * @param Boolean $condition expression which defines if the refill will take place or not, to make it conditional so to speak
	 * @return InputText method owner
	 */
	public function refill(Array $refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
	
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) && !is_array($refiller[$this->name]) ){
				$this->value = ''.HtmlFormTools::undoMagicQuotes($refiller[$this->val]);
			}
		}
		
		return $this;
	}
	
	
	
	/**
	 * Starts the validation-process for the element.
	 * Calculates the validity-status, based on the currently entered value, by applying the rules
	 * of a present validator. If there is none, the element is always valid.
	 * 
	 * @return Boolean element is currently valid yes/no
	 */
	public function validate(){
		parent::validate();
		
		if( !is_null($this->validator) ){
			$this->validator->setValue($this->value);
			$this->isValid = $this->validator->process();
		}
		
		return $this->isValid;
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * InputHidden has a very reduced rendering, with things like css-classes and everything visual ignored.
	 * If you need them for whatever reason, you're doing something wrong.
	 * This element is for form-value-injection only.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		return
			'<input'
				.$this->printId()
				.$this->printName()
				.' type="hidden"'
				.' value="'.HtmlFormTools::auto_htmlspecialchars($this->value, $this->needsUtf8Safety()).'"'
				.$this->masterForm->printSlash()
			.'>'
		;
	}
}

?>