<?php

/**
 * This is the general testcase scenario for the HtmlForm-framework.
 * 
 * Have a look at all the mechanisms and possible values and fool around with the possibilities
 * to get a feeling for the workings.
 * 
 * This is not, by any means, a well structured, integrated solution, but just a wild bunch of testcases
 * and tryouts, half showcase half test, but it's not very complicated to integrate the framework into
 * your workflows and write a nice wrapper or two.
 * 
 * I can tell by the code and by having quite some wrappers in my time :P
 * 
 * @author Sebastian Schlapkohl
 * @version 0.85 beta
 */

/**
 * Bind the main class to make whole framework usable and known
 */
require_once('htmlform/htmlform.class.php');

/**
 * Prepare php-output for utf-8. these are the minimum settings, to make utf-8-mode discoverable
 * for the framework. Without these utf-8 would be quite meaningless. These settings are not automatically
 * applied, since this is a too strong automatism, since it corrupt other values being processed in you apps. 
 */
ini_set('default_charset', 'UTF-8');
ini_set('mbstring.internal_encoding', 'UTF-8');
ini_set('mbstring.http_output', 'UTF-8');



//--|FORM----------

/**
 * Create the form.
 * - add css-classes
 * - activate error display (all / just custom)
 * - activate the usage of an external form declaration
 * - set the relative package path (slashes will be stripped automatically)
 * - set enctype to multiform to include file uploads
 * - set error marking to only marking the inputs itself
 */
$testForm = HtmlForm::get('form1')
	->addCssClasses('testform')
	->showMessages('Errors:')
	//->showCustomMessages('Errors:')
	->setLanguage('english')
	->useExternalFormDeclaration()
	->setPackagePath('/////htmlform///')
	->setEnctype('multipart/form-data')
	->useReducedErrorMarking()
;



//--|FIRST-FIELDSET----------

/**
 * Create a fieldset.
 * - apply a legend to it
 */
$testFieldSet = FieldSet::get()->setLegend('simple widgets');

/**
 * Create a custom-html-content-element and add it to the fieldset (simple, wrapped, all-purpose html)
 */
$testFieldSet->addElement(
	CustomHtml::get()
		->setHtml('<img src="img/htmlform_logo.png" alt="">')
);

