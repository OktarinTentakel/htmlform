<?php

//--|includes---------

require_once 'HtmlFormTools.class.php';



//--|class----------

/**
 * Abstract parent class for all widgets / elements contained in a form-object.
 * 
 * Provides all basic functionality concerning traversing, structure-fit-in, basic html-attributes, validation
 * to inheriting classes. Being a specialization of this class means being a barebone-widget with everything present,
 * but specific functionality. If you want to write a new widget, be sure to inherit from this class or a already
 * present widget.
 * 
 * This class also handles creation of error-messages and such side-tasks as auto-tabindices. In general this class handles all
 * the annoying bits for the elements.
 * 
 * This class also holds standard print-methods for later rendering of the specialized widget. Look up an example in the present
 * elements and do use them!
 * 
 * @author Sebastian Schlapkohl
 * @version 0.99 beta
 * @package formelements
 */

abstract class FormElement{
	
	/**
	 * css-class for the row-div each form element is delivered in
	 * @var String
	 */
	const WRAPCLASS = 'htmlform_row_div';
	
	/**
	 * css-class for the container that includes everything directly related to the element itself
	 * @var String
	 */
	const WIDGETCLASS = 'htmlform_widget_div';
	
	/**
	 * css-class for marking an element as invalid as specified by it's validator
	 * @var String
	 */
	const ERRORCLASS = 'htmlform_error';
	
	
	
	// ***	
	/**
	 * The form to which the element belongs.
	 * @var HtmlForm
	 */
	protected $masterForm;
	
	/**
	 * The element into which the element has been inserted, if this was the case.
	 * @var FormElement
	 */
	protected $masterElement;
	
	
	/**
	 * The validator to validate the values of the element according to its rules.
	 * @var FormValidator
	 */
	protected $validator;
	
	/**
	 * The validity status of the element.
	 * @var Boolean
	 */
	protected $isValid;
	
	
	/**
	 * The html-id of the element.
	 * @var String
	 */
	protected $id;
	
	/**
	 * The html-name of the element.
	 * @var String
	 */
	protected $name;
	
	/**
	 * The html-title of the element.
	 * @var String
	 */
	protected $title;
	
	/**
	 * The html-class-string of the element.
	 * @var String
	 */
	protected $cssClasses;
	
	/**
	 * The text of the element label.
	 * @var String
	 */
	protected $label;
	
	/**
	 * Complete attribute string for a standard javascript-tag-eventhandler such as "onclick".
	 * @var String
	 */
	protected $jsEventHandler;
	
	/**
	 * The subelements of the element if the element has container character.
	 * @var Array[FormElement]
	 */
	protected $subElements;
	
	/**
	 * The html-enabled-state of the element.
	 * @var Boolean
	 */
	protected $disabled;
	
