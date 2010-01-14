<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//---|class----------

class CustomHtml extends FormElement{
	// ***
	const WRAPPERCLASS = 'htmlform_custom';
	
	private $html;
	
	protected function __construct($id = ''){
		parent::__construct('', $id);
		
		$this->html = '';
	}
	
	
	
	public static function get($id = ''){
		$res = new CustomHtml($id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setHtml($html){
		$this->html = "$html";
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getValue(){
		return null;
	}
	
	
	
	//---|output----------
	
	public function doRender(){
		$this->cssClasses = self::WRAPPERCLASS.' '.$this->cssClasses;
		
		return
			 '<div'.$this->printName().$this->printId().$this->printCssClasses().'>'
				.$this->html
			.'</div>'
		;
	}
}

?>