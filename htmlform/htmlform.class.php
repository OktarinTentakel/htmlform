<?php

//---|includes----------

require_once('htmlform.formelement.absclass.php');

require_once('htmlform.inputbutton.class.php');
require_once('htmlform.inputcheckbox.class.php');
require_once('htmlform.inputradio.class.php');
require_once('htmlform.inputsubmit.class.php');
require_once('htmlform.inputtext.class.php');
require_once('htmlform.inputhidden.class.php');

require_once('htmlform.select.class.php');

require_once('htmlform.alignblock.class.php');
require_once('htmlform.customhtml.class.php');
require_once('htmlform.fieldset.class.php');

require_once('htmlform.formvalidator.class.php');
require_once('htmlform.formvalueset.class.php');



//---|class----------

class HtmlForm{
	// ***
	const CELLCLASS = 'htmlform_cell';
	const HEADLINECLASS = 'htmlform_formheadline';
	const EXPLANATIONCLASS = 'htmlform_formexplanation';
	const MESSAGESCLASS = 'htmlform_messages_div';
	const MESSAGESTITLECLASS = 'htmlform_messages_title_div';
	
	private $xhtml;
	
	private $isValid;
	private $language;
	private $usesExternalFormDeclaration;
	
	private $id;
	private $action;
	private $method;
	private $charset;
	private $cssClasses;
	private $tabIndex;
	private $cells;
	private $headline;
	private $explanation;
	private $messagesTitle;
	private $showMessages;
	
	private function __construct($id){
		$this->xhtml = false;
		
		$this->isValid = true;
		$this->language = 'english';
		$this->usesExternalFormDeclaration = false;
		
		$this->id = "$id";
		$this->action = '';
		$this->method = 'post';
		$this->charset = 'UTF-8';
		$this->cssClasses = '';
		$this->tabIndex = 1;
		$this->cells = array(0 => array());
		$this->headline = '';
		$this->explanation = '';
		$this->messagesTitle = '';
		$this->showMessages = false;
		
		$this->addElement(
			InputHidden::get($this->id.'_sent')
				->setValue('true')
		);
		
		return $this;
	}
	
	
	
