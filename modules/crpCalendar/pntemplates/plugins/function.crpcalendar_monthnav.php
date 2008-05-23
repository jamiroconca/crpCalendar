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
 * <!--[crpcalendar_monthnav month="$month" ]-->
 * 
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 * @param int $status status flag
 * @param int $eventid item_identifier
 * 
 * @return string the results of the module function
 */
function smarty_function_crpcalendar_monthnav($params, &$smarty)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	if (!$params['date'] || !$params['t'])
		return LogUtil::registerError (_MODARGSERROR);
	else
	{
		$date = $params['date'];
		$t = $params['t'];
	}

  $navbar = '';
  
  $nav['next_month_time'] = mktime(0,0,0,$date['m']+1, 1, $date['y']);
	$nav['prev_month_time'] = mktime(0,0,0,$date['m']-1, 1, $date['y']);
	$nav['next_year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']+1);
	$nav['prev_year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']-1);
	$nav['year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']);
	$nav['t'] = $t;
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['prev_year_time'])).'" title="'._CRPCALENDAR_PREV_YEAR.'">'."\n";
	$navbar .= '&lt;&lt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;|&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['prev_month_time'])).'" title="'._CRPCALENDAR_PREV_MONTH.'">'."\n";
	$navbar .= '&lt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;'.DateUtil::getDatetime($t, '%B').'&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['next_month_time'])).'" title="'._CRPCALENDAR_NEXT_MONTH.'">'."\n";
	$navbar .= '&gt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;|&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['next_year_time'])).'" title="'._CRPCALENDAR_NEXT_YEAR.'">'."\n";
	$navbar .= '&gt;&gt;'."\n";
	$navbar .= '</a>'."\n";
	
  return $navbar;
}
?>
