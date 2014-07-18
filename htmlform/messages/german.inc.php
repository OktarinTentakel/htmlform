<?php

/**
 * German error-message-dictionary.
 *
 * @author Sebastian Schlapkohl
 * @version 1.0
 * @package validation
 * @subpackage dictionaries
 */

define('MSG_CUSTOMCASE', '"%name%" ist nicht valide.');
define('MSG_REQUIRED', '"%name%" wird ben&ouml;tigt.');
define('MSG_NOTEMPTY', '"%name%" muss einen nicht-leeren Wert haben.');
define('MSG_MINLENGTH', '"%name%" ben&ouml;tigt eine Mindestanzahl von %count% Zeichen oder Optionen.');
define('MSG_MAXLENGTH', '"%name%" hat eine Obergrenze von %count% Zeichen oder Optionen.');
define('MSG_RANGELENGTH', '"%name%" hat eine Mindestanzahl von %min% und eine Obergrenze von %max% Zeichen oder Optionen.');
define('MSG_MIN', '"%name%" hat eine Mindestgr&ouml;&szlig;e von %count%.');
define('MSG_MAX', '"%name%" hat eine Obergrenze von %count%.');
define('MSG_RANGE', '"%name%" hat eine Mindestgr&ouml;&szlig;e von %min% und eine Obergrenze von %max%.');
define('MSG_EMAIL', '"%name%" muss eine valide eMail-Adresse sein.');
define('MSG_URL', '"%name%" muss eine valide URL sein.');
define('MSG_DATE', '"%name%" muss ein g&uuml;ltiges Datum entsprechend der Syntax &quot;(m)m/(d)d/yyyy&quot; sein.');
define('MSG_TIME', '"%name%" muss eine g&uuml;ltige Zeit entsprechend der Syntax &quot;(h)h:mm(:ss)am|pm&quot; sein.');
define('MSG_DATETIME', '"%name%" muss ein g&uuml;ltiges Datum mit Zeit entsprechend der Syntax &quot;(m)m/(d)d/yyyy (h)h:mm(:ss)am|pm&quot; sein.');
define('MSG_DATE_ISO', '"%name%" muss ein g&uuml;ltiges Datum entsprechend der ISO-Syntax &quot;yyyy-mm-dd&quot; sein.');
define('MSG_TIME_ISO', '"%name%" muss eine g&uuml;ltige Zeit entsprechend der ISO-Syntax &quot;hh:mm:ss&quot; sein.');
define('MSG_DATETIME_ISO', '"%name%" muss ein g&uuml;ltiges Datum mit Zeit entsprechend der ISO-Syntax &quot;yyyy-mm-dd(T| )hh:mm:ss&quot; sein.');
define('MSG_DATE_DE', '"%name%" muss ein g&uuml;ltiges Datum entsprechend der deutschen Syntax &quot;(d)d.(m)m.yyyy&quot; sein.');
define('MSG_TIME_DE', '"%name%" muss eine g&uuml;ltige Zeit entsprechend der  deutschen Syntax &quot;hh:mm:ss(h)&quot; sein.');
define('MSG_DATETIME_DE', '"%name%" muss ein g&uuml;ltiges Datum mit Zeit entsprechend der  deutschen Syntax &quot;(d)d.(m)m.yyyy hh:mm:ss(h)&quot; sein.');
define('MSG_NUMBER', '"%name%" muss eine Zahl englischer Notation sein (auch dezimal)');
define('MSG_NUMBER_DE', '"%name%" muss eine Zahl sein (auch dezimal).');
define('MSG_DIGITS', '"%name%" darf nur Zahlen beinhalten.');
define('MSG_CREDITCARD', '"%name%" muss eine Kreditkartennummer von 15 bis 16 Zahlen L&auml;nge sein, deren Zahlengruppen durch &quot;-&quot; getrennt sind.');
define('MSG_CHARACTERCLASS', '"%name%" enth&auml;lt verbotene Zeichen (nicht in [%class%]).');

?>