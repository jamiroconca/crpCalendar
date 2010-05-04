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

function crpCalendar_needleapi_crpevent_info()
{
    $info = array('module'  => 'crpCalendar',
                  'info'    => 'CRPEVENT{eventid}',
                  'inspect' => false);

    return $info;
}
