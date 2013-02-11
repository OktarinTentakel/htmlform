<?php

//---|includes----------

require_once 'HtmlForm.FormElement.InputText.class.php';
require_once 'HtmlForm.FormElement.Label.class.php';

require_once 'HtmlFormTools.class.php';



//---|class----------

/**
 * Wraps a standard text-input adapted and expanded to work as a date-time-picker.
 * This element is an example of how to write expanded, special elements, with behaviour not
 * originally found in html. JsDateTime take a normal InputText, expands from it and adds a
 * calendar-button to the mix, which opens a javascript-powered date-time-picker with own images.
 * The picked date and/or time will the be inserted into tht textfield as an ordinary string.
 * <br>
 * This element shows several principles:<br>
 * - elements and widgets are not bound by html alone<br>
 * - inclusion of external assets like javscripts and images by setting a packagepath in the form and adding
 *   the assets to the package
 * <br><br>
 * The javascript-date-time-picker can be configured via parameters using the according setters.
 * These are the parameters and their defaults:
 * 
 * var SpanBorderColor = "#cdcdcd";//span border color<br>
 * var SpanBgColor = "#cdcdcd";//span background color<br>
 * var WeekChar=3;//number of character for week day. if 2 then Mo,Tu,We. if 3 then Mon,Tue,Wed.<br>
 * var DateSeparator="-";//Date Separator, you can change it to "-" if you want.<br>
 * var ShowLongMonth=true;//Show long month name in Calendar header. example: "January".<br>
 * var ShowMonthYear=true;//Show Month and Year in Calendar header.<br>
 * var MonthYearColor="#cc0033";//Font Color of Month and Year in Calendar header.<br>
 * var WeekHeadColor="#18861B";//Background Color in Week header.<br>
 * var SundayColor="#C0F64F";//Background color of Sunday.<br>
 * var SaturdayColor="#C0F64F";//Background color of Saturday.<br>
 * var WeekDayColor="white";//Background color of weekdays.<br>
 * var FontColor="blue";//color of font in Calendar day cell.<br>
 * var TodayColor="#FFFF33";//Background color of today.<br>
 * var SelDateColor="#8DD53C";//Backgrond color of selected date in textbox.<br>
 * var YrSelColor="#cc0033";//color of font of Year selector.<br>
 * var MthSelColor="#cc0033";//color of font of Month selector if "MonthSelector" is "arrow".<br>
 * var ThemeBg="";//Background image of Calendar window.<br>
 * var CalBgColor="";//Backgroud color of Calendar window.<br>
 * var PrecedeZero=true;//Preceding zero [true|false]<br>
 * var MondayFirstDay=false;//true:Use Monday as first day; false:Sunday as first day. [true|false]  //added in version 1.7<br>
 * var UseImageFiles = false;//Use image files with "arrows" and "close" button<br>
 * var MonthName=["January", "February", "March", "April", "May", "June", "July","August", "September", "October", "November", "December"];<br>
 * var WeekDayName1=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];<br>
 * var WeekDayName2=["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package formelements
 * @subpackage value-widgets
 */
class JsDateTime extends InputText{
	
	/**
	 * css-class for the wrapper around the whole element-html-structure
	 * @var String
	 */
	const WRAPPERCLASS = 'htmlform_jsdatetime';
	
	/**
	 * css-class for the calendar-button
	 * @var String
	 */
	const BUTTONCLASS = 'htmlform_jsdatetime_btn';
	
	// ***
	private $jsConfig;
	private $printJs;
	
	private $dateFormat;
	private $dateSelectionType;
	private $displayTime;
	private $timeMode;
	private $showSeconds;
	
	/**
	 * Hidden constructor.
	 * Get new instances with "get()" instead.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 */
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
	
	
	
	/**
	 * Factory method for JsDateTime, returns new instance.
	 * Factories are used to make instant chaining possible.
	 * 
	 * @param String $name html-name for the element
	 * @param String $id html-id for the element
	 * @return JsDateTime new JsDateTime-instance
	 */
	public static function get($name, $id){
		$res = new JsDateTime($name, $id);
		return $res;
	}
	// ***
	
	
	
	//---|setter----------
	
	/**
	 * Set a configuration object for the element.
	 * There are always defaults for every parameter, so this set doesn't have to be complete necessarily.
	 * 
	 * @param Array[String] $jsConfig set of configuration data to replace the current config for the element
	 * @return JsDateTime method owner
	 */
	public function setJsConfig(Array $jsConfig){
		$this->jsConfig = $jsConfig;
		return $this;
	}
	
	
	
