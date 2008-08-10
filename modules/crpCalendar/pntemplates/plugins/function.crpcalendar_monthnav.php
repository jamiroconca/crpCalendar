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
 * <!--[crpcalendar_monthnav month="$month" ]-->
 * 
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
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
	($params['dateview'])?$dateview=false:$dateview=true;
  ($params['space'])?	$space = $params['space']:$space = '&nbsp;';
  ($params['separator'])?$separator = $params['separator']:$separator = $space.'|'.$space."\n";
  ($params['container'])?$container = $params['container']:$container = 'span';
  ($params['prev_year_char'])?$prev_year_char = $params['prev_year_char']:$prev_year_char = '&lt;&lt;';
  ($params['prev_month_char'])?$prev_month_char = $params['prev_month_char']:$prev_month_char = '&lt;';
  ($params['next_month_char'])?$next_month_char = $params['next_month_char']:$next_month_char = '&gt;';
  ($params['next_year_char'])?$next_year_char = $params['next_year_char']:$next_year_char = '&gt;&gt;';
  ($params['dateformat'])?$dateformat = $params['dateformat']:$dateformat = '%B';
  
  $nav['next_month_time'] = mktime(0,0,0,$date['m']+1, 1, $date['y']);
	$nav['prev_month_time'] = mktime(0,0,0,$date['m']-1, 1, $date['y']);
	$nav['next_year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']+1);
	$nav['prev_year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']-1);
	$nav['year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']);
	$nav['t'] = $t;
	
	$navbar .= '<'.$container.'>';
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['prev_year_time'])).'" title="'._CRPCALENDAR_PREV_YEAR.'">'."\n";
	$navbar .= $prev_year_char."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= $separator."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['prev_month_time'])).'" title="'._CRPCALENDAR_PREV_MONTH.'">'."\n";
	$navbar .= $prev_month_char."\n";
	$navbar .= '</a>'."\n";
	
	($dateview)?$navbar .= '&nbsp;'.DateUtil::getDatetime($t, $dateformat).'&nbsp;'."\n":$navbar.= $separator."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['next_month_time'])).'" title="'._CRPCALENDAR_NEXT_MONTH.'">'."\n";
	$navbar .= $next_month_char."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= $separator."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','month_view', array('t' => $nav['next_year_time'])).'" title="'._CRPCALENDAR_NEXT_YEAR.'">'."\n";
	$navbar .= $next_year_char."\n";
	$navbar .= '</a>'."\n";
	$navbar .= '</'.$container.'>';
	
  return $navbar;
}
?>
