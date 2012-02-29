<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Wraps a standard text-input.
 * This element is made for shorter, one-line text-inputs, rather than long texts, which should rather be
 * treated with a textarea.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.99 beta
 * @package formelements
 * @subpackage value-widgets
 */
class InputText extends FormElement{
	
	// ***
	/**
	 * the currently entered text (used for refill mostly, not synchronized to user input)
	 * @var String
	 */
	protected $text;
	
	
	/**
	 * amount of characters the input should hold horizontally, defines visual width as well
	 * @var uint
	 */
	protected $size;
	
	/**
	 * maximum amount of characters that can be inserted into the input, if the number is reached no further characters can be added
	 * @var uint
	 */
	protected $maxLength;
	
	/**
	 * defines if the input is in read-only-state or not
	 * @var Boolean
	 */
	protected $readonly;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->text = '';
		
		$this->size = 0;
		$this->maxLength = 0;
		
		$this->readonly = false;
	}
	
	
	
	/**
	 * Factory method for InputText, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputText new InputText-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputText($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the text to start with.
	 * This text will be rendered into the html-code.
	 * 
	 * @param String $text the text to insert into the element
	 * @return InputText method owner
	 */
	public function setText($text){
		$this->text = "$text";
		return $this;
	}
	
	
	
	/**
	 * Sets the amount of characters the input should hold horizontally.
	 * Defines visual width as well.
	 * 
	 * @param uint $size amount of characters the element should show
	 * @return InputText method owner
	 */
	public function setSize($size){
		if( is_numeric($size) && ($size > 0) ){
			$this->size = $size;
		}
		return $this;
	}
	
	
	
	/**
	 * Sets the maximum amount of characters that can be inserted into the input.
	 * If the number is reached no further characters can be added.
	 * 
	 * @param uint $maxLength
	 * @return InputText method owner
	 */
	public function setMaxLength($maxLength){
		if( is_numeric($maxLength) && ($maxLength > 0) ){
			$this->maxLength = $maxLength;
		}
		return $this;
	}
	
	
	
	/**
	 * Sets that the element should be read-only.
	 * 
	 * @return InputText method owner
	 */
	public function setReadonly(){
		$this->readonly = true;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the current value of the element.
	 * In case of the standard text input this is always a simple, single string.
	 * 
	 * @return String current value of element
	 */
	public function getValue(){
		return $this->text;
	}
	
	
	
	//---|functionality----------
	
	/**
	 * Tries to refill the element-value from existing data.
	 * This data can eiter be one of the method-arrays dependent on the
	 * method the surrounding form uses or a supplied array of name-value-pairs.
	 * 
	 * @param Array[String]|null $refiller data to use as the refill source
	 * @param Boolean $condition expression which defines if the refill will take place or not, to make it conditional so to speak
	 * @return InputText method owner
	 */
	public function refill($refiller = array(), $condition = true){
		if( !is_null($this->masterForm) && !$this->masterForm->hasBeenSent() && empty($refiller) ){
			$condition = false;
		}
	
		if( $condition ){
			$refiller = $this->determineRefiller($refiller);
			
			if( isset($refiller[$this->name]) && !is_array($refiller[$this->name]) ){
				$this->text = ''.HtmlFormTools::undoMagicQuotes($refiller[$this->name]);
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
			$this->validator->setValue($this->text);
			$this->isValid = $this->validator->process();
		}
		
		return $this->isValid;
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles and returns the html-size-attribute for the element.
	 * 
	 * @return String the html-size-attribute of the element
	 */
	protected function printSize(){
		return ($this->size > 0) ? ' size="'.$this->size.'"' : '';
	}
	
	
	
	/**
	 * Compiles and returns the html-maxlength-attribute for the element.
	 * 
	 * @return String the html-maxlength-attribute of the element
	 */
	protected function printMaxLength(){
		return ($this->maxLength > 0) ? ' maxlength="'.$this->maxLength.'"' : '';
	}
	
	
	
	/**
	 * Compiles and returns the html-readonly-attribute for the element.
	 * 
	 * @return String the html-readonly-attribute of the element
	 */
	protected function printReadonly(){
		return $this->readonly ? ' readonly="readonly"' : '';
	}
	
	
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
	
		return
			'<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'">'
					.'<input'
						.$this->printId()
						.$this->printName()
						.' type="text"'
						.' value="'.HtmlFormTools::auto_htmlspecialchars($this->text, $this->needsUtf8Safety()).'"'
						.$this->printTitle()
						.$this->printSize()
						.$this->printMaxLength()
						.$this->printCssClasses()
						.$this->printJavascriptEventHandler()
						.$this->printTabindex()
						.$this->printReadonly()
						.$this->printDisabled()
						.$this->masterForm->printSlash()
					.'>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
			.$this->printJavascriptValidationCode()
		;
	}
}

?>