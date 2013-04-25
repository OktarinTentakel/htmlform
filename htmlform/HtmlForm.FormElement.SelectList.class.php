<?php

//---|includes----------

require_once 'HtmlForm.FormElement.Select.class.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Constitutes a Select-replacement made of a list of labeled radiobuttons/checkboxes.
 * Long Selects, especially multiselects, where you select entries by holding ctrl, can be bothersome to handle.
 * This special, non-canonical class, is a reskin of select functionality using radiobuttons and checkboxes to offer
 * an alternative, with better usability, while offering largely the same functionality as a Select.
 * Single selects are build of radiobuttons, while multiselects are build as a list of checkboxes. 
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package formelements
 * @subpackage value-widgets
 */
class SelectList extends Select {
	
	const SELECT_CLASS = 'select';
	const OPTGROUP_CLASS = 'optgroup';
	const OPTION_CLASS = 'option';
	
	
	
	// ***
	/**
	 * Factory method for SelectList, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return SelectList new SelectList-instance
	 */
	public static function get($name, $id = ''){
		$res = new SelectList($name, $id);
		$res->addCssClasses(self::SELECT_CLASS);
		return $res;
	}
	// ***
	
	
	
	//---|output----------
	
	private function printOption($index, $value, $text){
		return
			'<div class="'.self::OPTION_CLASS.((count($this->optionCssClasses) > 0) ? ' '.$this->optionCssClasses[(($index - 1) % count($this->optionCssClasses))] : '').'">'
				.'<label'
					.(((count($this->optionTitles) > 0) && !empty($this->optionTitles[(($index - 1) % count($this->optionTitles))])) ? ' title="'.$this->optionTitles[(($index - 1) % count($this->optionTitles))].'"'  : '')
					.(($this->disabled || $this->isDisabledOption($index, $value)) ? ' class="disabled"' : '')
				.'>'
					.'<input'
						.' type="'.($this->multiple ? 'checkbox' : 'radio').'"'
						.($this->multiple ? $this->printNameArray() : $this->printName())
						.' value="'.HtmlFormTools::auto_htmlspecialchars($value, $this->needsUtf8Safety()).'"'
						.($this->isSelectedOption($index, $value) ? ' checked="checked"' : '')
						.(($this->disabled || $this->isDisabledOption($index, $value)) ? ' disabled="disabled"' : '')
						.$this->printTabindex()
					.'/>'
					.HtmlFormTools::auto_htmlspecialchars($text, $this->needsUtf8Safety())
				.'</label>'
			.'</div>'
		;
	}
	
	
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
	public function doRender(){
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		
		$index = 0;
		$optGroups = '';
		$options = '';
		foreach( $this->options as $value => $text ){
			$index++;
			$isInOptGroup = false;
			
			foreach( $this->optGroups as $optGroupLabel => $optGroupIndices ){
				$pos = array_search($index, $optGroupIndices);
				if( $pos !== false ){
					$isInOptGroup = true;
				
					if( count($optGroupIndices) > 1 ){
						if( $pos == 0 ){
							$optGroups .=
								'<div class="'.self::OPTGROUP_CLASS.'">'
									.'<label>'.HtmlFormTools::auto_htmlspecialchars($optGroupLabel, $this->needsUtf8Safety()).'</label><br/>'
									.$this->printOption($index, $value, $text)
							;
						} elseif( $pos == (count($optGroupIndices)-1) ){
							$optGroups .=
									$this->printOption($index, $value, $text)
								.'</div>'
							;
						} else {
							$optGroups .= $this->printOption($index, $value, $text);
						}
					} else {
						$optGroups .=
							'<div class="'.self::OPTGROUP_CLASS.'">'
								.'<label>'.HtmlFormTools::auto_htmlspecialchars($optGroupLabel, $this->needsUtf8Safety()).'</label><br/>'
								.$this->printOption($index, $value, $text)
							.'</div>'
						;
					}
				}
			}
			
			if( !$isInOptGroup ){
				$options .=	$this->printOption($index, $value, $text);
			}
		}

		$printJavascriptValidationCode = $this->printJavascriptValidationCode();
	
		return
			 '<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.(!empty($printJavascriptValidationCode) ? ' '.parent::JSENABLEDCLASS : '').'">'
					.'<div'
						.$this->printId()
						.$this->printTitle()
						.$this->printCssClasses()
						.$this->printJavascriptEventHandler()
					.'>'
						.$optGroups
						.$options
					.'</div>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
			.$printJavascriptValidationCode
		;
	}
}

?>