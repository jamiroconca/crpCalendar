<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007-2008, Daniele Conca
 * @link http://code.zikula.org/projects/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */
 
/**
 * Smarty function switch event view
 *
 * Example
 * <!--[crpcalendar_yearnav year="$year" ]-->
 * 
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 * @param int $status status flag
 * @param int $eventid item_identifier
 * 
 * @return string the results of the module function
 */
function smarty_function_crpcalendar_yearnav($params, &$smarty)
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

	$nav['next_year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']+1);
	$nav['prev_year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']-1);
	$nav['year_time'] = mktime(0,0,0,$date['m'], 1, $date['y']);
	$nav['t'] = $t;
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','year_view', array('t' => $nav['prev_year_time'])).'" title="'._CRPCALENDAR_PREV_YEAR.'">'."\n";
	$navbar .= '&lt;&lt;'."\n";
	$navbar .= '</a>'."\n";
	
	$navbar .= '&nbsp;<strong>'.DateUtil::getDatetime($t, '%Y').'</strong>&nbsp;'."\n";
	
	$navbar .= '<a href="'.pnModUrl('crpCalendar','user','year_view', array('t' => $nav['next_year_time'])).'" title="'._CRPCALENDAR_NEXT_YEAR.'">'."\n";
	$navbar .= '&gt;&gt;'."\n";
	$navbar .= '</a>'."\n";
	
  return $navbar;
}
?>
