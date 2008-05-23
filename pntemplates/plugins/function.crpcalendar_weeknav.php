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
 * <!--[crpcalendar_weeknav month="$month" ]-->
 * 
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 * @param int $status status flag
 * @param int $eventid item_identifier
 * 
 * @return string the results of the module function
 */
function smarty_function_crpcalendar_weeknav($params, &$smarty)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	if (!$params['t'])
		return LogUtil::registerError (_MODARGSERROR);
	else
	{
		$date = $params['date'];
		$t = $params['t'];
	}

  $navbar = '';
  
  $nav['next_week_time'] = mktime(0,0,0,$date['m'], $date['d']+7, $date['y']);
	$nav['prev_week_time'] = mktime(0,0,0,$date['m'], $date['d']-7, $date['y']);
	$nav['next_month_time'] = mktime(0,0,0,$date['m']+1, $date['d'], $date['y']);
	$nav['prev_month_time'] = mktime(0,0,0,$date['m']-1, $date['d'], $date['y']);
	$nav['year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']);
	$nav['t'] = $t;
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','week_view', array('t' => $nav['prev_month_time'])).'" title="'._CRPCALENDAR_PREV_MONTH.'">'."\n";
	$navbar .= '&lt;&lt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;|&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','week_view', array('t' => $nav['prev_week_time'])).'" title="'._CRPCALENDAR_PREV_WEEK.'">'."\n";
	$navbar .= '&lt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;'._WEEK.'&nbsp;'.DateUtil::getDatetime($t, '%W').'&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','week_view', array('t' => $nav['next_week_time'])).'" title="'._CRPCALENDAR_NEXT_WEEK.'">'."\n";
	$navbar .= '&gt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;|&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','week_view', array('t' => $nav['next_month_time'])).'" title="'._CRPCALENDAR_NEXT_MONTH.'">'."\n";
	$navbar .= '&gt;&gt;'."\n";
	$navbar .= '</a>'."\n";
	
  return $navbar;
}
?>
