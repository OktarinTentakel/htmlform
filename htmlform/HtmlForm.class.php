<?php

//---|includes----------

require_once 'HtmlForm.FormElement.absclass.php';

require_once 'HtmlForm.FormElement.InputButton.class.php';
require_once 'HtmlForm.FormElement.InputReset.class.php';
require_once 'HtmlForm.FormElement.InputCheckbox.class.php';
require_once 'HtmlForm.FormElement.InputRadio.class.php';
require_once 'HtmlForm.FormElement.InputSubmit.class.php';
require_once 'HtmlForm.FormElement.InputImage.class.php';
require_once 'HtmlForm.FormElement.InputText.class.php';
require_once 'HtmlForm.FormElement.InputHidden.class.php';
require_once 'HtmlForm.FormElement.InputPassword.class.php';
require_once 'HtmlForm.FormElement.InputFile.class.php';

require_once 'HtmlForm.FormElement.Select.class.php';
require_once 'HtmlForm.FormElement.TextArea.class.php';

require_once 'HtmlForm.FormElement.AlignBlock.class.php';
require_once 'HtmlForm.FormElement.CustomHtml.class.php';
require_once 'HtmlForm.FormElement.FieldSet.class.php';

require_once 'HtmlForm.FormElement.JsDateTime.class.php';

require_once 'HtmlForm.FormValidator.class.php';
require_once 'HtmlForm.FormValueSet.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Main class for the whole HtmlForm-Framework, representing a Form itself.
 * 
 * To minimize the hassle of using this framework this is the only file that needs to be included into
 * a project to make the whole framework known. All needed information and classes follow with this one.
 * 
 * To understand what you're doing: HtmlForm handles normal html4 data-input forms completely as php5-Objects, including
 * all possible widgets and functionality. So by instantiating this class you actually create a form, in which you may insert
 * everything you need for processing your data.
 * 
 * The features of this framework include validation, auto-masking, utf-8-handling, auto-tabindices, error display,
 * and intelligent html-preformat to easily style later, unified result and value-handling, xhtml-rendering and much more.
 * 
 * Additionally HtmlForm also provides all its validation capabilites as on the fly functionality based on jQuery, individually
 * configurable for each element.
 * 
 * For an extensive example see the index.php included in the full package.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.99 beta
 * @package form
 */

class HtmlForm{
	
	/**
	 * css-class for HtmlForm-forms
	 * @var String
	 */
	const FORMCLASS = 'htmlform';
	
	/**
	 * css-class for HtmlForm-cells
	 * @var String
	 */
	const CELLCLASS = 'htmlform_cell';
	
	/**
	 * css-class for the form headline
	 * @var String
	 */
	const HEADLINECLASS = 'htmlform_formheadline';
	
	/**
	 * css-class for the form explanation
	 * @var String
	 */
	const EXPLANATIONCLASS = 'htmlform_formexplanation';
	
	/**
	 * css-class for form messages
	 * @var String
	 */
	const MESSAGESCLASS = 'htmlform_messages_div';
	
	/**
	 * css-class for title of form messages
	 * @var String
	 */
	const MESSAGESTITLECLASS = 'htmlform_messages_title_div';
	
	/**
	 * css-class for float-breaking construct used between widgets of a form
	 * @var String
	 */
	const FLOATBREAKCLASS = 'htmlform_floatbreak';
	
	
	
	// ***
	private $packagePath;
	
	private $xhtml;
	
	private $isValid;
	private $language;
	private $usesExternalFormDeclaration;
	
	private $id;
	private $action;
	private $method;
	private $charset;
	private $enctype;
	private $cssClasses;
	private $tabIndex;
	private $cells;
	private $headline;
	private $explanation;
	private $messagesTitle;
	private $showMessages;
	private $onlyCustomMessages;
	private $errorsMarkOnlyWidget;
	
	private $prepareJsEnvironment;
	private $suppressJavascriptValidation;
	private $suppressJqueryInclude;
	
