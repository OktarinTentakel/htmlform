<?php

//---|includes----------

require_once 'HtmlForm.FormElement.InputSubmit.class.php';



//---|class----------

/**
 * Wraps an image-form-submit-button.
 * To get click-coordinates use getCoords() after submit. 
 * 
 * This element is not wrapped into a row, but should be inserted into a container-widget.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package formelements
 * @subpackage control-widgets
 */
class InputImage extends InputSubmit {
	
	// ***
	private $imageSrc;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
	protected function __construct($name, $id = ''){
		parent::__construct($name, $id);
		
		$this->imageSrc = '';
	}
	
	
	
	/**
	 * Factory method for InputImage, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return InputImage new InputImage-instance
	 */
	public static function get($name, $id = ''){
		$res = new InputImage($name, $id);
		return $res;
	}
	// ***
	
	
	
	//--|setter----------
	
	/**
	 * Sets the image-source-url for the image of the image-submit.
	 * 
	 * @param String $imageSrc source-image url
	 */
	public function setSrc($imageSrc){
		$this->imageSrc = "$imageSrc";
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns if the image_submit-button was used for the last occurred
	 * form-submit.
	 * 
	 * @return Boolean image-submit-button has used for last submit yes/no
	 */
	public function getValue(){
		$refiller = $this->determineRefiller();
		return (isset($refiller[''.$this->name.'_x']) || isset($refiller[''.$this->name.'_y']));
	}
	
	
	
	/**
	 * Returns the clicked coordinates on the image-submit button.
	 * Returns null, if button hasn't been clicked.
	 * 
	 * @return null/Object coordinate-object, ->x and ->y for uint-coordinates 
	 */
	public function getCoords(){
		if( $this->getValue() ){
			$refiller = $this->determineRefiller();
			$res = new StdClass();
			$res->x = (integer)$refiller[''.$this->name.'_x'];
			$res->y = (integer)$refiller[''.$this->name.'_y'];
			return $res;
		} else {
			return null;
		}
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		return
			 '<input'
				 .$this->printId()
				 .$this->printName()
				 .' type="image"'
				 .' alt="submit"'
				 .' src="'.$this->imageSrc.'"'
				 .$this->printTitle()
				 .$this->printCssClasses()
				 .$this->printJavascriptEventHandler()
				 .$this->printTabindex()
				 .$this->printDisabled()
				 .$this->masterForm->printSlash()
			.'>'
		;
	}

}

?>