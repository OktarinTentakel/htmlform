<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//--|class----------

class Label extends FormElement{
	// ***
	const WRAPCLASS = 'htmlform_label_div';
	
	private $caption;
	private $for;
	private $inline;
	
	protected function __construct(FormElement $inputToLabel){
		$this->caption = ''.$inputToLabel->getLabel();
		$this->for = ''.$inputToLabel->getId();
		$this->inline = false;
	}
	
	
	
	static public function get(FormElement $inputToLabel){
		$res = new Label($inputToLabel);
		return $res;
	}
	
	
	
	static public function getInline($caption, $for){
		$res = new Label(CustomHtml::get($for)->setLabel($caption));
		$res->setInline();
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setInline(){
		$this->inline = true;
	}
	
	
	
	//---|getter----------
	
	public function getValue(){
		return null;
	}
	
	
	
	//---|output----------
	
	public function doRender(){
		return
			 ((!$this->inline) ? '<div class="'.self::WRAPCLASS.'">' : '')
				.'<label '.(($this->for != '') ? 'for="'.$this->for.'"' : '').'>'
					.$this->caption
				.'</label>'
			.((!$this->inline) ? '</div>' : '')
		;
	}
}

?>