/**
 * Create a standard text input and add it to the fieldset.
 * - add a label
 * - set a default text
 * - add random bordered css-class
 * - set width/size
 * - set the max input chars
 * - set a validator (has to be an eMail-address, has to fulfill random expression)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputText::get('testinputtext')
		->setLabel('standard text input (must be valid eMail-address):')
		->setText('me@you.com')
		->setCssClasses('bordered')
		->setSize(25)
		->setMaxLength(10)
		->setValidator(
			FormValidator::get()
				->setEmail()
				->setCustomCase(true)
		)
		->refill()
);

/**
 * Create a standard single select and add it to the fieldset.
 * - add a label
 * - add options to choose from (optgroup => [value => text])
 * - select a entry by text as default
 * - set a validator (must be a simple digit-number, has custom error message)
 * - set "none" as a value to be considered empty, so that chosing the default validates
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	Select::get('testselectsingle')
		->setLabel('single select (must select number or nothing):')
		->setOptions(
			array(
				'default' => array(
					'none' => '---'
				),
				'strings' => array(
					'a' => 'test1', 'b' => 'test2'
				),
				'numbers' => array(
					'3' => '333'), 'c' => '444.4'
				)
			)
		->setSelectedSingle('333')
		->setValidator(
			FormValidator::get()
				->setDigits()
				->setOptional(array('none'))
				->setErrorMessage('Please choose an "only digit"-value or none.')
		)
		->refill()
);

/**
 * Create a single select with a "none"-default and add it to the fieldset.
 * - add a label
 * - set options (value => text)
 * - set validator (must have a selection)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	Select::get('testselectsinglemixed')
		->setLabel('single select (must have a selection)')
		->setOptions(array('' => '---', 'b' => 'hey', 'c' => 'you'))
		->setValidator(
			FormValidator::get()
				->setRequired()
		)
		->refill()
);

/**
 * Create a multiple select and add it to the fieldset.
 * - add label
 * - set as multiple select
 * - set options (value => text)
 * - set css classes for options (they cycle if less then number of options)
 * - set titles for options (not quite standard, but practical, they also cycle)
 * - set several options selected as default by index starting with 1
 * - set select height
 * - set validator (must have selection, values of options must be "a" or "c", set all standard messages as custom here)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	Select::get('testselectmultiple')
		->setLabel('multi select (must have selection, values must be "a" or "c"):')
		->setMultiple()
		->setOptions(array('a' => 'test1', 'b' => 'test2', 'c' => 'test3'))
		->setOptionCssClasses(array('odd', 'even'))
		->setOptionTitles(array('eins', 'zwei'))
		->setSelectedIndices(array(1, 3))
		->setSize(3)
		->setValidator(
			FormValidator::get()
				->setRequired()
				->setCharacterClass('ac')
				->setAutoErrorMessagesAsCustom()
		)
		->refill()
);

/**
 * Create a standard text input and add it to the fieldset.
 * - add a label
 * - set valid standard date as default value
 * - set validator (must be standard date if filled, but is optional)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputText::get('datetest')
		->setLabel('standard text input (must be a standard date, is optional):')
		->setText('1/12/2002')
		->setValidator(
			FormValidator::get()
				->setDate()
				->setOptional()
		)
		->refill()
);

/**
 * Create a standard text input and add it to the fieldset.
 * - add a label
 * - set a valid iso-date as default text
 * - set validator (must be iso-date)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputText::get('dateisotest')
		->setLabel('standard text input (must be an iso-date):')
		->setText('2002-12-1')
		->setValidator(
			FormValidator::get()
				->setDateISO()
		)
		->refill()
);

/**
 * Create a standard text input and add it to the fieldset.
 * - add a label
 * - set a valid german date as default text
 * - set validator (must be german date)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputText::get('datedetest')
		->setLabel('standard text input (must be german date)')
		->setText('1.12.2002')
		->setValidator(
			FormValidator::get()
				->setDateDE()
		)
		->refill()
);

/**
 * Create a standard text input and add it to the fieldset.
 * - add a label
 * - set a valid english decimal number as default text
 * - set validator (must be english decimal number)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputText::get('numbertest')
		->setLabel('standard text input (must be english decimal number):')
		->setText('100.1')
		->setValidator(
			FormValidator::get()
				->setNumber()
		)
		->refill()
);

/**
 * Create a standard text input and add it to the fieldset.
 * - add a label
 * - set a valid german decimal number as default text
 * - set validator (must be german decimal number)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputText::get('numberdetest')
		->setLabel('standard text input (must be german decimal number):')
		->setText('100,1')
		->setValidator(
			FormValidator::get()
				->setNumberDE()
		)
		->refill()
);

/**
 * Create a password text input and add it to the fieldset.
 * - add a label
 * - set a default text
 * - set max char number to 8
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputPassword::get('pass1')
		->setLabel('password text input:')
		->setText('testtest')
		->setMaxLength(8)
		->refill()
);

/**
 * Create a file input and add it to the fieldset.
 * - add a label
 * - set a default text (to show that it doesn't show)
 * - set accept (will not be used by browsers, but could be used for internal checks)
 * - refill from default refiller (get/post)
 */
$testFieldSet->addElement(
	InputFile::get('file1')
		->setLabel('file input:')
		->setText('no file selected')
		->setAccept('text/*')
);

/**
 * Add the fieldset to the form (cell 1)
 */
$testForm->addElement($testFieldSet);

/**
 * Create an align block to insert buttons into the form, but being aligned right instead of left.
 */
$testAlignBlock = AlignBlock::get();

/**
 * Add input submit to align block.
 * - set button caption
 */
$testAlignBlock->addElement(
	InputSubmit::get('save', 'save')
		->setCaption('submit')
);

/**
 * Add alternative image-submit to align block.
 * - set image-url
 * - coords of clicks are shown down below in the result-display of the form
 */
$testAlignBlock->addElement(
	InputImage::get('imgsave', 'imgsave')
		->setSrc('img/submit.png')
);

/**
 * Add reset-button to align block.
 * - set button caption
 */
$testAlignBlock->addElement(
	InputReset::get('reset', 'reset')
		->setCaption('reset')
);

/**
 * Add input button to align block.
 * - set button caption
 * - set the button disabled
 */
$testAlignBlock->addElement(
	InputButton::get('btn1', 'btn1')
		->setCaption('random button')
		->setDisabled()
);

