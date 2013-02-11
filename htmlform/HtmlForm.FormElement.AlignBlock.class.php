<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';



//---|class----------

/**
 * This formelement fulfills the purpose of being a visual gatherer for other formelements.
 * In general this is a container element used to style a certain form-part differently or to align
 * the widgets in another fashion.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package formelements
 * @subpackage container-widgets
 */
class AlignBlock extends FormElement{
	
	/**
	 * css-class for the widget container
	 * @var String
	 */
	const WRAPPERCLASS = 'htmlform_alignblock';
	
	
	
	// ***
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $id html-id for the element
	 */
	protected function __construct($id = ''){
		parent::__construct('', $id);
		
		$this->subElements = array();
	}
	
	
	
	/**
	 * Factory method for AlignBlock, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $id html-id for the element
	 * @return AlignBlock new AlignBlock-instance
	 */
	public static function get($id = ''){
		$res = new AlignBlock($id);
		return $res;
	}
	// ***
	
	
	
	//---|getter----------
	
	/**
	 * Since AlignBlock doesn't hold any value, this method will always return null.
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
		$this->cssClasses = self::WRAPPERCLASS.(($this->cssClasses != '') ?' '.$this->cssClasses : '');
		
		$subs = '';
		foreach( $this->subElements as $se ){
			$subs .= $se->doRender();
		}
		
		return
			 '<div'
				 .$this->printName()
				 .$this->printId()
				 .$this->printCssClasses()
				 .$this->printJavascriptEventHandler()
			 .'>'
				.$subs
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>