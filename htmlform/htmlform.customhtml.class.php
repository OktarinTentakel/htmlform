<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//---|class----------

/**
 * CustomHtml is a formelement to provide a container for custom html-content to be used inside a form.
 * Not every eventuality is covered by this framework, for the rest and small quirks this class can be used.
 * Basically this element doesn't do anything apart from wrapping some custom code into a container for insertion
 * into a HtmlForm.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.8 beta
 * @package formelements
 * @subpackage special-widgets
 */
class CustomHtml extends FormElement{
	
	/**
	 * css-class for the widget container
	 * @var String
	 */
	const WRAPPERCLASS = 'htmlform_custom';
	
	
	
	// ***
	private $html;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $id html-id for the element
	 */
	protected function __construct($id = ''){
		parent::__construct('', $id);
		
		$this->html = '';
	}
	
	
	
	/**
	 * Factory method for CustomHtml, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $id html-id for the element
	 * @return CustomHtml new CustomHtml-instance
	 */
	public static function get($id = ''){
		$res = new CustomHtml($id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the html-content to wrap into the element.
	 * Just write ordinary raw html, keep any context dependencies in mind and
	 * remember that the code is wrapped before inserted.
	 * 
	 * @param String $html the raw-html to wrap in the element
	 * @return CustomHtml method owner
	 */
	public function setHtml($html){
		$this->html = "$html";
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Since CustomHtml doesn't hold any value, this method will always return null.
	 * 
	 * @return null
	 */
	public function getValue(){
		return null;
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * This includes the wrapped html-code without any alterations.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		$this->cssClasses = self::WRAPPERCLASS.(($this->cssClasses != '') ?' '.$this->cssClasses : '');
		
		return
			 '<div'.$this->printName().$this->printId().$this->printCssClasses().'>'
				.$this->html
			.'</div>'
		;
	}
}

?>