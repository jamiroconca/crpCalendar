<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @author Daniele Conca <conca dot daniele at gmail dot com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */
 
/**
 * Smarty function to display status of an event
 *
 * Example
 * <!--[crpcalendar_daynav day="$day" separator="||" container="h3"]-->
 * 
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 *  
 * @return string the results of the module function
 */
function smarty_function_crpcalendar_daynav($params, &$smarty)
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
	($params['space'])?	$space = $params['space']:$space = '&nbsp;';
  ($params['separator'])?$separator = $params['separator']:$separator = $space.'|'.$space."\n";
  ($params['container'])?$container = $params['container']:$container = 'span';
  ($params['prev_week_char'])?$prev_week_char = $params['prev_week_char']:$prev_week_char = '&lt;&lt;';
  ($params['prev_day_char'])?$prev_day_char = $params['prev_day_char']:$prev_day_char = '&lt;';
  ($params['next_day_char'])?$next_day_char = $params['next_day_char']:$next_day_char = '&gt;';
  ($params['next_week_char'])?$next_week_char = $params['next_week_char']:$next_week_char = '&gt;&gt;';
	($params['dateformat'])?$dateformat = $params['dateformat']:$dateformat = '%A %d %B';  
  
  $nav['next_day_time'] = mktime(0,0,0,$date['m'], $date['d']+1, $date['y']);
	$nav['prev_day_time'] = mktime(0,0,0,$date['m'], $date['d']-1, $date['y']);
	$nav['next_week_time'] = mktime(0,0,0,$date['m'], $date['d']+7, $date['y']);
	$nav['prev_week_time'] = mktime(0,0,0,$date['m'], $date['d']-7, $date['y']);
	
	$nav['t'] = $t;
	
	$navbar .= '<'.$container.'>';
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','day_view', array('t' => $nav['prev_week_time'])).'" title="'._CRPCALENDAR_PREV_WEEK.'">'."\n";
	$navbar .= $prev_week_char."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= $separator;
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','day_view', array('t' => $nav['prev_day_time'])).'" title="'._CRPCALENDAR_PREV_DAY.'">'."\n";
	$navbar .= $prev_day_char."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= $space.DateUtil::getDatetime($t, $dateformat).$space."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','day_view', array('t' => $nav['next_day_time'])).'" title="'._CRPCALENDAR_NEXT_DAY.'">'."\n";
	$navbar .= $next_day_char."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= $separator;
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','day_view', array('t' => $nav['next_week_time'])).'" title="'._CRPCALENDAR_NEXT_WEEK.'">'."\n";
	$navbar .= $next_week_char."\n";
	$navbar .= '</a>'."\n";
	$navbar .= '</'.$container.'>';
	
  return $navbar;
}
?>
