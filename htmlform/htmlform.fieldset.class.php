<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');



//---|class----------

class FieldSet extends FormElement{
	// ***
	private $legend;
	
	protected function __construct($id = ''){
		parent::__construct('', $id);
		
		$this->legend = '';
		$this->subElements = array();
	}
	
	
	
	public static function get($id = ''){
		$res = new FieldSet($id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setLegend($legend){
		$this->legend = "$legend";
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getValue(){
		return null;
	}
	
	
	
	//---|output----------
	
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
				 .$this->printJsEventHandler()
			 .'>'
				.(($this->legend != '') ? '<legend>'.$this->legend.'</legend>' : '')
				.$subs
				.$this->masterForm->printFloatBreak()
			.'</fieldset>'
		;
	}
}

?>