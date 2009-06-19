<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//---|class----------

class AlignBlock extends FormElement{
	const WRAPPERCLASS = 'htmlform_alignblock';
	
	// ***
	protected function __construct($id = ''){
		parent::__construct('', $id);
		
		$this->subElements = array();
	}
	
	
	
	public static function get($id = ''){
		$res = new AlignBlock($id);
		return $res;
	}
	// ***
	
	
	
	//---|output----------
	
	public function doRender(){
		$this->cssClasses = self::WRAPPERCLASS.' '.$this->cssClasses;
		
		$subs = '';
		foreach( $this->subElements as $se ){
			$subs .= $se->doRender();
		}
		
		return
			 '<div'.$this->printName().$this->printId().$this->printCssClasses().'>'
				.$subs
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>