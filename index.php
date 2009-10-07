<?php

require_once('htmlform/htmlform.class.php');

$testForm = HtmlForm::get('form1')
	->addCssClasses('testform')
	->showMessages('Fehler:')
	->setLanguage('german')
	->useExternalFormDeclaration()
	->setPackagePath('/////htmlform///')
	->setEnctype('multipart/form-data')
	->useReducedErrorMarking()
;

$testFieldSet = FieldSet::get()->setLegend('testfieldset');

$testFieldSet->addElement(
	CustomHtml::get()
		->setHtml('<img src="img/test.jpg" alt="">')
);
$testFieldSet->addElement(
	InputText::get('testinputtext')
		->setLabel('email')
		->setText('ich@du.de')
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
$testFieldSet->addElement(
	Select::get('testselectsingle')
		->setOptions(array('a' => 'test1', 'b' => 'test2', '3' => '333'))
		->setSelectedSingle('333')
		->setLabel('Einzelselect nur Zahlen')
		->setValidator(
			FormValidator::get()
				->setDigits()
		)
		->refill()
);
$testFieldSet->addElement(
	Select::get('testselectmultiple')
		->setOptions(array('a' => 'test1', 'b' => 'test2', 'c' => 'test3'))
		->setSelectedIndices(array(1, 3))
		->setMultiple()
		->setSize(3)
		->setLabel('testmultiselect')
		->refill()
);
$testFieldSet->addElement(
	InputText::get('datetest')
		->setLabel('Standarddatum')
		->setText('1/12/2002')
		->setValidator(
			FormValidator::get()
				->setDate()
		)
		->refill()
);
$testFieldSet->addElement(
	InputText::get('dateisotest')
		->setLabel('ISO-Datum')
		->setText('2002-12-1')
		->setValidator(
			FormValidator::get()
				->setDateISO()
		)
		->refill()
);
$testFieldSet->addElement(
	InputText::get('datedetest')
		->setLabel('deutsches Datum')
		->setText('1.12.2002')
		->setValidator(
			FormValidator::get()
				->setDateDE()
		)
		->refill()
);
$testFieldSet->addElement(
	InputText::get('numbertest')
		->setLabel('englische Dezimalzahl')
		->setText('100.1')
		->setValidator(
			FormValidator::get()
				->setNumber()
		)
		->refill()
);
$testFieldSet->addElement(
	InputText::get('numberdetest')
		->setLabel('deutsche Dezimalzahl')
		->setText('100,1')
		->setValidator(
			FormValidator::get()
				->setNumberDE()
		)
		->refill()
);
$testFieldSet->addElement(
	InputPassword::get('pass1')
		->setLabel('passwordtest1')
		->setText('test')
		->setMaxLength(8)
		->refill()
);
$testFieldSet->addElement(
	InputFile::get('file1')
		->setLabel('filetest1')
		->setText('test')
		->setAccept('text/*')
);

$testForm->addElement($testFieldSet);

$testAlignBlock = AlignBlock::get();

$testAlignBlock->addElement(
	InputSubmit::get('save', 'save')
		->setCaption('Abschicken')
);

$testAlignBlock->addElement(
	InputButton::get('cancel', 'cancel')
		->setCaption('Abbrechen')
		->setDisabled()
);

$testForm->addElement($testAlignBlock);

$testForm->addCell();

$testFieldSet2 = FieldSet::get()->setLegend('testfieldset2');
$testFieldSet2->addElement(
	InputText::get('testtextinput2')
		->setLabel('L&auml;nge zwischen 3 und 10')
		->setText('testotesto')
		->setValidator(
			FormValidator::get()
				->setRequired()
				->setMinLength(3)
				->setMaxLength(10)
		)
		->refill()
);
$testFieldSet2->addElement(
	InputText::get('testtextinput3')
		->setLabel('L&auml;nge zwischen 4 und 6')
		->setText('testo')
		->setValidator(
			FormValidator::get()
				->setRangeLength(array(4,6))
		)
		->refill()
);
$testFieldSet2->addElement(
	InputText::get('testtextinput5')
		->setLabel('zwischen 3 und 10')
		->setText('4')
		->setValidator(
			FormValidator::get()
				->setMin(3)
				->setMax(10)
		)
		->refill()
);
$testFieldSet2->addElement(
	InputText::get('testtextinput6')
		->setLabel('zwischen 4 und 6')
		->setText('5')
		->setValidator(
			FormValidator::get()
				->setRange(array(4, 6))
		)
		->refill()
);
$testFieldSet2->addElement(
	InputText::get('testtextinput4')
		->setLabel('url')
		->setCssClasses('bordered')
		->setText('http://www.100sonnen.de')
		->setValidator(
			FormValidator::get()
				->setUrl()
		)
		->refill()
);
$testFieldSet2->addElement(
	InputRadio::get('radios1')
		->setLabel('radiotest1')
		->setOptions(array('a' => 'radio1', 'b' => 'radio2', 'c' => 'radio3', 'd' => 'radio4'))
		->setSelectedValue('d')
		->setWidth(3)
		->refill()
);

$checkbox1 = InputCheckbox::get('check1')
	->setLabel('checktest1')
	->setOptions(array('a' => 'check1', 'b' => 'check2', 'c' => 'check3', 'd' => 'check4'))
	->setSelected(array('check2', 'check3'));
$testFieldSet2->addElement($checkbox1);

$testFieldSet2->addElement(
	JsDateTime::get('cal1', 'cal1')
		->setLabel('datetimetest1')
		->setText('12.12.2008')
		->setReadonly()
		->setUpAsGermanDate()
		->setAmPmTime()
		->setArrowSelection()
		->showTime()
		->setJsConfigVars(
			array(
				'WeekChar' => 3,
				'SundayColor' => '#ffffff',
				'SaturdayColor' => '#ffffff',
				'WeekDayColor' => '#eeeeee'
			)
		)
		->setValidator(
			FormValidator::get()
				->setDateDE()
		)
		->refill()
);
$testFieldSet2->addElement(
	TextArea::get('textarea1')
		->setLabel('Flie&szlig;text (nur Buchstaben, Leer- und Satzzeichen)')
		->setText('Hallo Welt!')
		->setSize(20, 10)
		->setValidator(
			FormValidator::get()
				->setCustomCase(
					preg_match('/^[a-zA-Z\!\.\,\? ]+$/', isset($_REQUEST['textarea1']) ? $_REQUEST['textarea1'] : 'Hallo Welt!')
						? ''
						: 'Keinen Murks in den Flie&szlig;text ey!'
				)
		)
		->refill()
);

$testForm->addElement($testFieldSet2, 2);

$testForm->setHeadline('Dies ist eine Test&uuml;berschrift');
$testForm->setExplanation('Dies ist eine Testerkl&auml;rung eines HTML-Formulars.');

$checkbox1->refill();

$testForm->validate();

$valSet = $testForm->getValueSet();

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<title>HTML Formularklasse Test</title>
		<style type="text/css" media="screen">
			.testform{ padding:10px;}
			.testform fieldset{ margin-left:auto; margin-right:auto; margin-bottom:25px; background:lightgrey;}
			.testform fieldset label{ color:white; }
			.testform fieldset input, .testform fieldset select{ width:250px; }
			.testform fieldset input[type=radio], .testform fieldset input[type=checkbox]{ width:auto; }
			.testform .htmlform_cell{ float:left; width:750px; }
			.testform fieldset .htmlform_row_div{ clear:left; margin-bottom:10px; }
			.testform fieldset .htmlform_label_div{ float:left; width:200px; }
			.testform fieldset .htmlform_widget_div{ float:left; width:300px; }
			.testform .htmlform_alignblock{ text-align:right; }
			.testform .htmlform_custom{ margin-bottom:10px; }
			
			.htmlform_formheadline{ font-size:64px; color:red; }
			.htmlform_formexplanation{ font-size:16px; color:red; font-style:oblique; }
			.htmlform_messages_div{ border:1px solid black; padding:10px; margin: 10px 0px 5px 0px; background:#fafafa; }
			.htmlform_messages_title_div, .htmlform_message_div{ color:red; margin-bottom:5px; background:#fafafa; }
			.htmlform_messages_title_div{ font-weight:bold; }
			.htmlform_jsdatetime_btn{ margin-left:5px; border:0px; }
			.htmlform_error{ background:#ff0000; }
			
			.bordered{ border:1px solid black }
		</style>
	</head>
	
	<body>
		<form id="form1" action="" method="post" accept-charset="UTF-8" class="testform">
			<?=$testForm->doRender()?>
		</form>
	</body>
</html>