	private function __construct($id){
		$this->packagePath = '';
	
		$this->xhtml = false;
		
		$this->isValid = true;
		$this->language = 'english';
		$this->usesExternalFormDeclaration = false;
		
		$this->id = "$id";
		$this->action = '';
		$this->method = 'post';
		$this->charset = 'UTF-8';
		$this->enctype = '';
		$this->cssClasses = '';
		$this->tabIndex = 1;
		$this->cells = array(0 => array());
		$this->headline = '';
		$this->explanation = '';
		$this->messagesTitle = '';
		$this->showMessages = false;
		$this->onlyCustomMessages = false;
		$this->errorsMarkOnlyWidget = false;
		
		$this->prepareJsEnvironment = false;
		$this->suppressJavascriptValidation = false;
		$this->suppressJqueryInclude = false;
		
		$this->addElement(
			InputHidden::get($this->id.'_sent')
				->setValue('true')
		);
		
		return $this;
	}
	
	
	
	/**
	 * Factory method for HtmlForms, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $id html-id for the form
	 * @return HtmlForm new HtmlForm-instance
	 */
	static public function get($id){
		$res = new HtmlForm($id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the path to HtmlForm-package relative to the executing php-file to make usage of package assets such
	 * as javascripts and images possible (needed for special widgets such as datetime picker)
	 * 
	 * @param String $packagePath relative path to HtmlForm-package
	 * @return HtmlForm method owner 
	 */
	public function setPackagePath($packagePath){
		// regEx removes all preceding and following slashes from the path
		$this->packagePath = HtmlFormTools::auto_preg_replace('/^\/+([^\/]+.*[^\/]+)\/+$/', '$1', $packagePath);
		return $this;
	}
	
	
	
	/**
	 * Sets xhtml-rendering-mode for the form. Form renders as html4 per default.
	 * 
	 * @return HtmlForm method owner
	 */
	public function setXhtml(){
		$this->xhtml = true;
		return $this;
	}
	
	
	
	/**
	 * Sets the language to use in error messages. Form uses corresponding dictionary in "./messages".
	 * Add new ones to enable other values aside from "english" and "german"
	 * 
	 * @param String $language the name of the language to use
	 * @return HtmlForm method owner
	 */
	public function setLanguage($language){
		$this->language = $language;
		return $this;
	}
	
	
	
	/**
	 * Tells the form to use an external form declaration (first around the rendered code) and not to render an own.
	 * Useful if the form needs to be integrated into a bigger one or if special setting in the form tag are needed, which
	 * cannot be provided by the existing methods.
	 * 
	 * @return HtmlForm method owner
	 */
	public function useExternalFormDeclaration(){
		$this->usesExternalFormDeclaration = true;
		return $this;
	}
	
	
	
	/**
	 * Set the html-action of the form.
	 * Normally an empty string to enable form-submit-cycling.
	 * 
	 * @param String $action value of the html-action-attribute
	 * @return HtmlForm method owner
	 */
	public function setAction($action){
		$this->action = "$action";
		return $this;
	}
	
	
	
	/**
	 * Tells the form to send all data as POST and sets the html-method-attribute.
	 * 
	 * @return HtmlForm method owner
	 */
	public function setMethodPost(){
		$this->method = 'post';
		return $this;
	}
	
	
	
	/**
	 * Tells the form to send all data as GET and sets the html-method-attribute.
	 * 
	 * @return HtmlForm method owner
	 */
	public function setMethodGet(){
		$this->method = 'get';
		return $this;
	}
	
	
	
	/**
	 * Tells the form to handle all data and values of the form as utf-8-encoded.
	 * This is the default setting, which should only be changed for the right reasons. Encoding does
	 * not only change the charset-attribute but changes the whole way the form handles data in encoding
	 * critical operations.
	 * 
	 * @return HtmlForm method owner
	 */
	public function setCharsetUtf8(){
		$this->charset = 'UTF-8';
		return $this;
	}
	
	
	
	/**
	 * Tells the form to explicitly use standard english encoding, without any exotic special characters.
	 * By setting this you also disable binary-safe string operations for all values.
	 * 
	 * @return HtmlForm method owner
	 */
	public function setCharsetLatin(){
		$this->charset = 'ISO-8859-1';
		return $this;
	}
	
	
	
	/**
	 * Sets the encoding type for the form. All standard values are allowed here, other values will be ignored.
	 * 
	 * Look up the specifications if you are unsure what the usual values might be.
	 * 
	 * This setting is normally necessary for enabling file-uploads by setting this to "multipart/form-data",
	 * to allow mixed normal fields and upload fields.
	 * 
	 * @param String $enctype encoding type for the form
	 * @return HtmlForm method owner
	 */
	public function setEnctype($enctype){
		if( HtmlFormTools::auto_preg_match('/^(application|audio|image|multipart|text|video)\/(\*|[a-zA-Z\-]+)$/i', $enctype) ){
			$this->enctype = $enctype;
		}
		return $this;
	}
	
	
	
	/**
	 * Sets the html-class-attribute for the form as a whole.
	 * Use exactly the same notation you would use in html.
	 * 
	 * @param String $cssClasses css-classes-string to use for the form
	 * @return HtmlForm method owner
	 */
	public function setCssClasses($cssClasses){
		$this->cssClasses = "$cssClasses";
		return $this;
	}
	
	
	
	/**
	 * Sets the tabindex of the form itself. All widgets of the form will get tabindices according to this value.
	 * By setting this value you can ensure a correct flow ob tabindices even if the form doesn't provide the first
	 * input on the page.
	 * 
	 * @param uint $tabIndex tabindex of the form itself
	 * @return HtmlForm method owner
	 */
	public function setTabIndex($tabIndex){
		$this->tabIndex = (integer) $tabIndex;
		return $this;
	}
	
	
	
	/**
	 * Sets the amount of cells to create for the form. Each cell represents a logical container for parts of the form.
	 * This can serve the purpose of applying different styles to each cell for example, or to group widgets for easier
	 * javascript-manipulation.
	 * 
	 * This method doesn't recreate all cells but expands or contracts existing ones.
	 *
	 * @param uint $cellCount amount of cells to create
	 * @return HtmlForm method owner
	 */
	public function setCells($cellCount){
		if( count($this->cells) < $cellCount ){
			for( $i = count($this->cells); $i < (integer) $cellCount; $i++ ){
				$this->cells[] = array();
			}
		} elseif( count($this->cells) > $cellCount ){
			for( $i = (integer) $cellCount; $i < count($this->cells); $i++ ){
				unset($this->cells[$i]);
			}
		}
		
		return $this;
	}
	
	
	
	/**
	 * Sets a headline for the form, which is put in a fitting container on top of the form.
	 * This is a convenience method, which also allows easier adaptation of the html-view for different forms,
	 * that can be displayed at the same spot.
	 * 
	 * @param String $headline headline for the form
	 * @return HtmlForm method owner
	 */
	public function setHeadline($headline){
		$this->headline = "$headline";
		return $this;
	}
	
	
	
	/**
	 * Sets an explanation text for the form, which in put in a fitting container on top of the form, below the headline (if one
	 * is defined).
	 * This is a convenience method, which also allows easier adaptation of the html-view for different forms,
	 * that can be displayed at the same spot.
	 * 
	 * @param unknown_type $explanation
	 * @return HtmlForm method owner
	 */
	public function setExplanation($explanation){
		$this->explanation = "$explanation";
		return $this;
	}
	
	
	
	/**
	 * Defines if standard error messages should be displayed above the form or not.
	 * Standard error messages are taken out of the "./messages"-dictionaries and give detailed information about failed validators on form widgets.
	 * If these messages are activated, you will get a very verbose error ouptut.
	 * 
	 * @param String $title title text above current error messages
	 * @param Boolean $show show all messages yes/no
	 * @return HtmlForm method owner
	 */
	public function showMessages($title = '', $show = true){
		$this->messagesTitle = "$title";
		$this->showMessages = $show;
		$this->onlyCustomMessages = false;
		return $this;
	}
	
	
	
	/**
	 * Defines if custom error messages should be displayed above the form or not.
	 * Custom error messages are defined by the developer by defining error messages specifically for certain validators, or by
	 * setting the standard messages of a certain widget to be treated as custom.
	 * If these messages are activated you will get a selective and individual error output, which has to
	 * be defined manually.
	 * 
	 * @param String $title title text above current error messages
	 * @param Boolean $show show custom messages yes/no
	 * @return HtmlForm method owner
	 */
	public function showCustomMessages($title = '', $show = true){
		$this->messagesTitle = "$title";
		$this->showMessages = $show;
		$this->onlyCustomMessages = true;
		return $this;
	}
	
	
	
	/**
	 * Sets the error marking in the form to reduced highlighting.
	 * Normally the whole widget would be marked, including label, container and everything. The reduced
	 * mode only marks the inputs themselves, leaving the surroundings out. This ist especially useful
	 * if your form is rather compact and you don't want to mark half the form area for every error.
	 * 
	 * @return HtmlForm method owner
	 */
	public function useReducedErrorMarking(){
		$this->errorsMarkOnlyWidget = true;
		return $this;
	}
	
	
	
	/**
	 * Tells the form to prepare everything concerning Javascript to use the jQuery-based
	 * Javascript-validation for form elements with activated JS-validation.
	 * 
	 * At least one form per page should do this, to be able to use JS-validation. You can leave this
	 * out for additional forms, but leaving it won't hurt dramatically either.
	 * 
	 * The method includes an own version of jQuery, which is protected by an own namespace and a
	 * JS-environment, docked to a HTMLFORM global-JS-variable.
	 * 
	 * @return HtmlForm method owner
	 */
	public function prepareJavascriptValidation(){
		$this->prepareJsEnvironment = true;
		return $this;
	}
	
	
	
	/**
	 * Deactivates the execution of all JS-validation, no matter if any elements have this activated, or the form was
	 * told to prepare for it. No JS-environment will be printed and no validation code for elements will be printed.
	 * 
	 * @return HtmlForm method owner
	 */
	public function suppressJavascriptValidation(){
		$this->suppressJavascriptValidation = true;
		return $this;
	}
	
	
	
	/**
	 * Suppresses the inclusion of HtmlForm's own jQuery version and thereby sets the forms to rely on an already
	 * included, compatible, external version. HtmlForm relies on $ being present in that case.
	 * 
	 * Use this method to prevent double includes for more or less identical jQuery-versions, which may already
	 * be part of the site.
	 * 
	 * @return HtmlForm method owner
	 */
	public function suppressJqueryInclude(){
		$this->suppressJqueryInclude = true;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the set package path.
	 * 
	 * @see setPackagePath()
	 * @return String current package path
	 */
	public function getPackagePath(){
		return $this->packagePath;
	}
	
	
	
	/**
	 * Returns the set html-id of the form.
	 * 
	 * @return String current form id
	 */
	public function getId(){
		return $this->id;
	}
	
	
	
	/**
	 * Returns the set form method.
	 * Also able to return the method as php-array, for direct use.
	 * 
	 * @param Boolean $returnAsArray defines if the result should be the php-method-array itself
	 * @return String/Array[String] either the current method as a string (default) or the fitting php-method-array
	 */
	public function getMethod($returnAsArray = false){
		if( !$returnAsArray ){
			return $this->method;
		} else {
			switch( $this->method ){
				case 'get':
					return $_GET;
				break;
				
				case 'post':
				default:
					return $_POST;
				break;
			}
		}
	}
	
	
	
	/**
	 * Returns the set tabindex for the form.
	 * 
	 * @return uint current tabindex of the form
	 */
	public function getTabIndex(){
		return $this->tabIndex;
	}
	
	
	
	/**
	 * Returns the set language of the form.
	 * 
	 * @return String name of current form language 
	 */
	public function getLanguage(){
		return $this->language;
	}
	
	
	
	/**
	 * Returns any form element added to the form by searching for it's name.
	 * This search is strictly for names not ids. If an element needs to be searchable
	 * just give it a name by using {@link FormElement::setName() setName} if it doesn't get
	 * one by default.
	 * 
	 * @param String $name the name to search for
	 * @param Boolean $multiple defines if more than one element should be returned, or just the first
	 * @return FormElement/Array[FormElement] the found element(s)
	 */
	public function getElementByName($name, $multiple = false){
		$res = array();
		
		foreach( $this->cells as $cell ){
			foreach( $cell as $element ){
				if( $element->getName() == "$name" ){
					$res[] = $element;
				}
				
				$subElements = $element->getSubElements();
				if( count($subElements) > 0 ) {
					foreach( $subElements as $subElement ){
						if( $subElement->getName() == "$name" ){
							$res[] = $subElement;
						}
					}
				}
				
				// brutal dirty break to cut search time
				if( !$multiple && (count($res) > 0) ){
					break; break;
				}
			}
		}
		
		if( count($res) == 0 ){
			$res = null;
		} else {
			$res = $multiple ? $res : $res[0];
		}
		
		return $res;
	}
	
	
	
	/**
	 * Returns the complete valueset of a form.
	 * A valueset gathers all contained values of a form in one consistent object, that
	 * can be much more easily parsed than dealing with every input-characteristic manually.
	 * Missing values are null, everything else is a string or array of strings.
	 * 
	 * @see FormValueSet
	 * @param FormValueSet $addedSet a valueset to start on
	 * @return FormValueSet the complete current valueset of the form
	 */
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
	
	/**
	 * Answers if the form is currently in a valid state.
	 * This value is always based on the currently active validators of the form
	 * widgets. If no validators exist, the form ist always valid.
	 * 
	 * @return Boolean yes/no answer
	 */
	public function isValid(){
		return $this->isValid;
	}
	
	
	
	/**
	 * Answers if the form is currently using utf-8 for value encoding.
	 * 
	 * @see setCharsetUtf8()
	 * @return Boolean yes/no answer
	 */
	public function usesUtf8(){
		return $this->charset == 'UTF-8';
	}
	
	
	
	/**
	 * Answers if the form is currently set to use an external form declaration instead
	 * of an own one.
	 * 
	 * @see useExternalFormDeclaration()
	 * @return Boolean yes/no answer
	 */
	public function usesExternalFormDeclaration(){
		return $this->usesExternalFormDeclaration;
	}
	
	
	
	/**
	 * Answers if the form is currently set to only use reduced error marking
	 * for occuring validation errors.
	 * 
	 * @see useReducedErrorMarking()
	 * @return Boolean yes/no answer
	 */
	public function usesReducedErrorMarking(){
		return $this->errorsMarkOnlyWidget;
	}
	
	
	
	/**
	 * Answers if the form has already been sent or not.
	 * This is a relative information since the method searches for fitting GET- or POST-information. If
	 * you changed the action or created data under the name of the form-id you may interrupt this
	 * mechanism.
	 * 
	 * @return Boolean yes/no answer
	 */
	public function hasBeenSent(){
		return (
			(($this->method == 'get') && isset($_GET[$this->id.'_sent'])) 
			|| (($this->method == 'post') && isset($_POST[$this->id.'_sent']))
		);
	}
	
	
	
	/**
	 * Answers if the form actively suppresses possibly set JS-validation in it.
	 * 
	 * @return Boolean yes/no answer
	 */
	public function javascriptValidationIsSuppressed(){
		return $this->suppressJavascriptValidation;
	}
	
	
	
	//---|functionality----------
	
	/**
	 * Starts the form-validation for every contained widget and calculates the validity for every value leading
	 * to the overall validity for the form as a whole.
	 * 
	 * @return Boolean form is valid yes/no
	 */
	public function validate(){
		foreach( $this->cells as $cell ){
			foreach( $cell as $element ){
				$elEval = $element->validate();
				$this->isValid = $this->isValid && $elEval;
			}
		}

		return $this->isValid;
	}
	
	
	
	/**
	 * Adds one or many css-classes to the forms class-attribute.
	 * This is simple string concatenation. You don't have to leave a blank at the start
	 * of the string to add.
	 * 
	 * @param String $cssClasses the classes-string to add to the current one
	 * @return HtmlForm method owner
	 */
	public function addCssClasses($cssClasses){
		$this->cssClasses .= ($this->cssClasses == '') ? "$cssClasses" : " $cssClasses";
		return $this;
	}
	
	
	
	/**
	 * Adds an element to the form.
	 * You can never add directly to the form, but will automatically add to a cell, which
	 * a form always has a minimum of one of. Before adding elements to additional cells, be sure
	 * to add or define them beforehand.
	 * 
	 * @param FormElement $element the element to add to the target cell
	 * @param uint $cell the index of the cell in which to add the element, starts with 1 (default value as well)
	 * @return HtmlForm method owner
	 */
	public function addElement(FormElement $element, $cell = 1){
		if( is_array($this->cells[($cell - 1)]) ){
			$element->setMasterForm($this);
			$this->cells[($cell - 1)][] = $element;
		}
		
		return $this;
	}
	
	
	
	/**
	 * Adds an element directly after another one in the form.
	 * The target element is identified by name alone. Terminates after the first insert.
	 * 
	 * @param String $targetElementName name of the element to insert after
	 * @param FormElement $element the element to insert
	 * @return HtmlForm method owner
	 */
	public function insertElementAfter($targetElementName, FormElement $element){
		foreach( $this->cells as $cellIndex => $cell ){
			foreach( $cell as $elementIndex => $oldElement ){
				if( $oldElement->getName() == "$targetElementName" ){
					$element->setMasterElement($this);
					$element->setMasterForm($this);
					$this->cells[$cellIndex] = HtmlFormTools::array_insert($this->cells[$cellIndex], ($elementIndex + 1), $element);
					break;break;
				}
				
				$subElementNum = count($oldElement->getSubElements());
				$oldElement->insertElementAfter($targetElementName, $element);
				if( $subElementNum < count($oldElement->getSubElements()) ){
					break;break;
				}
			}
		}
		
		return $this;
	}
	
	
	
	/**
	 * Increases the internal tabindex-counter of the form by one.
	 * Should not be called externally. This is mostly a helper method for the form widgets.
	 * 
	 * @return HtmlForm method owner
	 */
	public function incTabIndex(){
		$this->tabIndex++;
		return $this;
	}
	
	
	
	/**
	 * Adds a new cell to the form.
	 * 
	 * @see setCells()
	 * @return HtmlForm method owner
	 */
	public function addCell(){
		$this->cells[] = array();
		return $this;
	}
	
	
	
	//---|output----------
	
	private function printCssClasses(){
		return ' class="'.self::FORMCLASS.(($this->cssClasses != '') ? ' '.$this->cssClasses : '').'"';
	}
	
	
	
	private function printEnctype(){
		return ($this->enctype != '') ? ' enctype="'.$this->enctype.'"' : '';
	}
	
	
	
	/**
	 * Returns a the character to attach to the xhtml-presentation of tags.
	 * Should not be called externally. For internal use by the form-widgets.
	 * 
	 * @return String xhtml-slash-character
	 */
	public function printSlash(){
		return ($this->xhtml ? '/' : '');
	}
	
	
	
	/**
	 * Returns the html-code used by widgets to mark the end of a supposed row in a form.
	 * This fragment is meant to break any occured floating in the row, to prevent displaying several logical rows
	 * in the same optical row. Simply spoken: This brutally ends a row.
	 * 
	 * For internal use by form-widgets.
	 * 
	 * @return String html-fragment to end a form-row
	 */
	public function printFloatBreak(){
		return '<div class="'.self::FLOATBREAKCLASS.'" style="clear:both; height:0px; margin:0px; padding:0px; font-size:0px;">&nbsp;</div>';
	}
	
	
	
	private function printHeadline(){
			return (($this->headline != '') ? ' <div id="'.$this->id.'_formheadline" class="'.self::HEADLINECLASS.'">'.$this->headline.'</div>' : '');
	}
	
	
	
	private function printExplanation(){
			return (($this->explanation != '') ? ' <div id="'.$this->id.'_formexplanation" class="'.self::EXPLANATIONCLASS.'">'.$this->explanation.'</div>' : '');
	}
	
	
	
	private function printMessages(){
		$msg = '';
		
		if( $this->showMessages ){
			foreach( $this->cells as $cell ){
				foreach( $cell as $element ){
					$msg .= $element->printMessages($this->onlyCustomMessages);
				}
			}
			
			$title = ($this->messagesTitle != '') ? '<div class="'.self::MESSAGESTITLECLASS.'">'.$this->messagesTitle.'</div>' : '';
			
			return 
				 '<div class="'.self::MESSAGESCLASS.'" style="display:'.(($this->hasBeenSent() && ($msg != '')) ? 'block' : 'none').';">'
					.$title
					.$msg
					.$this->printFloatBreak()
				.'</div>'
			;
		} else {
			return $msg;
		}
	}
	
	
	
	private function printFormDeclaration($formContent){
		return
			$this->usesExternalFormDeclaration
			? $formContent
			: (
				 '<form id="'.$this->id.'" '.(($this->action != '') ? 'action="'.$this->action.'" ' : '').'method="'.$this->method.'" accept-charset="'.$this->charset.'"'.$this->printEnctype().$this->printCssClasses().'>'
					.$formContent
				.'</form>'
			)
		;
	}
	
	
	
	private function printJsEnvironment(){
		if( $this->prepareJsEnvironment && !$this->suppressJavascriptValidation ){
			$jsEnvironment = '';
			
			// own jQuery include
			if( !$this->suppressJqueryInclude ){
				$jsEnvironment .= "
					<script type=\"text/javascript\">
						".file_get_contents('js/jquery.min.js')."
						var \$htmlform = jQuery.noConflict();
					</script>
				";
			}
			
			// HtmlForm's own JS-environment
			$jsEnvironment .= "
				<script type=\"text/javascript\">
					if( window.HTMLFORM === undefined ){
						window['HTMLFORM'] = {
							jquery : (window.\$htmlform !== undefined) ? window.\$htmlform : ((window.\$ !== undefined) ? window.\$ : null),
							data : {
								getAsObj : function(\$form){
									var fields = \$form.serializeArray();
									var targetObj = {};
									var currentFieldIsArray = false;
									
									for( var i = 0; i < fields.length; i++ ){
										currentFieldIsArray = false;
										if( fields[i].name.indexOf('[]') != -1 ){
											fields[i].name = fields[i].name.slice(0, fields[i].name.indexOf('[]'));
											currentFieldIsArray = true;
										}
										
										if( targetObj[fields[i].name] === undefined ){
											if( !currentFieldIsArray ){
												targetObj[fields[i].name] = fields[i].value;
											} else {
												targetObj[fields[i].name] = [fields[i].value];
											}
										} else if( !HTMLFORM.jquery.isArray(targetObj[fields[i].name]) ){
											targetObj[fields[i].name] = [targetObj[fields[i].name], fields[i].value];
										} else {
											targetObj[fields[i].name].push(fields[i].value);
										}
									}
									
									return targetObj;
								}
							},
							validation : {
								markError : function(\$widgets, res){
									\$widgets.each(function(){
										if( !res && !HTMLFORM.jquery(this).hasClass('".FormElement::ERRORCLASS."') ){
											HTMLFORM.jquery(this).addClass('".FormElement::ERRORCLASS."');
										} else if( res ){
											HTMLFORM.jquery(this).removeClass('".FormElement::ERRORCLASS."');
										}
									
										HTMLFORM.jquery(this).closest('.".FormElement::WIDGETCLASS."').each(function(){
											if( !res && ".($this->errorsMarkOnlyWidget ? 'false' : 'true')." ){
												if( !HTMLFORM.jquery(this).hasClass('".FormElement::ERRORCLASS."') ){
													HTMLFORM.jquery(this).addClass('".FormElement::ERRORCLASS."');
												}
											} else if( res ){
												HTMLFORM.jquery(this).removeClass('".FormElement::ERRORCLASS."');
											}
										});
									});
								},
								handleErrorMessage : function(id, msg){
									HTMLFORM.jquery('.".self::MESSAGESCLASS."')
										.append('<div class=\"".FormValidator::MESSAGECLASS." msg_'+id+'\">'+msg+'<\/div>')
										.show()
									;
								},
								removeErrorMessages : function(id){ 
									HTMLFORM.jquery('.".self::MESSAGESCLASS."'+(id ? ' > .msg_'+id : '')).remove();
									
									if( HTMLFORM.jquery('.".self::MESSAGESCLASS." > .".FormValidator::MESSAGECLASS."').length == 0 ){
										HTMLFORM.jquery('.".self::MESSAGESCLASS."').hide();
									}
								}
							}
						};
					}
				</script>
			";
			
			return $jsEnvironment;
		} else {
			return '';
		}
	}
	
	
	
	/**
	 * Compiles the whole form for output and returns the finished html-code-fragment.
	 * Insert the result of this function into your page to use the form.
	 * 
	 * @return String html-code for the form
	 */
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
			 $this->printJsEnvironment()
			.$this->printHeadline()
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