/**
 * Insert align block directly into the form, beneath the fieldset.
 */
$testForm->addElement($testAlignBlock);

/**
 * Add a second cell to the form.
 */
$testForm->addCell();



//--|SECOND-FIELDSET----------

/**
 * Create a second fieldset.
 * - set legend
 */
$testFieldSet2 = FieldSet::get()->setLegend('extended widgets');

/**
 * Create standard text input and add it to second fieldset.
 * - add a label
 * - set valid default text (10 length)
 * - set widget title
 * - set validator (must be filled, must have min 3 chars, must hav max 10 chars, only letters and umlauts)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	InputText::get('testtextinput2')
		->setLabel('standard text input (text length between 3 and 10):')
		->setText('lolcatpaws')
		->setTitle('between 3 and 10 please')
		->setValidator(
			FormValidator::get()
				->setRequired()
				->setMinLength(3)
				->setMaxLength(10)
				->setCharacterClass('a-zA-ZäöüÄÖÜß')
		)
		->refill()
);

/**
 * Create standard text input and add it to second fieldset.
 * - add a label
 * - set valid default text (5 length)
 * - set validator (must have between 4 and 6 chars)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	InputText::get('testtextinput3')
		->setLabel('standard text input (text length between 4 and 6):')
		->setText('tenso')
		->setValidator(
			FormValidator::get()
				->setRangeLength(array(4,6))
		)
		->refill()
);

/**
 * Create standard text input and add it to second fieldset.
 * - add a label
 * - set valid default text
 * - set validator (value must be min 3, value must be max 10)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	InputText::get('testtextinput5')
		->setLabel('standard text input (value between 3 and 10):')
		->setText('4')
		->setValidator(
			FormValidator::get()
				->setMin(3)
				->setMax(10)
		)
		->refill()
);

/**
 * Create standard text input and add it to second fieldset.
 * - add a label
 * - set valid default text
 * - set validator (value must be between 4 and 6)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	InputText::get('testtextinput6')
		->setLabel('standard text input (value between 4 and 6):')
		->setText('5')
		->setValidator(
			FormValidator::get()
				->setRange(array(4, 6))
		)
		->refill()
);

/**
 * Create standard text input and add it to second fieldset.
 * - add a label
 * - add random bordered css-class
 * - set valid default text (absolute url)
 * - set validator (value must be a valid url)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	InputText::get('testtextinput4')
		->setLabel('standard text input (must be url):')
		->addCssClasses('bordered')
		->setText('http://www.google.com')
		->setValidator(
			FormValidator::get()
				->setUrl()
		)
		->refill()
);

/**
 * Create a radiogroup and add it to second fieldset.
 * - add a label
 * - set selectable options (value => labeltext)
 * - set default selection
 * - set width of radiogroup (amount of radiobutton-cols)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	InputRadio::get('radios1')
		->setLabel('radiogroup:')
		->setOptions(array('a' => 'radio1', 'b' => 'radio2', 'c' => 'radio3', 'd' => 'radio4'))
		->setOptionCssClasses(array('allthesame'))
		->setOptionTitles(array('one', 'two', 'three', 'four'))
		->setSelectedValue('d')
		->setWidth(3)
		->refill()
);

/**
 * Create a checkboxgroup and add it to second fieldset (not instantly here).
 * - add a label
 * - set random css-class to prove that it isn't rendered (makes no sense for composita)
 * - set selectable options (value => labeltext)
 * - set css-classes for options (cycle if number smaller than option count)
 * - set default selection
 */
$checkbox1 = InputCheckbox::get('check1')
	->setLabel('checkboxgroup:')
	->setCssClasses('nothing')
	->setOptions(array('a' => 'check1', 'b' => 'check2', 'c' => 'check3', 'd' => 'check4'))
	->setOptionCssClasses(array('odd', 'equal', 'even'))
	->setOptionTitles(array('just one for all'))
	->setSelected(array('check2', 'check3'))
;
$testFieldSet2->addElement($checkbox1);

