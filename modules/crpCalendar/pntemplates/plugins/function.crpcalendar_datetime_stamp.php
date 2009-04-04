<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @author Daniele Conca <jami at cremonapalloza dot org>
 * @license GNU/GPL - v.2
 * @package crpCalendar
 */

/**
 * Smarty function to display status of an event
 *
 * Example
 * <!--[crpcalendar_datetime_stamp datetime=$datetime ]-->
 *
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 * @param datetime $datetime
 *
 * @return string the results of the module function
 */
function smarty_function_crpcalendar_datetime_stamp($params, &$smarty)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}

	if (!$params['datetime'])
		return DateUtil :: getDatetime();
	else
		$datetime = $params['datetime'];


	$res = DateUtil::makeTimestamp($datetime);

  if (isset($params['assign']) && $params['assign'])
		  	$smarty->assign ($params['assign'], $res);
	else
		return $res;

}

?>
