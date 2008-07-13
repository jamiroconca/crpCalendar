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
 * crpCalendarUI
 */
class crpCalendarUI
{

	function crpCalendarUI()
	{
		// none
		$this->theme_options = array('default' => 'default',
																	'tan' => 'tan',
																	'grey' => 'grey');
																	
		$this->day_of_week_short = array (_CRPCALENDAR_MON,
																			_CRPCALENDAR_TUE,
																			_CRPCALENDAR_WED,
																			_CRPCALENDAR_THU,
																			_CRPCALENDAR_FRI,
																			_CRPCALENDAR_SAT,
																			_CRPCALENDAR_SUN);
	}
	
	
	/**
	 * Draw events administration list
	 * 
	 * @param array $rows of event's value
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html code
	 */
	function adminList($rows=array(), $category=null, $mainCat=null, $modvars=array(), $active=null)
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar', false);

		// Assign the items to the template
		$pnRender->assign('events', $rows);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign($modvars);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign('events_status', $active);

		// Assign the information required to create the pager
		$pnRender->assign('pager', array (
			'numitems' => pnModAPIFunc('crpCalendar',
			'user',
			'countitems',
			array (
				'category' => $category,
				'active'		=> $active
			)
		), 'itemsperpage' => $modvars['itemsperpage']));

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_admin_view.htm');
	}
	
	
	/**
	 * Draw user events list
	 * 
	 * @param array $rows of event's value
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * @param string $typeList upcoming/archive
	 * 
	 * @return string html code
	 */
	function userList($rows=array(), $category=null, $mainCat=null, $modvars=array(), $typeList=null)
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');

		// Assign the items to the template
		$pnRender->assign('events', $rows);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);
		$pnRender->assign('typeList', $typeList);

		// Assign the information required to create the pager
		$pnRender->assign('pager', array (
			'numitems' => pnModAPIFunc('crpCalendar',
			'user',
			'countitems',
			array (
				'category' => $category,
				'active' => 'A',
				'typeList' => $typeList
			)
		), 'itemsperpage' => $modvars['itemsperpage']));

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_view.htm');
	}
	
	/**
	 * Draw user events list
	 * 
	 * @param array $rows of event's value
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html code
	 */
	function userPartecipations($rows=array(), $uid=null, $category=null, $mainCat=null, $modvars=array())
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');

		// Assign the items to the template
		$pnRender->assign('events', $rows);
		$pnRender->assign('uid', $uid);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);
		$pnRender->assign('user_avatar', pnUserGetVar('_YOURAVATAR', $uid));

		// Assign the information required to create the pager
		$pnRender->assign('pager', array (
			'numitems' => pnModAPIFunc('crpCalendar',
			'user',
			'countitems',
			array (
				'category' => $category,
				'active' => 'A',
				'uid' => $uid
			)
		), 'itemsperpage' => $modvars['itemsperpage']));

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_view_partecipations.htm');
	}
	
	/**
	 * Draw user events list
	 * 
	 * @param array $rows of event's value
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * @param array $arrayExport for ical export
	 * 
	 * @return string html code
	 */
	function attendeesList($rows=array(), $category=null, $mainCat=null, $modvars=array())
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');
		
		// add Profile define
		pnModLangLoad('Profile');

		// Assign the items to the template
		$pnRender->assign('users', $rows);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);
		/* TODO : 9999 -> $navigationValues['modvars'] to be changed when Zikula ticket #49 is resolved 
		$pnRender->assign('pager', array (
			'numitems' => pnModAPIFunc('crpCalendar',
			'user',
			'countitems_attendance',
			array (
				'category' => $category,
				'active' => 'A',
				'eventid' => true
			)
		), 'itemsperpage' => $modvars['itemsperpage']));
		*/
		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_view_attendees.htm');
	}
	
	/**
	 * Draw user month events list
	 * 
	 * @param array $items of event's value
	 * @param array $days of week's month dates
	 * @param array $daysexpanded out of week's month dates
	 * @param int $t timestamp
	 * @param array $date year,month,day values
	 * @param datetime $startDate init
	 * @param datetime $endDate end
	 * @param datetime $today for layout purpose
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * @param string $viewForm visualization
	 * 
	 * @return string html code
	 */
	function userMonthList($items=array(), $days=array(), $daysexpanded=array(), $t=null, $date=null,
													$startDate=null, $endDate=null, $today=null,
													$category=null, $mainCat=null, $modvars=array(), $viewForm='table')
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');

		// Assign the items to the template
		$pnRender->assign('events', $items);
		$pnRender->assign('days', $days);
		$pnRender->assign('daysexpanded', $daysexpanded);
		$pnRender->assign('t', $t);
		$pnRender->assign('todayEv', $today);
		$pnRender->assign('date', $date);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);
		$pnRender->assign('viewForm', $viewForm);
		
		$pnRender->assign('day_of_week_short', $this->day_of_week_short);

		// Return the output that has been generated by this function
		if ($viewForm == 'list')
			return $pnRender->fetch('crpcalendar_user_view_month.htm');
		else
			return $pnRender->fetch('crpcalendar_user_monthlist.htm');
	}
	
	/**
	 * Draw user week events list
	 * 
	 * @param array $items of event's value
	 * @param array $days of week's month dates
	 * @param array $daysexpanded out of week's month dates
	 * @param int $t timestamp
	 * @param array $date year,month,day values
	 * @param datetime $startDate init
	 * @param datetime $endDate end
	 * @param datetime $today for layout purpose
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * @param string $viewForm visualization
	 *  
	 * @return string html code
	 */
	function userWeekList($items=array(), $days=array(), $daysexpanded=array(), $t=null, $date=null,
													$startDate=null, $endDate=null, $today=null,
													$category=null, $mainCat=null, $modvars=array(), $viewForm='table')
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');

		// Assign the items to the template
		$pnRender->assign('events', $items);
		$pnRender->assign('days', $days);
		$pnRender->assign('daysexpanded', $daysexpanded);
		$pnRender->assign('t', $t);
		$pnRender->assign('date', $date);
		$pnRender->assign('todayEv', $today);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);
		
		$pnRender->assign('day_of_week_short', $this->day_of_week_short);

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_weeklist.htm');
	}
	
	/**
	 * Draw user week events list
	 * 
	 * @param array $items of event's value
	 * @param array $day to display
	 * @param int $t timestamp
	 * @param array $date year,month,day values
	 * @param datetime $startDate init
	 * @param datetime $endDate end
	 * @param datetime $today for layout purpose
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html code
	 */
	function userDayList($items=array(), $day=null, $t=null, $date=null,
													$startDate=null, $endDate=null, $today=null,
													$category=null, $mainCat=null, $modvars=array())
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');

		// Assign the items to the template
		$pnRender->assign('events', $items);
		$pnRender->assign('day', $day);
		$pnRender->assign('t', $t);
		$pnRender->assign('date', $date);
		$pnRender->assign('todayEv', $today);
		$pnRender->assign('events_category', $category);
		$pnRender->assign('mainCategory', $mainCat);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);
		
		$pnRender->assign('day_of_week_short', $this->day_of_week_short);

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_daylist.htm');
	}
	
	
	/**
	 * Draw single event page
	 * 
	 * @param int $eventid identifier
	 * @param array $item of event's value
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html code
	 */
	function userDisplay($eventid=null, $item=array(), $modvars=array())
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');
		
		// determine which template to render this page with
    // A specific template may exist for this page (based on page id)
    if ($pnRender->template_exists("crpcalendar_user_display_$eventid")) {
        $template = "crpcalendar_user_display_$eventid";
    } else {
        $template = 'crpcalendar_user_display.htm';
    }
    
		// check if the contents are cached.
    if ($pnRender->is_cached($template)) {
        return $pnRender->fetch($template);
    }
    
		// Assign the items to the template
		$pnRender->assign('event', $item);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_display.htm');
	}
	
	/**
	 * Draw single event page for Content module
	 * 
	 * @param int $eventid identifier
	 * @param array $item of event's value
	 * @param int $category current category if specified
	 * @param int $mainCat module root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html code
	 */
	function userSimpleDisplay($eventid=null, $item=array(), $modvars=array())
	{
		// Create output object
		$pnRender = pnRender::getInstance('crpCalendar');
		
		// determine which template to render this page with
    // A specific template may exist for this page (based on page id)
    if ($pnRender->template_exists("crpcalendar_user_simple_display_$eventid")) {
        $template = "crpcalendar_user_simple_display_$eventid";
    } else {
        $template = 'crpcalendar_user_simple_display.htm';
    }
    
		// check if the contents are cached.
    if ($pnRender->is_cached($template)) {
        return $pnRender->fetch($template);
    }
    
		// Assign the items to the template
		$pnRender->assign('event', $item);
		$pnRender->assign('lang', pnUserGetLang());
		$pnRender->assign($modvars);

		// Return the output that has been generated by this function
		return $pnRender->fetch('crpcalendar_user_simple_display.htm');
	}
	
	
	/**
	 * Draw creation form
	 * 
	 * @return string html
	 */
	function newEvent($item=array(), $mainCat, $modvars, $avail=array())
	{
		// Create output object
    $pnRender = pnRender::getInstance('crpCalendar', false);

    $pnRender->assign ('mainCategory', $mainCat);
    $pnRender->assign($modvars);
    $pnRender->assign($item);
    $pnRender->assign('avail',$avail);

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_admin_new.htm');
	}
	
	
	/**
	 * Draw user creation form
	 * 
	 * @return string html
	 */
	function submitEvent($item=array(), $mainCat, $modvars, $avail=array())
	{
		// Create output object
    $pnRender = pnRender::getInstance('crpCalendar', false);

    $pnRender->assign ('mainCategory', $mainCat);
    $pnRender->assign($modvars);
    $pnRender->assign($item);
    $pnRender->assign('avail',$avail);

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_user_new.htm');
	}
	
	
	/**
	 * Draw modify form
	 * 
	 * @param array $item element values
	 * @param int $mainCat module's root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html
	 */
	function modifyEvent($item, $mainCat, $modvars, $avail=array())
	{
		// Create output object
    $pnRender = pnRender::getInstance('crpCalendar', false);

   	$pnRender->assign ('mainCategory', $mainCat);
    $pnRender->assign($modvars);

    // assign the item to the template
    $pnRender->assign('eventid', $item['eventid']);
    $pnRender->assign($item);
    $pnRender->assign('avail',$avail);

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_admin_modify.htm');
	}
	
	/**
	 * Draw modify form
	 * 
	 * @param array $item element values
	 * @param int $mainCat module's root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html
	 */
	function editEvent($item, $mainCat, $modvars, $avail=array())
	{
		// Create output object
    $pnRender = pnRender::getInstance('crpCalendar', false);

   	$pnRender->assign ('mainCategory', $mainCat);
    $pnRender->assign($modvars);

    // assign the item to the template
    $pnRender->assign('eventid', $item['eventid']);
    $pnRender->assign($item);
    $pnRender->assign('avail',$avail);

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_user_edit.htm');
	}
	
	/**
	 * Draw delete form
	 * 
	 * @param int $eventid item identifier
	 * 
	 * @return string html
	 */
	function deleteEvent($eventid)
	{
		// Create output object
    $pnRender = pnRender::getInstance('crpCalendar', false);

    // assign the item to the template
    $pnRender->assign('eventid', $eventid);

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_admin_delete.htm');
	}
	
	
	/**
	 * Draw modify configuration form
	 * 
	 * @param array $item element values
	 * @param int $mainCat module's root category
	 * @param array $modvars module's variables
	 * 
	 * @return string html
	 */
	function modifyConfig($modvars=array(), $gdArray=array())
	{
		// Create output object
    $pnRender = pnRender::getInstance('crpCalendar', false);

    $pnRender->assign($modvars);
    $pnRender->assign('theme_options', $this->theme_options);
    $pnRender->assign('gd_version', $this->gd_version($gdArray['GD Version']));

    // Return the output that has been generated by this function
    return $pnRender->fetch('crpcalendar_admin_modifyconfig.htm');
	}
	
	/**
	 * Draw rss feed
	 * 
	 * @param array $data feed values
	 * @param array $list elements
	 * 
	 * @return string xml
	 */
	function drawFeed($data, $list)
	{
		$pnRender = pnRender::getInstance('crpCalendar', false);
		$pnRender->assign('data', $data);
		$pnRender->assign('list', $list);

		return $pnRender->fetch('crpcalendar_user_getfeed.htm');
	}
	
	/**
	 * Draw ical event export
	 * 
	 * @param array $data event values
	 * 
	 * @return string html
	 */
	function drawICal($data=array())
	{
		$pnRender = pnRender::getInstance('crpCalendar', false);
		$pnRender->assign('data', $data);

		return $pnRender->fetch('ical/crpcalendar_user_getical.htm');
	}
	
	/**
	 * Draw ical header export
	 * 
	 * @param string $selfurl return url
	 * 
	 * @return string html
	 */
	function drawICalHeader($selfurl=null)
	{
		$pnRender = pnRender::getInstance('crpCalendar', false);
		$pnRender->assign('selfurl', $selfurl);

		return $pnRender->fetch('ical/crpcalendar_user_ical_header.htm');
	}
	
	/**
	 * Draw ical footer export
	 * 
	 * @return string html
	 */
	function drawICalFooter()
	{
		$pnRender = pnRender::getInstance('crpCalendar', false);
		
		return $pnRender->fetch('ical/crpcalendar_user_ical_footer.htm');
	}
	
	function gd_version($fullstring=null) 
	{
		$cache_gd_version = array();
		
		if (eregi('bundled \((.+)\)$', $fullstring, $matches)) {
			$cache_gd_version['string'] = $fullstring;  // e.g. "bundled (2.0.15 compatible)"
			$cache_gd_version['value'] = (float) $matches[1];     // e.g. "2.0" (not "bundled (2.0.15 compatible)")
		} else {
			$cache_gd_version['string'] = $fullstring;                       // e.g. "1.6.2 or higher"
			$cache_gd_version['value'] = (float) substr($fullstring, 0, 3); // e.g. "1.6" (not "1.6.2 or higher")
		}
	
		return $cache_gd_version;
	}

}
?>
