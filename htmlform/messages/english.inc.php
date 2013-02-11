<?php

/**
 * English error-message-dictionary.
 * 
 * @author Sebastian Schlapkohl
 * @version 0.999 beta
 * @package validation
 * @subpackage dictionaries
 */

define('MSG_CUSTOMCASE', '"%name%" has an invalid form.');
define('MSG_REQUIRED', '"%name%" is required.');
define('MSG_NOTEMPTY', '"%name%" has to be non-empty.');
define('MSG_MINLENGTH', '"%name%" has a minimum size of %count% characters or options.');
define('MSG_MAXLENGTH', '"%name%" has a maximum size of %count% characters or options.');
define('MSG_RANGELENGTH', '"%name%" has a minimum size of %min% and a maximum size of %max% characters or options.');
define('MSG_MIN', '"%name%" has a minimum size of %count%.');
define('MSG_MAX', '"%name%" has a maximum size of %count%.');
define('MSG_RANGE', '"%name%" has a minimum size of %min% and a maximum size of %max%.');
define('MSG_EMAIL', '"%name%" has to be a valid eMail-address.');
define('MSG_URL', '"%name%" has to be a valid URL.');
define('MSG_DATE', '"%name%" has to be a valid date following the syntax &quot;(m)m/(d)d/yyyy&quot;.');
define('MSG_TIME', '"%name%" has to be a valid time following the syntax &quot;(h)h:mm(:ss)am|pm&quot;.');
define('MSG_DATETIME', '"%name%" has to be a valid datetime following the syntax &quot;(m)m/(d)d/yyyy (h)h:mm(:ss)am|pm&quot;.');
define('MSG_DATE_ISO', '"%name%" has to be a valid date following the ISO-syntax &quot;yyyy-mm-dd&quot;.');
define('MSG_TIME_ISO', '"%name%" has to be a valid time following the ISO-syntax &quot;hh:mm:ss&quot;.');
define('MSG_DATETIME_ISO', '"%name%" has to be a valid iso-datetime following the syntax &quot;yyyy-mm-dd(T| )hh:mm:ss&quot;.');
define('MSG_DATE_DE', '"%name%" has to be a valid date following the German syntax &quot;(d)d.(m)m.yyyy&quot;.');
define('MSG_TIME_DE', '"%name%" has to be a valid time following the German syntax &quot;hh:mm:ss(h)&quot;.');
define('MSG_DATETIME_DE', '"%name%" has to be a valid German datetime following the syntax &quot;(d)d.(m)m.yyyy hh:mm:ss(h)&quot;.');
define('MSG_NUMBER', '"%name%" has to be a number (can be decimal).');
define('MSG_NUMBER_DE', '"%name%" has to be a number according to German notation (can be decimal).');
define('MSG_DIGITS', '"%name%" must only contain digits.');
define('MSG_CREDITCARD', '"%name%" has to be a valid creditcard number of 15 to 16 Digits, where the digit groups are seperated by &quot;-&quot;.');
define('MSG_CHARACTERCLASS', '"%name%" contains forbidden characters (not in [%class%]).');

?>