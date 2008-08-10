<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://code.zikula.org/projects/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

Loader :: includeOnce('modules/crpCalendar/pnlang/eng/global_block.php');

define('_CRPCALENDAR_BLOCK_DISPLAYWEEK','Display week links into table');
define('_CRPCALENDAR_BLOCK_DISPLAYEVENTS','Display events into table');

// view navigation
define('_CRPCALENDAR_NEXT_DAY','Tomorrow');
define('_CRPCALENDAR_NEXT_WEEK','Next week');
define('_CRPCALENDAR_NO_EVENTS','No events to be shown');
define('_CRPCALENDAR_PREV_DAY','Yesterday');
define('_CRPCALENDAR_PREV_WEEK','Previous week');
define('_CRPCALENDAR_VIEW_DAY','Daily view');
define('_CRPCALENDAR_VIEW_MONTH','Monthly view');
define('_CRPCALENDAR_VIEW_YEAR','Yearly view');
define('_CRPCALENDAR_VIEW_WEEK','Weekly view');

// days label
define('_CRPCALENDAR_MON','M');
define('_CRPCALENDAR_TUE','T');
define('_CRPCALENDAR_WED','W');
define('_CRPCALENDAR_THU','T');
define('_CRPCALENDAR_FRI','F');
define('_CRPCALENDAR_SAT','S');
define('_CRPCALENDAR_SUN','S');