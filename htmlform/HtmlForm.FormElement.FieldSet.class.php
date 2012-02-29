<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';



//---|class----------

/**
 * FieldSet provides the means to group formelements in a HtmlForm by using normal html-fieldsets.
 * Generally this is just a container element without value. Insert widgets into it, skin it with css and you're done.
 * It behaves exactly like the raw html pendant.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.99 beta
 * @package formelements
 * @subpackage container-widgets
 */
class FieldSet extends FormElement{
	// ***
	private $legend;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $id html-id for the element
	 */
	protected function __construct($id = ''){
		parent::__construct('', $id);
		
		$this->legend = '';
		$this->subElements = array();
	}
	
	
	
	/**
	 * Factory method for FieldSet, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $id html-id for the element
	 * @return FieldSet new FieldSet-instance
	 */
	public static function get($id = ''){
		$res = new FieldSet($id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Set the legend/caption the fieldset should display.
	 * 
	 * @param String $legend the legend/caption for the fieldset
	 * @return Fieldset method owner
	 */
	public function setLegend($legend){
		$this->legend = "$legend";
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Since FieldSet doesn't hold any value, this method will always return null.
	 * 
	 * @return null
	 */
	public function getValue(){
		return null;
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles and returns the html-fragment for the element including all subelements.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		$subs = '';
		foreach( $this->subElements as $se ){
			$subs .= $se->doRender();
		}
		
		return
			 '<fieldset'
				 .$this->printName()
				 .$this->printId()
				 .$this->printCssClasses()
				 .$this->printJavascriptEventHandler()
			 .'>'
				.(($this->legend != '') ? '<legend>'.$this->legend.'</legend>' : '')
				.$subs
				.$this->masterForm->printFloatBreak()
			.'</fieldset>'
		;
	}
}

?>