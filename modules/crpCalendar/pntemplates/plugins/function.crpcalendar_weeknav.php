<?php

/**
 * crpCalendar
 *
 * @copyright (c) 2007,2009 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca dot daniele at gmail dot com>
 * @license GNU/GPL - v.2.1
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
 *
 * @return string the results of the module function
 */
function smarty_function_crpcalendar_weeknav($params, & $smarty)
{
    // Security check
    if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
    {
        return LogUtil :: registerPermissionError();
    }

    $dom = ZLanguage::getModuleDomain('crpCalendar');

    if (!$params['date'] || !$params['t'])
    return LogUtil :: registerError(__('Error! Could not do what you wanted. Please check your input.', $dom));
    else
    {
        $date = $params['date'];
        $t = $params['t'];
    }

    $navbar = '';
    ($params['dateview']) ? $dateview = false : $dateview = true;
    ($params['space']) ? $space = $params['space'] : $space = '&nbsp;';
    ($params['separator']) ? $separator = $params['separator'] : $separator = $space . '|' . $space . "\n";
    ($params['container']) ? $container = $params['container'] : $container = 'span';
    ($params['prev_month_char']) ? $prev_month_char = $params['prev_month_char'] : $prev_month_char = '&lt;&lt;';
    ($params['prev_week_char']) ? $prev_week_char = $params['prev_week_char'] : $prev_week_char = '&lt;';
    ($params['next_week_char']) ? $next_week_char = $params['next_week_char'] : $next_week_char = '&gt;';
    ($params['next_month_char']) ? $next_month_char = $params['next_month_char'] : $next_month_char = '&gt;&gt;';
    ($params['dateformat']) ? $dateformat = $params['dateformat'] : $dateformat = '%W';

    $nav['next_week_time'] = mktime(0, 0, 0, $date['m'], $date['d'] + 7, $date['y']);
    $nav['prev_week_time'] = mktime(0, 0, 0, $date['m'], $date['d'] - 7, $date['y']);
    $nav['next_month_time'] = mktime(0, 0, 0, $date['m'] + 1, $date['d'], $date['y']);
    $nav['prev_month_time'] = mktime(0, 0, 0, $date['m'] - 1, $date['d'], $date['y']);
    $nav['year_time'] = mktime(0, 0, 0, $date['m'], 1, $date['y']);
    $nav['t'] = $t;

    $navbar .= '<' . $container . '>';
    $navbar .= '<a href="' . DataUtil :: formatForDisplay(pnModUrl('crpCalendar', 'user', 'week_view', array (
		't' => $nav['prev_month_time']
    ))) . '" title="' . _CRPCALENDAR_PREV_MONTH . '">' . "\n";
    $navbar .= $prev_month_char . "\n";
    $navbar .= '</a>' . "\n";

    $navbar .= $separator;

    $navbar .= '<a href="' . DataUtil :: formatForDisplay(pnModUrl('crpCalendar', 'user', 'week_view', array (
		't' => $nav['prev_week_time']
    ))) . '" title="' . _CRPCALENDAR_PREV_WEEK . '">' . "\n";
    $navbar .= $prev_week_char . "\n";
    $navbar .= '</a>' . "\n";

    ($dateview) ? $navbar .= $space . _WEEK . $space . (DateUtil :: getDatetime($t, $dateformat) + 1) . $space . "\n" : $navbar .= $separator . "\n";

    $navbar .= '<a href="' . DataUtil :: formatForDisplay(pnModUrl('crpCalendar', 'user', 'week_view', array (
		't' => $nav['next_week_time']
    ))) . '" title="' . _CRPCALENDAR_NEXT_WEEK . '">' . "\n";
    $navbar .= $next_week_char . "\n";
    $navbar .= '</a>' . "\n";

    $navbar .= $separator . "\n";

    $navbar .= '<a href="' . DataUtil :: formatForDisplay(pnModUrl('crpCalendar', 'user', 'week_view', array (
		't' => $nav['next_month_time']
    ))) . '" title="' . _CRPCALENDAR_NEXT_MONTH . '">' . "\n";
    $navbar .= $next_month_char . "\n";
    $navbar .= '</a>' . "\n";
    $navbar .= '</' . $container . '>';

    return $navbar;
}