	/**
	 * Set several configuration vars for the element.
	 * 
	 * @param Array[String] $jsConfigVars set of configuration data to add to the current config for the element
	 * @return JsDateTime method owner
	 */
	public function setJsConfigVars(Array $jsConfigVars){
		foreach( $jsConfigVars as $key => $var ){
			$this->jsConfig[$key] = $var;
		}
		
		return $this;
	}
	
	
	
	/**
	 * Set a date format to be used by the picker.
	 * Default is the iso-format "yyyymmdd", feel free to change that.
	 * 
	 * @param String $dateFormat the date format to use
	 * @return JsDateTime method owner
	 */
	public function setDateFormat($dateFormat){
		$this->dateFormat = "$dateFormat";
		return $this;
	}
	
	
	
	/**
	 * Tells the picker to use selects for month-, year-selection and so forth, instead of arrows.
	 * 
	 * @return JsDateTime method owner
	 */
	public function setDropDownSelection(){
		$this->dateSelectionType = 'dropdown';
		return $this;
	}
	
	
	
	/**
	 * Tells the picker to use arrows for month-, year-selection and so forth, instead of selects.
	 * 
	 * @return JsDateTime method owner
	 */
	public function setArrowSelection(){
		$this->dateSelectionType = 'arrow';
		return $this;
	}
	
	
	
	/**
	 * Tells the picker to offer time selection as well.
	 * 
	 * @return JsDateTime method owner
	 */
	public function showTime(){
		$this->displayTime = true;
		return $this;
	}
	
	
	
	/**
	 * Tells the picker to use american AMPM-time-format instead of iso-counting.
	 * 
	 * @return JsDateTime method owner
	 */
	public function setAmPmTime(){
		$this->timeMode = 12;
		return $this;
	}
	
	
	
	/**
	 * Tells the picker to use 24h-iso-counting for the time.
	 * 
	 * @return JsDateTime method owner
	 */
	public function setIsoTime(){
		$this->timeMode = 24;
		return $this;
	}
	
	
	
	/**
	 * Tells the picker to include seconds into the time selection.
	 * 
	 * @return JsDateTime method owner
	 */
	public function showSeconds(){
		$this->showSeconds = true;
		return $this;
	}
	
	
	
	//---|functionality----------
	
	/**
	 * Suppresses the javascript-include of the picker sources for this element.
	 * If you got several pickers on a page you want to include those only with the
	 * first and not every time.
	 * 
	 * @return JsDateTime method owner
	 */
	public function suppressJsInclude(){
		$this->printJs = false;
		return $this;
	}
	
	
	
	/**
	 * Configures the picker to offer the selection of a standard American date.
	 * Date-parts will be separated by a forward slash here.
	 *
	 * @return JsDateTime method owner
	 */
	public function setUpAsAmericanDate(){
		$this->dateFormat = 'mmddyyyy';
		$this->jsConfig['DateSeparator'] = '/';
		return $this;
	}
	
	
	
	/**
	 * Configures the picker to offer the selection of a standard iso-date.
	 * Date-parts will be separated by a dash here.
	 * 
	 * @return JsDateTime method owner
	 */
	public function setUpAsIsoDate(){
		$this->dateFormat = 'yyyymmdd';
		$this->jsConfig['DateSeparator'] = '-';
		return $this;
	}
	
	
	
	/**
	 * Configures the picker to offer the selection of a german date.
	 * Date-parts will be separated by a point here.
	 * 
	 * @return JsDateTime method owner
	 */
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
	
	
	
	/**
	 * Compiles and returns the html-fragment for the element.
	 * 
	 * @return String html-fragment for the element
	 */
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
				.'<div class="'.parent::WIDGETCLASS.'"'.$this->printJavascriptEventHandler().'>'
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
					.'<img'
						.' class="'.self::BUTTONCLASS.'"'
						.' src="'.$packagePath.'img/cal.gif"'
						.' style="cursor:pointer"'
						.' onclick="NewCssCal(\''.$this->id.'\', \''.$this->dateFormat.'\', \''.$this->dateSelectionType.'\', \''.$this->displayTime.'\', \''.$this->timeMode.'\', \''.(!$this->showSeconds).'\');"'
						.' alt="datetimepicker"'
						.$this->masterForm->printSlash()
					.'>'
				.'</div>'
				.$this->masterForm->printFloatBreak()
			.'</div>'
			.$this->printJavascriptValidationCode()
		;
	}
}

?>