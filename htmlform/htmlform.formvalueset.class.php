<?php

//---|class----------

/**
 * This class defines a container to hold gathered form-data.
 * The idea behind this is fairly simple: normally every form-widget has it's own exotic set
 * of rules concerning the representation of it's values. A group of checkboxes for example
 * not only returns nothing when not box is checked, but isn't even included in the resultset in that case.
 * Considering that a multi-select, which is merely a different representation for this, is not only inlcuded,
 * but also has a value (an empty array), this is quite bizarre.
 * 
 * A FormValueSet is supposed to streamline this, by applying the same rules to every value-bearing widget.
 * When calling a FormValueSet for a form like $valueSet->htmlnameofwidget you should get the following defined answers:
 * widget is not in form/is disabled => null
 * widget is empty => '' or array()
 * widget has selected value(s) => 'string' or Array(String, String, ...)
 * 
 * Widgets are supposed to return according values.
 * 
 * By this all voodoo-knowledge about when which widget returns what should be obsolete.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.85 beta
 * @package formdata
 */
class FormValueSet {
	// ***
	private $values;
	
	/**
	 * Basic object constructor. Initializes with empty values.
	 */
	public function __construct(){
		$this->values = array();
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Sets a value for a html-name in the resultset.
	 * Value may only be null, an empty string, an empty array, a string or an array of strings.
	 * 
	 * @param String $name html-name of the widget the value is gathered from
	 * @param String $value the value-representation of the widget identified by the given name
	 * @return FormValueSet method owner
	 */
	public function setValue($name, $value){
		$this->values["$name"] = $value;
		return $this;
	}
	
	
	
	//---|magic----------
	
	/**
	 * Magic method caller.
	 * Enables value returns by calling a function of the required html-name.
	 * $valueSet->htmlnameofwidget()
	 * 
	 * @param String $name html-name of value to get
	 * @param array $args obligatory second parameter for magic function
	 * @return null/String/Array[String] value of the required widget
	 */
	public function __call($name, Array $args = array()){
		return isset($this->values["$name"]) ? $this->values["$name"] : null;
	}
	
	
	
	/**
	 * Magic attribute getter.
	 * Enables value returns by asking the FormValueSet for an attribute of the
	 * name of the required html-name.
	 * $valueSet->htmlnameofwidget
	 * 
	 * @param String $name html-name of value to get
	 * @return null/String/Array[String] value of the required widget
	 */
	public function __get($name){
		return isset($this->values["$name"]) ? $this->values["$name"] : null;
	}
}

?>