/**
 * Create a datetime text input and add it to second fieldset.
 * - add a label
 * - set a valid date text value
 * - set text readonly, to prevent direct editing
 * - set widget up for german dates
 * - set time format to am/pm-notation
 * - set navigation by arrows
 * - set up time display
 * - set config-values for javascript(3 chars for weekdays, color for sundays, color for saturdays, color for weekdays)
 * - set validator (must be german date)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	JsDateTime::get('cal1', 'cal1')
		->setLabel('datetime (german date):')
		->setText('12.12.2008')
		->setReadonly()
		->setUpAsGermanDate()
		->setAmPmTime()
		->setArrowSelection()
		->showTime()
		->setJsConfigVars(
			array(
				'SpanBorderColor' => '#37c900',
				'CalBgColor' => '#444444',
				'WeekChar' => 3,
				'SundayColor' => '#333333',
				'SaturdayColor' => '#333333',
				'WeekDayColor' => '#444444',
				'TodayColor' => '#666666',
				'SelDateColor' => '#37c900',
				'YrSelColor' => '#ffffff'
			)
		)
		->setValidator(
			FormValidator::get()
				->setDateDE()
		)
		->refill()
);

/**
 * Create textarea and add it to the second fieldset.
 * - add a label
 * - add a random javascript-event-handler
 * - add a valid text
 * - set dimensions of textarea (20 cols, 10 rows)
 * - set validator (custom case, with message result, empty string represents true here)
 * - refill from default refiller (get/post)
 */
$testFieldSet2->addElement(
	TextArea::get('textarea1')
		->setLabel('textarea (only normal chars, umlauts and punctuation, not empty, single number zero is considered empty):')
		->setJsEventHandler('onclick', 'alert(\'onclick-test\');')
		->setText('Hello world!')
		->setSize(20, 10)
		->setValidator(
			FormValidator::get()
				->setNotEmpty(array(0))
				->setCustomCase(
					preg_match('/^[a-zA-ZäöüÄÖÜß!.,? ]+$/u', isset($_REQUEST['textarea1']) ? $_REQUEST['textarea1'] : 'Hallo Welt!')
						? ''
						: 'Keinen Murks in den Flie&szlig;text ey!'
				)
		)
		->refill()
);

/**
 * Add second fieldset to second cell of the form.
 */
$testForm->addElement($testFieldSet2, 2);

/**
 * Create a custom html-content a append it in the middle of the form after a specific widget.
 */
$testForm->insertElementAfter('testselectmultiple',
	CustomHtml::get()
		->setHtml(
			 '<p style="font-size:larger; margin-top:25px; margin-bottom:25px; background-color: #444;">'
				.'This piece was injected into the middle of the form after completely building it.'
			.'</p>'
		)
);

/**
 * Set a headline and an explanation for the form.
 */
$testForm->setHeadline('HtmlForm Testcase Scenario');
$testForm->setExplanation(
	 'Use this form to test out the possibilities and see what else is possible. Feel free to break it...<br>'
	.'Expand viewport to see flow of form cells.<br>'
	.'Have a look at the source to discover the options and default solutions used here.'
);

/**
 * Late refill for checkbox group, to show, that timing for refill is relevant. A checkboxgroup can not be refilled to
 * "no checked boxes" before it is actually added to the form, instead it would be reset to default values. This is an
 * html-problem which can't be fixed easily. But this is only a problem if you chain the objects extremely.
 */
$checkbox1->refill();



//--|RESULT-HANDLING----------

/**
 * Check if form has been sent before validating it, otherwise there's little sense in it.
 */
