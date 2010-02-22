<?php

//---|includes----------

require_once('htmlform.inputtext.class.php');
require_once('htmlform.label.class.php');

require_once('htmlform.tools.class.php');



//---|class----------

class JsDateTime extends InputText{
	// ***
	const WRAPPERCLASS = 'htmlform_jsdatetime';
	const BUTTONCLASS = 'htmlform_jsdatetime_btn';
	
	private $jsConfig;
	private $printJs;
	
	private $dateFormat;
	private $dateSelectionType;
	private $displayTime;
	private $timeMode;
	private $showSeconds;
	
	protected function __construct($name, $id){
		parent::__construct($name, $id);
		
		$this->jsConfig = array();
		$this->printJs = true;
		
		$this->dateFormat = 'yyyymmdd';
		$this->dateSelectionType = 'dropdown';
		$this->displayTime = false;
		$this->timeMode = 24;
		$this->showSeconds = false;
	}
	
	
	
	public static function get($name, $id){
		$res = new JsDateTime($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	public function setJsConfig(Array $jsConfig){
		$this->jsConfig = $jsConfig;
		return $this;
	}
	
	
	
	public function setJsConfigVars(Array $jsConfigVars){
		foreach( $jsConfigVars as $key => $var ){
			$this->jsConfig[$key] = $var;
		}
		
		return $this;
	}
	
	
	
	public function setDateFormat($dateFormat){
		$this->dateFormat = "$dateFormat";
		return $this;
	}
	
	
	
	public function setDropDownSelection(){
		$this->dateSelectionType = 'dropdown';
		return $this;
	}
	
	
	
	public function setArrowSelection(){
		$this->dateSelectionType = 'arrow';
		return $this;
	}
	
	
	
	public function showTime(){
		$this->displayTime = true;
		return $this;
	}
	
	
	
	public function setAmPmTime(){
		$this->timeMode = 12;
		return $this;
	}
	
	
	
	public function setIsoTime(){
		$this->timeMode = 24;
		return $this;
	}
	
	
	
	public function showSeconds(){
		$this->showSeconds = true;
		return $this;
	}
	
	
	
	//---|functionality----------

	public function suppressJsInclude(){
		$this->printJs = false;
		return $this;
	}
	
	
	
	public function setUpAsIsoDate(){
		$this->dateFormat = 'yyyymmdd';
		$this->jsConfig['DateSeparator'] = '-';
		return $this;
	}
	
	
	
	public function setUpAsGermanDate(){
		$this->dateFormat = 'ddmmyyyy';
		$this->jsConfig['DateSeparator'] = '.';
		return $this;
	}
	
	
	
	//---|output----------

	private function printJsConfig(){
		// Variablenvorbereitung
		$packagePath = ($this->masterForm->getPackagePath() == '') ? '' : $this->masterForm->getPackagePath().'/';
		
		$monthNames =
			(
				isset($this->jsConfig['MonthName']) 
				&& is_array($this->jsConfig['MonthName']) 
				&& (count($this->jsConfig['MonthName']) == 12)
			)
			? $this->jsConfig['MonthName']
			: array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')
		;
		
		$weekDayNames =
			(
				isset($this->jsConfig['WeekDayName']) 
				&& is_array($this->jsConfig['WeekDayName']) 
				&& (count($this->jsConfig['WeekDayName']) == 7)
			)
			? $this->jsConfig['WeekDayName']
			: array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')
		;
		
		// String-Vorbereitung
		$monthNamesString = '[';
		foreach( $monthNames as $name ){
			$monthNamesString .= '"'.$name.'", ';
		}
		$monthNamesString = HtmlFormTools::auto_preg_replace('/(, )$/', '', $monthNamesString, $this->needsUtf8Safety()).']';
		
		$weekDayNamesString = array();
		$weekDayNamesString[0] = '[';
		$weekDayNamesString[1] = '[';
		foreach( $weekDayNames as $day ){
			$weekDayNamesString[0] .= '"'.$day.'", ';
		}
		$tmp = array_shift($weekDayNames);
		$weekDayNames[count($weekDayNames)] = $tmp;
		foreach( $weekDayNames as $day ){
			$weekDayNamesString[1] .= '"'.$day.'", ';
		}
		$weekDayNamesString[0] = HtmlFormTools::auto_preg_replace('/(, )$/', '', $weekDayNamesString[0], $this->needsUtf8Safety()).']';
		$weekDayNamesString[1] = HtmlFormTools::auto_preg_replace('/(, )$/', '', $weekDayNamesString[1], $this->needsUtf8Safety()).']';
		
		// Rückgabekonstruktion
		return
			 '<script type="text/javascript">'
				.'var SpanBorderColor = "'.(isset($this->jsConfig['SpanBorderColor']) ? ''.$this->jsConfig['SpanBorderColor'] : '#cdcdcd').'";'
				.'var SpanBgColor = "'.(isset($this->jsConfig['SpanBgColor']) ? ''.$this->jsConfig['SpanBgColor'] : '#cdcdcd').'";'
				.'var WeekChar = '.(isset($this->jsConfig['WeekChar']) ? ''.$this->jsConfig['WeekChar'] : '2').';'
				.'var DateSeparator = "'.(isset($this->jsConfig['DateSeparator']) ? ''.$this->jsConfig['DateSeparator'] : '-').'";'
				.'var ShowLongMonth = '.(isset($this->jsConfig['ShowLongMonth']) ? ''.$this->jsConfig['ShowLongMonth'] : 'true').';'
				.'var ShowMonthYear = '.(isset($this->jsConfig['ShowMonthYear']) ? ''.$this->jsConfig['ShowMonthYear'] : 'true').';'
				.'var MonthYearColor = "'.(isset($this->jsConfig['MonthYearColor']) ? ''.$this->jsConfig['MonthYearColor'] : '#cc0033').'";'
				.'var WeekHeadColor = "'.(isset($this->jsConfig['WeekHeadColor']) ? ''.$this->jsConfig['WeekHeadColor'] : '#18861B').'";'
				.'var SundayColor = "'.(isset($this->jsConfig['SundayColor']) ? ''.$this->jsConfig['SundayColor'] : '#C0F64F').'";'
				.'var SaturdayColor = "'.(isset($this->jsConfig['SaturdayColor']) ? ''.$this->jsConfig['SaturdayColor'] : '#C0F64F').'";'
				.'var WeekDayColor = "'.(isset($this->jsConfig['WeekDayColor']) ? ''.$this->jsConfig['WeekDayColor'] : 'white').'";'
				.'var FontColor = "'.(isset($this->jsConfig['FontColor']) ? ''.$this->jsConfig['FontColor'] : 'blue').'";'
				.'var TodayColor = "'.(isset($this->jsConfig['TodayColor']) ? ''.$this->jsConfig['TodayColor'] : '#FFFF33').'";'
				.'var SelDateColor = "'.(isset($this->jsConfig['SelDateColor']) ? ''.$this->jsConfig['SelDateColor'] : '#8DD53C').'";'
				.'var YrSelColor = "'.(isset($this->jsConfig['YrSelColor']) ? ''.$this->jsConfig['YrSelColor'] : '#cc0033').'";'
				.'var MthSelColor = "'.(isset($this->jsConfig['MthSelColor']) ? ''.$this->jsConfig['MthSelColor'] : '#cc0033').'";'
				.'var ThemeBg = "'.(isset($this->jsConfig['ThemeBg']) ? ''.$this->jsConfig['ThemeBg'] : '').'";'
				.'var CalBgColor = "'.(isset($this->jsConfig['CalBgColor']) ? ''.$this->jsConfig['CalBgColor'] : '').'";'
				.'var PrecedeZero = '.(isset($this->jsConfig['PrecedeZero']) ? ''.$this->jsConfig['PrecedeZero'] : 'true').';'
				.'var MondayFirstDay = '.(isset($this->jsConfig['MondayFirstDay']) ? ''.$this->jsConfig['MondayFirstDay'] : 'false').';'
				.'var UseImageFiles = '.(isset($this->jsConfig['UseImageFiles']) ? ''.$this->jsConfig['UseImageFiles'] : 'true').';'
				.'var MonthName = '.$monthNamesString.';'
				.'var WeekDayName1 = '.$weekDayNamesString[0].';'
				.'var WeekDayName2 = '.$weekDayNamesString[1].';'
				
				.'var packagePath = "'.$packagePath.'";'
			.'</script>'
		;
	}
	
	
	
	public function doRender(){
		$this->cssClasses = self::WRAPPERCLASS.' '.$this->cssClasses;
	
		$label = ($this->label != '') ? Label::get($this)->doRender() : '';
		$packagePath = ($this->masterForm->getPackagePath() == '') ? '' : $this->masterForm->getPackagePath().'/';
		$jsInclude = 
			$this->printJs
				? $this->printJsConfig().'<script src="'.$packagePath.'js/datetimepicker_css.js" type="text/javascript"></script>'
				: ''
		;
	
		return
			 $jsInclude
			.'<div class="'.$this->printWrapperClasses().'">'
				.$label
				.'<div class="'.parent::WIDGETCLASS.'"'.$this->printJsEventHandler().'>'
					.'<input'
						.$this->printId()
						.$this->printName()
						.' type="text"'
						.' value="'.HtmlFormTools::auto_htmlspecialchars($this->text, $this->needsUtf8Safety()).'"'
						.$this->printTitle()
						.$this->printSize()
						.$this->printMaxLength()
						.$this->printCssClasses()
						.$this->printTabindex()
						.$this->printReadonly()
						.$this->printDisabled()
						.$this->masterForm->printSlash()
					.'>'
					.'<img class="'.self::BUTTONCLASS.'" src="'.$packagePath.'img/cal.gif" style="cursor:pointer" onclick="NewCssCal(\''.$this->id.'\', \''.$this->dateFormat.'\', \''.$this->dateSelectionType.'\', \''.$this->displayTime.'\', \''.$this->timeMode.'\', \''.(!$this->showSeconds).'\');">'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
		;
	}
}

?>