<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');
require_once('htmlform.label.class.php');

require_once('htmlform.tools.class.php');



//---|class----------

/**
 * Wraps a textarea.
 * This element is made for long, multi-line text entries in contrast to short inputs handled by InputText.
 * In general both mostly work the same way, with a tad more config-options for the textarea.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.8 beta
 * @package formelements
 * @subpackage value-widgets
 */
class TextArea extends FormElement{
	
	// ***
	/**
	 * the currently entered text (used for refill mostly, not synchronized to user input)
	 * @var String
	 */
	protected $text;
	
	
	/**
	 * the amount of characters to display horizontally, defines visual width as well
	 * @var String
	 */
	protected $cols;
	
	/**
	 * the amount of lines to display vertically, defines visual height as well
	 * @var unknown_type
	 */
	protected $rows;
	
	
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
		
		$this->cols = 0;
		$this->rows = 0;
		
		$this->readonly = false;
	}
	
	
	
	/**
	 * Factory method for TextArea, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return TextArea new TextArea-instance
	 */
	public static function get($name, $id = ''){
		$res = new TextArea($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the text to start with.
	 * This text will be rendered into the html-code.
	 * 
	 * @param String $text the text to insert into the element
	 * @return TextArea method owner
	 */
	public function setText($text){
		$this->text = "$text";
		return $this;
	}
	
	
	
	/**
	 * Sets the amount of characters to display horizontally, defines visual width as well
	 * 
	 * @param uint $cols amount of characters to show horizontally
	 * @return TextArea method owner
	 */
	public function setCols($cols){
		if( is_numeric($cols) && ($cols > 0) ){
			$this->cols = $cols;
		}
		return $this;
	}
	
	
	
	/**
	 * Sets the amount of lines to display vertically, defines visual height as well.
	 * 
	 * @param uint $rows the amount of lines to show
	 * @return TextArea method owner
	 */
	public function setRows($rows){
		if( is_numeric($rows) && ($rows > 0) ){
			$this->rows = $rows;
		}
		return $this;
	}
	
	
	
	/**
	 * Combination of setCols() and setRows().
	 * Sets both characters to display horizontally and lines to show vertically at once.
	 * 
	 * @param uint $cols amount of characters to show horizontally
	 * @param uint $rows the amount of lines to show
	 * @return TextArea method owner
	 */
	public function setSize($cols, $rows){
		$this->setCols($cols);
		$this->setRows($rows);
		return $this;
	}
	
	
	
	/**
	 * Sets that the element should be read-only.
	 * 
	 * @return TextArea method owner
	 */
	public function setReadonly(){
		$this->readonly = true;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the current value of the element.
	 * In case of a textarea this is always a simple, single string.
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
	 * @param Array[String] $refiller data to use as the refill source
	 * @param Boolean $condition expression which defines if the refill will take place or not, to make it conditional so to speak
	 * @return TextArea method owner
	 */
	public function refill(Array $refiller = array(), $condition = true){
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
	 * Compiles and returns the html-cols-attribute for the element.
	 * 
	 * @return String the html-cols-attribute of the element
	 */
	protected function printCols(){
		return ($this->cols > 0) ? ' cols="'.$this->cols.'"' : '';
	}
	
	
	
	/**
	 * Compiles and returns the html-rows-attribute for the element.
	 * 
	 * @return String the html-rows-attribute of the element
	 */
	protected function printRows(){
		return ($this->rows > 0) ? ' rows="'.$this->rows.'"' : '';
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
					.'<textarea'
						.$this->printId()
						.$this->printName()
						.$this->printTitle()
						.$this->printCols()
						.$this->printRows()
						.$this->printCssClasses()
						.$this->printJsEventHandler()
						.$this->printTabindex()
						.$this->printReadonly()
						.$this->printDisabled()
					.'>'
						.HtmlFormTools::auto_htmlspecialchars($this->text, $this->needsUtf8Safety())
					.'</textarea>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>