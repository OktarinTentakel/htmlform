<?php

require_once('htmlform/htmlform.class.php');

$testForm = HtmlForm::get('form1')->addCssClasses('testform');

$testFieldSet = FieldSet::get()->setLegend('testfieldset');

$testFieldSet->addElement(
	CustomHtml::get()
		->setHtml('<img src="img/test.jpg" alt="">')
);

$testFieldSet->addElement(
	InputText::get('testinputtext')
		->setLabel('testlabel')
		->setText('testvalue')
		->setCssClasses('bordered')
);

$testFieldSet->addElement(
	Select::get('testselectsingle')
		->setOptions(array('a' => 'test1', 'b' => 'test2', 'c' => 'test3'))
		->setSelectedSingle('test2')->setLabel('testsingleselect')
);

$testFieldSet->addElement(
	Select::get('testselectmultiple')
		->setOptions(array('a' => 'test1', 'b' => 'test2', 'c' => 'test3'))
		->setSelectedIndices(array(1, 3))
		->setMultiple()
		->setSize(3)
		->setLabel('testmultiselect')
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
);

$testForm->addElement($testAlignBlock);

$testForm->addCell();

$testFieldSet2 = FieldSet::get()->setLegend('testfieldset2');
$testFieldSet2->addElement(
	InputText::get('testtextinput2')
		->setLabel('testinputtext2')
		->setText('test')
		->setValidator(
			FormValidator::get()
				->setRequired()
				->setMinLength(3)
		)
);
$testFieldSet2->addElement(
	InputRadio::get('radios1[]')
		->setLabel('radiotest1')
		->setOptions(array('a' => 'radio1', 'b' => 'radio2', 'c' => 'radio3', 'd' => 'radio4'))
		->setSelectedValue('d')
		->setWidth(3)
);
$testFieldSet2->addElement(
	InputCheckbox::get('check1[]')
		->setLabel('checktest1')
		->setOptions(array('a' => 'check1', 'b' => 'check2', 'c' => 'check3', 'd' => 'check4'))
		->setSelected(array('check2', 'check3'))
);

$testForm->addElement($testFieldSet2, 2);

$testForm->setHeadline('Dies ist eine Test&uuml;berschrift');
$testForm->setExplanation('Dies ist eine Testerkl&auml;rung eines HTML-Formulars.');

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
			.testform fieldset .htmlform_label_div{ float:left; width:150px; }
			.testform fieldset .htmlform_widget_div{ float:left; width:300px; }
			.testform .htmlform_alignblock{ text-align:right; }
			.testform .htmlform_custom{ margin-bottom:10px; }
			
			.htmlform_formheadline{ font-size:64px; color:red; }
			.htmlform_formexplanation{ font-size:16px; color:red; font-style:oblique; }
			
			.bordered{ border:1px solid black }
		</style>
	</head>
	
	<body>
		<?=$testForm->doRender()?>
		<p>
			<?=$testForm->validate() ? 'valid' : 'invalid'?>
		</p>
	</body>
</html>