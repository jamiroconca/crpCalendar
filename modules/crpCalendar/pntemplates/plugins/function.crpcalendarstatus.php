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
 * <!--[crpcalendarstatus status="$status_flag" fake=true]-->
 *
 * @param array $params All attributes passed to this function from the template
 * @param object &$smarty Reference to the Smarty object
 * @param int $status status flag
 * @param int $eventid item_identifier
 *
 * @return string the results of the module function
 */
function smarty_function_crpcalendarstatus($params, & $smarty)
{
    // Security check
    if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
    {
        return LogUtil :: registerPermissionError();
    }

    $statusimage = '';

    if (!$params['fake'])
    {
        if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_ADD) && ($params['status'] == 'A' || $params['status'] == 'P'))
        $statusimage .= '<a href="' . DataUtil :: formatForDisplay(pnModUrl('crpCalendar', 'admin', 'change_status', array (
                'eventid' => $params['eventid'],
                'obj_status' => $params['status']
        ))) . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '">' . "\n";
        else
        $statusimage .= '';

        if ($params['status'] == 'A')
        $statusimage .= '<img id="eventstatus_' . $params['eventid'] . '" src="modules/crpCalendar/pnimages/green_dot.gif" alt="' . _ACTIVE . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '"/>' . "\n</a>\n";
        elseif ($params['status'] == 'P') $statusimage .= '<img id="eventstatus_' . $params['eventid'] . '" src="modules/crpCalendar/pnimages/yellow_dot.gif" alt="' . _CRPCALENDAR_PENDING . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '"/>' . "\n</a>\n";
        else
        $statusimage .= '<img id="eventstatus_' . $params['eventid'] . '" src="modules/crpCalendar/pnimages/red_dot.gif" alt="' . _CRPCALENDAR_REJECTED . '" title="' . _CRPCALENDAR_CHANGE_STATUS_MODIFYING . '" />' . "\n";
    }
    else
    {
        if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_ADD) && ($params['status'] == 'A' || $params['status'] == 'P'))
        {
            $statusimage .= "<a href='javascript:void(0);'>";
            $statusimage .= '<img id="eventstatus_fake_A_' . $params['eventid'] . '" ';
            $statusimage .= ($params['status'] == 'P') ? ' style="display:none" ' : '';
            $statusimage .= '" onclick="togglestatus(\'' . $params['eventid'] . '\',\'A\')" src="modules/crpCalendar/pnimages/green_dot.gif" alt="' . _ACTIVE . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '"/>' . "\n";
            $statusimage .= '<img id="eventstatus_fake_P_' . $params['eventid'] . '"';
            $statusimage .= ($params['status'] == 'A') ? ' style="display:none" ' : '';
            $statusimage .= 'onclick="togglestatus(\'' . $params['eventid'] . '\',\'P\')" src="modules/crpCalendar/pnimages/yellow_dot.gif" alt="' . _CRPCALENDAR_PENDING . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '"/>' . "\n";
            $statusimage .= "</a>";
        }
        elseif ($params['status'] == 'A') $statusimage .= '<img id="eventstatus_' . $params['eventid'] . '" src="modules/crpCalendar/pnimages/green_dot.gif" alt="' . _ACTIVE . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '"/>' . "\n";
        elseif ($params['status'] == 'P') $statusimage .= '<img id="eventstatus_' . $params['eventid'] . '" src="modules/crpCalendar/pnimages/yellow_dot.gif" alt="' . _CRPCALENDAR_PENDING . '" title="' . _CRPCALENDAR_CHANGE_STATUS . '"/>' . "\n";
        else
        $statusimage .= '<img id="eventstatus_' . $params['eventid'] . '" src="modules/crpCalendar/pnimages/red_dot.gif" alt="' . _CRPCALENDAR_REJECTED . '" title="' . _CRPCALENDAR_CHANGE_STATUS_MODIFYING . '" />' . "\n";

    }

    return $statusimage;
}