$successContainer = '';
if($testForm->hasBeenSent()) {
	/**
	 * Start form validation. It knows its validity-state after this.
	 */
	$testForm->validate();
	
	/**
	 * Retrieve a complete valueset from the form.
	 * To get a value: $valueSet->nameofwidget. This ist either null (if value is missing, a string value
	 * or an array of string for multiples)
	 */
	$valueSet = $testForm->getValueSet();
	if( $testForm->isValid() && ($valueSet->save || $valueSet->imgsave) ){
		$successContainer = 
			 '<div id="successcontainer">'
			 	.'<div class="closer" onclick="document.body.removeChild(getElementById(\'successcontainer\'));">&or;</div>'
			 	.'<div class="text">'
					.'<h2>Hooray, the form validated! This is what it returned:</h2>'
					.'<pre>'.print_r($valueSet, true).'</pre>'
					.($valueSet->imgsave ? '<br>image-submit click-coordinates:<pre>'.print_r($testForm->getElementByName('imgsave')->getCoords(), true).'</pre>' : '')
				.'</div>'
			.'</div>'
		;
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<title>HtmlForm Testcase Scenario</title>
		<style type="text/css" media="screen">
			html, body {
				color: white;
				background-color: #333;
				
				font-family: arial, sens-serif;
				font-size: 11px;
			}
			
			
			
			.testform{
				padding: 10px;
			}
			
			.testform fieldset{
				border: 1px solid #37c900;
				
				padding: 10px;
				margin-left: auto;
				margin-right: auto;
				margin-bottom: 25px;
			}
			
			.testform fieldset label{
				color: #ccc;
			}
			
			.testform fieldset input, .testform fieldset select{
				width: 250px;
			}
			
			.testform fieldset input[type=radio], .testform fieldset input[type=checkbox]{
				width: auto;
			}
			
			.testform fieldset select option.odd{
				background-color: #afa;
			}
			
			.testform fieldset select option.even{
				background-color:#dfd;
			}
			
			.testform .htmlform_cell{
				float: left;
				
				width: 750px;
				
				margin-left: 15px;
			}
			
			.testform .htmlform_alignblock {
				padding: 10px;
				margin-bottom: 25px;
				
				border: 1px solid #37c900;
			}
			
			.testform .htmlform_alignblock input[type=submit],
			.testform .htmlform_alignblock input[type=button],
			.testform .htmlform_alignblock input[type=reset],
			.testform .htmlform_alignblock input[type=image] {
				margin-right: 5px;
			}
			
			.testform .htmlform_alignblock input[type=image]{
				vertical-align: middle;
				
				border: 1px solid #444;
			}
			
			.testform .htmlform_alignblock input[type=image]:hover{
				border: 1px solid white;
			}
			
			.testform fieldset .htmlform_row_div{
				clear: left;
				
				margin-bottom: 10px;
			}
			
			.testform fieldset .htmlform_label_div{
				float: left;
				
				width: 360px;
			}
			
			.testform fieldset .htmlform_widget_div{
				float: left;
				
				width: 360px;
			}
			
			.testform .htmlform_alignblock{
				text-align: right;
			}
			
			.testform .htmlform_custom{
				margin-bottom: 10px;
			}
			
			
			
			.htmlform_formheadline{
				margin-left: 10px;
				
				color: white;
				
				font-size: 64px;
			}
			
			.htmlform_formexplanation{
				color: #ccc;
				
				margin: -5px 0px 20px 15px;
				
				font-size: 16px;
			}
			
			.htmlform_messages_div{
				width: 728px;
			
				padding: 10px;
				margin: 10px 0px 5px 14px;
				border: 1px dotted #37c900;
				
				background-color: #444;
			}
			
			.htmlform_messages_title_div, .htmlform_message_div{
				margin-bottom: 5px;
			}
			
			.htmlform_messages_title_div{
				color: #e00;
			
				font-weight: bold;
			}
			
			.htmlform_jsdatetime_btn{
				margin-left: 5px;
				
				border: 0px;
			}
			
			.htmlform_error{
				background: #e00;
			}
			
			
			
			.bordered{
				border:1px solid black;
			}
			
			#calBorder table {
				border-spacing: 0px;
			}
			
			#successcontainer {
				position: fixed;
			
				height: 250px;
				right: 0px;
				bottom: 0px;
				left: 0px;
				
				border-top: 1px solid #37c900;
				
				background-color: #333;
			}
			
			#successcontainer .closer {
				position: absolute;;
				
				height: 20px;
				top: 0px;
				left: 0px;
				right:0px;
				
				padding-top: 4px;
				border-bottom: 1px dotted #37c900;
				
				text-align: center;
				font-size: 14px;
				font-weight: bold;
				
				cursor: pointer;
			}
			
			#successcontainer .closer:hover {
				background-color: #444;
			}
			
			#successcontainer .text {
				position: absolute;
			
				top: 25px;
				left: 0px;
				bottom: 0px;
				right:0px;
				
				padding: 0px 10px 5px 10px;
				
				overflow: auto;
			}
		</style>
	</head>
	
	<body>
		<?=$successContainer?>
		<form id="form1" action="" method="post" accept-charset="UTF-8" class="testform">
			<?=$testForm->doRender()?>
		</form>
	</body>
</html>