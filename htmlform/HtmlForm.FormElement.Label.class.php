<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';



//--|class----------

/**
 * Wraps a label for the use in the direct vicinity of a formelement.
 * Each value-widget can have a label, to describe it's purpose, this class gathers the necessary information,
 * and compile the label with correct linkage and wrapping.
 * 
 * If you want a logical connection between a label and an element, make sure the element has an id, since that's
 * what defines the connection here.
 * 
 * This element should not be inserted manually into a form, but rather be seen as a subwidget automatically provided
 * the a value-bearing-element. If you implement one yourself, be sure to also use Label.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.99 beta
 * @package formelements
 * @subpackage special-widgets
 */
class Label extends FormElement{
	
	/**
	 * css-class for the label wrapper
	 * @var String
	 */
	const WRAPCLASS = 'htmlform_label_div';
	
	// ***
	private $caption;
	private $for;
	private $inline;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param FormElement $inputToLabel the element to attach the label to
	 */
	protected function __construct(FormElement $inputToLabel){
		$this->caption = ''.$inputToLabel->getLabel();
		$this->for = ''.$inputToLabel->getId();
		$this->inline = false;
	}
	
	
	
	/**
	 * Factory method for Label, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param FormElement $inputToLabel the element to attach the label to
	 * @return Label new Label-instance
	 */
	static public function get(FormElement $inputToLabel){
		$res = new Label($inputToLabel);
		return $res;
	}
	
	
	
	/**
	 * Special factory-method for labels to be displayed inline, and not in a own wrapping element.
	 * 
	 * @param String $caption the text the label should show
	 * @param String $for the id of the element the label should be associated to
	 * @return Label new inline-label
	 */
	static public function getInline($caption, $for){
		$res = new Label(CustomHtml::get($for)->setLabel($caption));
		$res->setInline();
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the element to inline-mode, where no wrapping container is rendered, but only
	 * the pure label-tag.
	 * 
	 * @return Label method owner
	 */
	public function setInline(){
		$this->inline = true;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Since Label doesn't hold any value, this method will always return null.
	 * 
	 * @return null
	 */
	public function getValue(){
		return null;
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * Wrapped in normal mode, barebone if set to inline.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		return
			 ((!$this->inline) ? '<div class="'.self::WRAPCLASS.'">' : '')
				.'<label '.(($this->for != '') ? 'for="'.$this->for.'"' : '').'>'
					.HtmlFormTools::auto_htmlspecialchars($this->caption, $this->needsUtf8Safety())
				.'</label>'
			.((!$this->inline) ? '</div>' : '')
		;
	}
}

?>