	/**
	 * General abstract, hidden formelement constructor.
	 * Initializes basic form values. Must be called at the beginning of
	 * specialized element constructors.
	 * 
	 * @param String $name internal name of the element (also html-name if one is needed)
	 * @param String $id html-id of the element
	 */
	protected function __construct($name, $id = ''){
		$this->masterForm = null;
		$this->masterElement = null;
		
		$this->validator = null;
		$this->isValid = true;
		
		$this->id = "$id";
		$this->name = "$name";
		$this->title = '';
		$this->cssClasses = '';
		$this->label = '';
		$this->jsEventHandler = '';
		$this->subElements = null;
		$this->disabled = false;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets the owning form for an element and by doing so inserts the element into
	 * the logical structure of the form.
	 * For internal use. Use form->addElement() instead.
	 * 
	 * @param HtmlForm $master the form this element belongs to
	 * @return FormElement method owner
	 */
	public function setMasterForm(HtmlForm $master){
		$this->masterForm = $master;
		foreach( $this->getSubElements() as $se ){
			$se->setMasterForm($master);
		}
		return $this;
	}
	
	
	
	/**
	 * Sets the owning element for an element.
	 * Some elements can contain others, allowing a tree structure of
	 * the form object. Every element eventually knows the form it is in, but additionally knows which element
	 * contains it, if it is not directly put into the form. For internal use. Use element->addElement() instead.
	 * 
	 * @param FormElement $master the formelement to set as owner of the element
	 * @return FormElement method owner
	 */
	public function setMasterElement(FormElement $master){
		$this->masterElement = $master;
		return $this;
	}
	
	
	
	/**
	 * Sets a validator for a value-bearing element.
	 * By doing so you render the element invalid by default. To get it valid again you have to validate the form
	 * it is in or the element itself. A validator can be set up with a set of rules for the value of the element.
	 * If one of those rules is broken the element is treated as being invalid.
	 * 
	 * @see FormValidator
	 * @param FormValidator $validator the validator holding the rules to validate the elements value(s)
	 * @return FormElement method owner
	 */
	public function setValidator(FormValidator $validator){
		if( $this->label != '' ) $validator->setFieldName($this->label);
		if( $this->name != '' ) $validator->setDataName($this->name);
		$this->validator = $validator;
		$this->isValid = false;
		return $this;
	}
	
	
	
	/**
	 * Sets the html-id for this element.
	 * 
	 * @param String $id html-id to use for this element
	 * @return FormElement method owner
	 */
	public function setId($id){
		$this->id = "$id";
		return $this;
	}
	
	
	
	/**
	 * Sets the html-title for this element.
	 * 
	 * @param String $title html-title to use for this element
	 * @return FormElement method owner
	 */
	public function setTitle($title){
		$this->title = "$title";
		return $this;
	}
	
	
	
	/**
	 * Sets the html-class-attribute for the element.
	 * Use exactly the same notation you would use in html.
	 * 
	 * @param String $cssClasses css-classes-string to use for the element
	 * @return FormElement method owner
	 */
	public function setCssClasses($cssClasses){
		$this->cssClasses = "$cssClasses";
		return $this;
	}
	
	
	
	/**
	 * Inserts a label for the element.
	 * This is not a label in the html-sense, but to be seen as a description for the purpose of the element.
	 * The label is rendered into an own tag before the widget. 
	 * 
	 * @see Label
	 * @param string $label text to display in the element-label
	 * @return FormElement method owner
	 */
	public function setLabel($label){
		$this->label = "$label";
		return $this;
	}
	
	
	
	/**
	 * Sets a javascript-handler for the element.
	 * If you want to use such things as "onchange" or "onclick", you can set that with this method.
	 * At the moment only one handler per element is allowed, because this is supposed to be
	 * a comfortable way of inserting little quirks and not a programming interface.
	 * If you need to do complicated things, use raw javascript or jquery and reference the element by a
	 * fitting selector.
	 * 
	 * @param String $handler the handler attribute
	 * @param String $reaction the javascript-code to execute if handler fires
	 * @return FormElement method owner
	 */
	public function setJavascriptEventHandler($handler, $reaction){
		$this->jsEventHandler = $handler.'="'.$reaction.'"';
		return $this;
	}
	
	
	
	/**
	 * Set the element disabled.
	 * 
	 * @return FormElement method owner
	 */
	public function setDisabled(){
		$this->disabled = true;
		return $this;
	}
	
	
	
	/**
	 * Set the element usables or disabled based on an expression.
	 * 
	 * @param Boolean $expression boolean expression to determine if element is usable
	 * @return FormElement method owner
	 */
	public function setUsable($expression){
		$this->disabled = !$expression;
		return $this;
	}
	
	
	
	//---|getter----------
	
	/**
	 * Returns the currently connected owner form for the element.
	 * 
	 * @return HtmlForm the element's owner form or null
	 */
	public function getMasterForm(){
		return $this->masterForm;
	}
	
	
	
	/**
	 * Return the currently connected owner element for the element.
	 * 
	 * @return FormElement the element's owner element or null
	 */
	public function getMasterElement(){
		return $this->masterElement;
	}
	
	
	
	/**
	 * Return the currently connected validator for the element.
	 * 
	 * @return FormValidator the element's validator or null
	 */
	public function getValidator(){
		return $this->validator;
	}
	
	
	
	/**
	 * Return the element's html-id.
	 * 
	 * @return String the element's html-id
	 */
	public function getId(){
		return $this->id;
	}
	
	
	
	/**
	 * Return the element's html-name.
	 * 
	 * @return String the element's html-name
	 */
	public function getName(){
		return $this->name;
	}
	
	
	
	/**
	 * Return the element's html-title.
	 * 
	 * @return String the element's html-title
	 */
	public function getTitle(){
		return $this->title;
	}
	
	
	
	/**
	 * Return the element's label text.
	 * 
	 * @return String the element's label text
	 */
	public function getLabel(){
		return $this->label;
	}
	
	
	
	/**
	 * Return all owned elements of this element.
	 * 
	 * @return Array[FormElement] all elements owned by this element
	 */
	public function getSubElements(){
		return is_array($this->subElements) ? $this->subElements : array();
	}
	
	
	
	/**
	 * Returns the compiled valueset for this element including the elements owned by this one. 
	 * 
	 * @see FormValueSet
	 * @param FormValueSet $addedSet a valueset to start with instead of an empty one
	 * @return FormValueSet the comiled valueset of this element and its descendants
	 */
	public function getValueSet(FormValueSet $addedSet = null){
		$valueSet = is_null($addedSet) ? new FormValueSet() : $addedSet;
		
		if( !is_null($this->getValue()) ){
			$valueSet->setValue($this->name, $this->getValue());
		}
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$valueSet = $element->getValueSet($valueSet);
			}
		}
		
