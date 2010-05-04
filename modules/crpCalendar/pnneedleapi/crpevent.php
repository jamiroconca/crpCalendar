<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007,2010 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

/**
 * crpCalendar event needle
 * @param $args['nid'] needle id
 * @return array()
 */
function crpCalendar_needleapi_crpevent($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);

    // cache the results
    static $cache;

    if (!isset($cache)) {
        $cache = array();
    }

    $dom = ZLanguage::getModuleDomain('crpCalendar');

    if (!empty($nid)) {
        if (!isset($cache[$nid])) {
            // not in cache array

            $obj = pnModAPIFunc('crpCalendar', 'user', 'get', array('eventid' => $nid));

            if ($obj != false) {
                $url   = DataUtil::formatForDisplay(pnModURL('crpCalendar', 'user', 'display', array('eventid' => $nid)));
                $title = DataUtil::formatForDisplay($obj['title']);
                $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
            } else {
                $cache[$nid] = '<em>' . __f("Error! Database contains no event with the eventid '%s'.", $nid, $dom) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . __('Error! No needle ID provided.', $dom) . '</em>';
    }

    return $result;
}