	static public function get($id){
		$res = new HtmlForm($id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setXhtml(){
		$this->xhtml = true;
		return $this;
	}
	
	
	
	public function setLanguage($language){
		$this->language = $language;
		return $this;
	}
	
	
	
	public function setAction($action){
		$this->action = "$action";
		return $this;
	}
	
	
	
	public function setMethodPost(){
		$this->method = 'post';
		return $this;
	}
	
	
	
	public function setMethodGet(){
		$this->method = 'get';
		return $this;
	}
	
	
	
	public function setCharsetUtf8(){
		$this->charset = 'UTF-8';
		return $this;
	}
	
	
	
	public function setCharsetLatin(){
		$this->charset = 'ISO-8859-1';
		return $this;
	}
	
	
	
	public function setCharsetLatinExtended(){
		$this->charset = 'ISO-8859-15';
		return $this;
	}
	
	
	
	public function setCssClasses($cssClasses){
		$this->cssClasses = "$cssClasses";
		return $this;
	}
	
	
	
	public function setTabIndex($tabIndex){
		$this->tabIndex = (integer) $tabIndex;
		return $this;
	}
	
	
	
	public function setCells($cells){
		$this->cells = array();
		for( $i = 0; $i < (integer) $cells; $i++ ){
			$this->cells[] = array();
		}
		return $this;
	}
	
	
	
	public function setHeadline($headline){
		$this->headline = "$headline";
		return $this;
	}
	
	
	
	public function setExplanation($explanation){
		$this->explanation = "$explanation";
		return $this;
	}
	
	
	
	public function showMessages($title = '', $show = true){
		$this->messagesTitle = "$title";
		$this->showMessages = $show;
		return $this;
	}
	
	
	
	//---|getter----------
	
	public function getId(){
		return $this->id;
	}
	
	
	
	public function getTabIndex(){
		return $this->tabIndex;
	}
	
	
	
	public function getLanguage(){
		return $this->language;
	}
	
	
	
	public function getValueSet(FormValueSet $addedSet = null){
		$valueSet = is_null($addedSet) ? new FormValueSet() : $addedSet;
	
		foreach( $this->cells as $cell ){
			foreach( $cell as $element ){
				$valueSet = $element->getValueSet($valueSet);
			}
		}
		return $valueSet;
	}
	
	
	
	//---|questions----------
	
	public function isValid(){
		return $this->isValid;
	}
	
	
	
	public function usesExternalFormDeclaration(){
		return $this->usesExternalFormDeclaration;
	}
	
	
	
	//---|functionality----------
	
	public function validate(){
		foreach( $this->cells as $cell ){
			foreach( $cell as $element ){
				$this->isValid = $this->isValid and $element->validate();
			}
		}
		return $this->isValid;
	}
	
	
	
	public function addCssClasses($cssClasses){
		$this->cssClasses .= "$cssClasses";
		return $this;
	}
	
	
	
	public function addElement(FormElement $element, $cell = 1){
		if( is_array($this->cells[($cell - 1)]) ){
			$element->setMasterForm($this);
			$this->cells[($cell - 1)][] = $element;
		}
		
		return $this;
	}
	
	
	
	public function incTabIndex(){
		$this->tabIndex++;
		return $this;
	}
	
	
	
	public function addCell(){
		$this->cells[] = array();
		return $this;
	}
	
	
	
	public function useExternalFormDeclaration(){
		$this->usesExternalFormDeclaration = true;
		return $this;
	}
	
	
	
	//---|output----------
	
	public function printSlash(){
		return ($this->xhtml ? '/' : '');
	}
	
	
	
	public function printFloatBreak(){
		return '<div style="clear:both; height:0px; margin:0px; padding:0px; font-size:0px;">&nbsp;</div>';
	}
	
	
	
	private function printHeadline(){
			return (($this->headline != '') ? ' <div id="'.$this->id.'_formheadline" class="'.self::HEADLINECLASS.'">'.$this->headline.'</div>' : '');
	}
	
	
	
	private function printExplanation(){
			return (($this->explanation != '') ? ' <div id="'.$this->id.'_formexplanation" class="'.self::EXPLANATIONCLASS.'">'.$this->explanation.'</div>' : '');
	}
	
	
	
	private function printMessages(){
		$msg = '';
		
		foreach( $this->cells as $cell ){
			foreach( $cell as $element ){
				$msg .= $element->printMessages();
			}
		}
		
		if( $this->showMessages && $msg != '' ){
			$title = ($this->messagesTitle != '') ? '<div class="'.self::MESSAGESTITLECLASS.'">'.$this->messagesTitle.'</div>' : '';
			
			return 
				 '<div class="'.self::MESSAGESCLASS.'">'
					.$title
					.$msg
					.$this->printFloatBreak()
				.'</div>'
			;
		} else {
			return '';
		}
	}
	
	
	
	private function printFormDeclaration($formContent){
		return 	$this->usesExternalFormDeclaration
							? $formContent
							: (
									'<form id="'.$this->id.'" action="'.$this->action.'" method="'.$this->method.'" accept-charset="'.$this->charset.'"'.(($this->cssClasses != '') ? ' class="'.$this->cssClasses.'"' : '').'>'
									 .$formContent
								.'</form>'
								)
		;
	}
	
	
	
	public function doRender(){
		$cells = '';
		for( $i = 0; $i < count($this->cells); $i++ ){
			$subs = '';
			foreach( $this->cells[$i] as $el ){
				$subs .= $el->doRender();
			}
			
			$cells .=
				 '<div class="'.self::CELLCLASS.' '.self::CELLCLASS.'_'.($i + 1).'">'
					.$subs
				.'</div>'
			;
		}
	
		return
			 $this->printHeadline()
			.$this->printExplanation()
			.$this->printMessages()
			.$this->printFormDeclaration(
				$cells
			 .$this->printFloatBreak()
			)
		;
	}
}

?>