		return $valueSet;
	}
	
	
	
	// ###
	/**
	 * Returns the value of the element if there is any.
	 * Has to be implemented by all form elements.
	 * 
	 * @return null/String/Array[String] the value the element provides in the implementation
	 */
	abstract public function getValue();
	// ###
	
	
	
	//---|questions----------
	
	/**
	 * Answers if the element is in a valid state at the moment.
	 * Per default an element is always valid, if a validator is added it can't be valid
	 * until it has been validated according to the rules of the validator.
	 * 
	 * @return Boolean yes/no answer
	 */
	public function isValid(){
		return $this->isValid;
	}
	
	
	
	/**
	 * Answers if the element needs to be treated with precautions according to possible utf-8 values.
	 * This value is completely derived from the masterform if present, otherwise the value will be treated as
	 * utf-8 for security reasons.
	 * 
	 * @return Boolean yes/no answer
	 */
	protected function needsUtf8Safety(){
		return is_null($this->masterForm) ? true : $this->masterForm->usesUtf8();
	}
	
	
	
	//---|functionality----------
	
	// >>>
	/**
	 * Starts validation for the element and all subelements according to the rules laid out in the element's validator.
	 * Calculates the compiled validity-state for the element and all descendants recursively. After this call,
	 * isValid() always returns the correct calculated state.
	 * 
	 * If element has no masterform at the moment of call the validators work on default settings.
	 * 
	 * This method must be overwritten in specialized classes and called at the beginning of the overwriting method. 
	 * 
	 * @return Boolean element is valid yes/no
	 */
	public function validate(){
		if( !is_null($this->validator) && !is_null($this->masterForm) ){
			$this->validator->setMessageLanguage($this->masterForm->getLanguage());
			$this->validator->setUtf8Safety($this->masterForm->usesUtf8());
		}
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$elEval = $element->validate();
				$this->isValid = $this->isValid && $elEval;
			}
		}
		
		return $this->isValid;
	}
	// >>>
	
	
	
	/**
	 * Adds one or many css-classes to the element's class-attribute.
	 * This is simple string concatenation. You don't have to leave a blank at the start
	 * of the string to add.
	 * 
	 * @param String $cssClasses the classes-string to add to the current one
	 * @return FormElement method owner
	 */
	public function addCssClasses($cssClasses){
		$this->cssClasses .= ($this->cssClasses == '') ? "$cssClasses" : " $cssClasses";
		return $this;
	}
	
	
	
	/**
	 * Adds a subelement to the element.
	 * Is available but won't do anything for elements that have no container character.
	 * 
	 * @param FormElement $element the element to add to the subelements
	 * @return FormElement method owner
	 */
	public function addElement(FormElement $element){
		if( is_array($this->subElements) ){
			$element->setMasterElement($this);
			$this->subElements[] = $element;
		}
		return $this;
	}
	
	
	
	/**
	 * Searches the subelements of the element if present and inserts a given element after the first found one.
	 * This method doesn't add an element right behind the element itself, but inserts one into the subelements
	 * if a match is found.
	 * 
	 * This method is available, but doesn't do anything for elements that have no container character.
	 * 
	 * @param String $targetElementName the name of the subelement to search for
	 * @param FormElement $element the element to insert into the subelements behind the found one
	 * @return FormElement method owner
	 */
	public function insertElementAfter($targetElementName, FormElement $element){
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $index => $oldElement ){
				if( $oldElement->getName() == "$targetElementName" ){
					$element->setMasterElement($this);
					$element->setMasterForm($this->masterForm);
					$this->subElements = HtmlFormTools::array_insert($this->subElements, ($index + 1), $element);
					break;
				}
			}
		}
		
		return $this;
	}
	
	
	
	/**
	 * Returns the supposed source of refill values.
	 * Either the provided array will be taken, or the method tries to determine the element's data source
	 * by the method of the masterform. If that fails the refiller defaults to $_POST.
	 * For internal use. Doesn't need to be overwritten or extended.
	 * 
	 * @param Array[String]|null $refiller predetermined refill source for element
	 * @return Array[String] refill source for element
	 */
	protected function determineRefiller($refiller = array()){
		if( empty($refiller) ){
			if( !is_null($this->masterForm) ){
				$refiller = $this->masterForm->getMethod(true);
			} else {
				$refiller = $_POST;
			}
		}
		
		return $refiller;
	}
	
	
	
	//---|output----------
	
	/**
	 * Compiles the html-id-attribute-string of the element.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printId(){
		return (($this->id != '') ? ' id="'.$this->id.'"' : '');
	}
	
	
	
	/**
	 * Compiles the html-name-string of the element.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printName(){
		return (($this->name != '') ? ' name="'.$this->name.'"' : '');
	}
	
	
	
	/**
	 * Comiles the html-name-string of the element, if the element is part of a group of values.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printNameArray(){
		return (($this->name != '') ? ' name="'.$this->name.'[]"' : '');
	}
	
	
	
	/**
	 * Compiles the html-title-string of the element.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printTitle(){
		return (($this->title != '') ? ' title="'.$this->title.'"' : '');
	}
	
	
	
	/**
	 * Comiles the html-class-attribute-string of the element.
	 * Auto-includes an error-class if element is found invalid.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printCssClasses(){
		if( 
			!$this->isValid 
			&& $this->masterForm->usesReducedErrorMarking() 
			&& $this->masterForm->hasBeenSent()
			&& !is_null($this->getValue())
		){
			$this->cssClasses .= ($this->cssClasses == '') ? self::ERRORCLASS : ' '.self::ERRORCLASS;
		}
		
		return (($this->cssClasses != '') ? ' class="'.$this->cssClasses.'"' : '');
	}
	
	
	
	/**
	 * Compiles a string of all classes for the tag-wrapper for the element's html-code.
	 * Auto-includes an error-class if element is found invalid and error marking is set to full.
	 * 
	 * @return String compiled wrapper classes
	 */
	protected function printWrapperClasses(){
		return
			self::WRAPCLASS
			.(
				(!$this->isValid 
				 && !$this->masterForm->usesReducedErrorMarking()
				 && $this->masterForm->hasBeenSent())
					? ' '.self::ERRORCLASS 
					: ''
			)
		;
	}
	
	
	
	/**
	 * Compiles html-javascript-eventhandler-string of the element.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printJavascriptEventHandler(){
		return (($this->jsEventHandler != '') ? ' '.$this->jsEventHandler : '');
	}
	
	
	
	/**
	 * Calculates current ongoing tabindex of the form, refreshes it and returns the proper value for the element.
	 * 
	 * @return unit tabindex of the element
	 */
	protected function printTabIndex(){
		$res = ' tabindex="'.$this->masterForm->getTabIndex().'"';
		$this->masterForm->incTabIndex();
		return $res;
	}
	
	
	
	/**
	 * Compiles the html-disabled-attribute-string of the element.
	 * 
	 * @return String compiled attribute-string
	 */
	protected function printDisabled(){
		return $this->disabled ? ' disabled="disabled"' : '';
	}
	
	
	
	/**
	 * Returns a string of all aggregated error messages of the element.
	 * Makes only sense after a validation process took place.
	 * 
	 * @param Boolean $onlyCustomMessages defines that only custom messages should be considered
	 * @return String aggregated error-message-string
	 */
	public function printMessages($onlyCustomMessages = false){
		$msg = '';
		
		if( !is_null($this->validator) ){
			$msg .= $this->validator->printMessageQueue($onlyCustomMessages);
		}
		
		if( is_array($this->subElements) ){
			foreach( $this->subElements as $element ){
				$msg .= $element->printMessages($onlyCustomMessages);
			}
		}
		
		return $msg;
	}
	
	
	
	/**
	 * Grabs the compiled JS-validation-code for the element from its validator, if present and returns
	 * the code as a string.
	 * 
	 * @return String JS-code to validate the element's values on the fly
	 */
	public function printJavascriptValidationCode(){
		if( is_null($this->masterForm) || (!is_null($this->masterForm) && !$this->masterForm->javascriptValidationIsSuppressed()) ){
			return !is_null($this->validator) ? $this->validator->printJavascriptValidationCode() : '';
		} else {
			return '';
		}
	}
	
	
	
	// ###
	/**
	 * Returns the compiled html-code for the element.
	 * Has to be implemented by specialized classes.
	 * Use the protected print-methods of this class to make life easier for you.
	 */
	abstract public function doRender();
	// ###
}

?>