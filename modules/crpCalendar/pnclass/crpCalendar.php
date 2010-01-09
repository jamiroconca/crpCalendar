<?php

/**
 * crpCalendar
 *
 * @copyright (c) 2007,2009 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendarUI.php');
Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendarDAO.php');

/**
 * crpCalendar Object
 */
class crpCalendar
{

	/**
	 * constructor, set values
	 */
	function crpCalendar()
	{
		$this->ui = new crpCalendarUI();
		$this->dao = new crpCalendarDAO();

		(function_exists('gd_info')) ? $this->gd = gd_info() : $this->gd = array ();

		$this->modvars = pnModGetVar('crpCalendar');

		//gettext
		$this->dom = ZLanguage :: getModuleDomain('crpCalendar');
	}

	/**
	 * Return a condition about an event
	 *
	 * @param int $eventid identifier
	 *
	 * @return bool
	 */
	function isAuthor($eventid = null)
	{
		$author = false;
		$author = $this->dao->isAuthor($eventid);

		return $author;
	}

	/**
	 * see if a user is authorised to carry out a particular task
	 *
	 * @param int $level level of access required
	 * @param int $author_object object's author id
	 * @param int $id_object object's id
	 * @param int $name_object object's title
	 *
	 * @return boolean true if authorized
	 */
	function authAction($level = 0, $author_object = '', $id_object = '', $name_object = '')
	{
		return $this->dao->getAuth($level, $author_object, $id_object, $name_object);
	}

	/**
	 * Main administrative page with event's list
	 *
	 * @return string html
	 */
	function manageEvents()
	{
		$navigationValues = $this->collectNavigationFromInput();

		// Get all matching pages
		$items = pnModAPIFunc('crpCalendar', 'admin', 'getall', $navigationValues);

		if (!$items)
			$items = array ();

		$rows = array ();
		foreach ($items as $key => $item)
		{
			$options = array ();

			// display link
			$options[] = crpCalendar :: buildLinkArray(__("View", $this->dom), $item, 'user');
			// edit, copy, delete
			if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']) || ($this->authAction(ACCESS_EDIT, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])))
			{
				$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
				$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
				if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
					$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
			}

			// Add the calculated menu options to the item array
			$item['options'] = $options;
			$rows[] = $item;
		}

		return $this->ui->adminList($rows, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars'], $navigationValues['active'], $navigationValues['sortColumn'], $navigationValues['sortOrder']);
	}

	/**
	 * Main user page with event's list
	 *
	 * @return string html
	 */
	function listEvents()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'view');
		SessionUtil :: setVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'view'));

		$navigationValues = $this->collectNavigationFromInput();
		SessionUtil :: delVar('crpCalendar_export_events');
		SessionUtil :: delVar('crpCalendar_choosed_time');

		// Get all matching pages
		$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items = array ();

		$rows = array ();
		$exports = array ();
		foreach ($items as $key => $item)
		{
			$options = array ();

			// display link
			$options[] = crpCalendar :: buildLinkArray(__("View", $this->dom), $item, 'user');

			// subscribe to event
			if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation'))
			{
				if (!$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
					$options[] = crpCalendar :: buildLinkArray(__("Add partecipation", $this->dom), $item, 'user');
				else
					$options[] = crpCalendar :: buildLinkArray(__("Delete partecipation", $this->dom), $item, 'user');
			}

			// edit, copy, delete
			if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']))
			{
				$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
				$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
				if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
					$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
			}
			elseif ($this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])) $options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'user');

			// Add the calculated menu options to the item array
			$item['options'] = $options;
			$rows[] = $item;
			$exports[] = $item['eventid'];
		}

		SessionUtil :: setVar('crpCalendar_export_events', $exports);

		return $this->ui->userList($rows, $navigationValues['startnum'], $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars'], $navigationValues['typeList']);
	}

	/**
	 * Page with month's events list
	 *
	 * @return string html
	 */
	function listYearEvents()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'year_view');
		SessionUtil :: setVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'year_view'));

		$navigationValues = $this->collectNavigationFromInput();
		SessionUtil :: delVar('crpCalendar_export_events');
		SessionUtil :: delVar('crpCalendar_choosed_time');

		$date = $this->timeToDMY($navigationValues['t']);

		$startYear = DateUtil :: getDatetime(mktime(0, 0, 0, 1, 1, $date['y']));
		$endYear = DateUtil :: getDatetime(mktime(0, 0, 0, 12, 31, $date['y']));

		$navigationValues['startDate'] = DateUtil :: getDatetime(DateUtil :: parseUIDateTime($startYear));
		$navigationValues['endDate'] = DateUtil :: getDatetime(DateUtil :: parseUIDateTime($endYear));
		$navigationValues['sortOrder'] = 'ASC';
		// reset page limit for daylist
		$navigationValues['modvars']['itemsperpage'] = '-1';
		// Get all matching pages
		$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items = array ();

		$rows = array ();
		$exports = array ();
		foreach ($items as $key => $item)
		{
			$options = array ();
			// display link
			$options[] = crpCalendar :: buildLinkArray(__("View", $this->dom), $item, 'user');

			// subscribe to event
			if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation'))
			{
				if (!$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
					$options[] = crpCalendar :: buildLinkArray(__("Add partecipation", $this->dom), $item, 'user');
				else
					$options[] = crpCalendar :: buildLinkArray(__("Delete partecipation", $this->dom), $item, 'user');
			}

			// edit, copy, delete
			if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']))
			{
				$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
				$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
				if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
					$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
			}
			elseif ($this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])) $options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'user');

			// Add the calculated menu options to the item array
			$item['options'] = $options;
			$rows[] = $item;
			$exports[] = $item['eventid'];
		}

		if ($navigationValues['modvars']['yearlist_categorized'])
		{
			$cats = CategoryUtil :: getSubCategories($navigationValues['mainCat']);
			$userLang = ZLanguage :: getLanguageCode();
			foreach ($cats as $cat)
			{
				foreach ($items as $kitem => $vitem)
				{
					if ($cat['id'] == $vitem[__CATEGORIES__]['Main']['id'])
						$categorizedEvents[$vitem[__CATEGORIES__]['Main']['display_name'][$userLang]][] = $vitem;
				}
			}
			$rows = $categorizedEvents;
		}

		SessionUtil :: setVar('crpCalendar_export_events', $exports);

		return $this->ui->userYearList($rows, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars'], $navigationValues['t'], $date);
	}

	/**
	 * Page with month's events list
	 *
	 * @return string html
	 */
	function listMonthEvents()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'month_view');
		SessionUtil :: setVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'month_view'));

		$navigationValues = $this->collectNavigationFromInput();
		SessionUtil :: delVar('crpCalendar_export_events');

		$date = $this->timeToDMY($navigationValues['t']);

		$days = DateUtil :: getMonthDates($date['m'], $date['y']);
		$daysexpanded = $days;
		$monthFirstDay = $days['1'];
		$monthLastDay = $days[count($days)];

		$navigationValues['startDate'] = DateUtil :: getDatetime($this->backToFirstDOW(DateUtil :: parseUIDateTime($monthFirstDay)));
		$navigationValues['endDate'] = DateUtil :: getDatetime($this->forwardToLastDOW(DateUtil :: parseUIDateTime($monthLastDay)));
		$navigationValues['sortOrder'] = 'ASC';
		// reset page limit for monthlist
		$navigationValues['modvars']['itemsperpage'] = '-1';

		// Get all matching events
		$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items = array ();

		$exports = array ();
		if ($navigationValues['viewForm'] == 'list')
		{
			foreach ($items as $key => $item)
			{
				$options = array ();
				// display link
				$options[] = crpCalendar :: buildLinkArray(__("View", $this->dom), $item, 'user');

				// subscribe to event
				if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation'))
				{
					if (!$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
						$options[] = crpCalendar :: buildLinkArray(__("Add partecipation", $this->dom), $item, 'user');
					else
						$options[] = crpCalendar :: buildLinkArray(__("Delete partecipation", $this->dom), $item, 'user');
				}

				// edit, copy, delete
				if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']))
				{
					$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
					$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
					if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
						$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
				}
				elseif ($this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])) $options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'user');

				// Add the calculated menu options to the item array
				$item['options'] = $options;
				$rows[] = $item;
			}
		}
		else
			$rows = $items;

		$exports = array ();
		foreach ($items as $key => $item)
		{
			$exports[] = $item['eventid'];
		}

		SessionUtil :: setVar('crpCalendar_export_events', $exports);
		SessionUtil :: setVar('crpCalendar_choosed_time', $navigationValues['t']);

		$today = DateUtil :: getDatetime(time());

		// expand days array
		$this->expandFirstDOW(DateUtil :: parseUIDateTime($monthFirstDay), $daysexpanded);
		$this->expandLastDOW(DateUtil :: parseUIDateTime($monthLastDay), $daysexpanded);

		return $this->ui->userMonthList($rows, $days, $daysexpanded, $navigationValues['t'], $date, $navigationValues['startDate'], $navigationValues['endDate'], $today, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars'], $navigationValues['viewForm']);
	}

	/**
	 * Page with week's events list
	 *
	 * @return string html
	 */
	function listWeekEvents()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'week_view');
		SessionUtil :: setVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'week_view'));

		$navigationValues = $this->collectNavigationFromInput();
		SessionUtil :: delVar('crpCalendar_export_events');

		$date = $this->timeToDMY($navigationValues['t']);

		$days = array (
			DateUtil :: getDatetime($navigationValues['t'])
		);
		$weekDay = DateUtil :: getDatetime($navigationValues['t']);

		$navigationValues['startDate'] = DateUtil :: getDatetime($this->backToFirstDOW(DateUtil :: parseUIDateTime($weekDay)));
		$navigationValues['endDate'] = DateUtil :: getDatetime($this->forwardToLastDOW(DateUtil :: parseUIDateTime($weekDay)));
		$navigationValues['sortOrder'] = 'ASC';
		// reset page limit for weeklist
		$navigationValues['modvars']['itemsperpage'] = '-1';

		// Get all matching events
		$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items = array ();

		$exports = array ();
		foreach ($items as $key => $item)
		{
			$exports[] = $item['eventid'];
		}

		SessionUtil :: setVar('crpCalendar_export_events', $exports);
		SessionUtil :: setVar('crpCalendar_choosed_time', $navigationValues['t']);

		// expand days array
		$this->expandFirstDOW(DateUtil :: parseUIDateTime($weekDay), $days);
		$this->expandLastDOW(DateUtil :: parseUIDateTime($weekDay), $days);

		$daysexpanded = $days;

		$monthDays = DateUtil :: getMonthDates($date['m'], $date['y']);

		// for style purpose
		foreach ($days as $kday => $day)
		{
			if (!in_array($day, $monthDays))
				unset ($days[$kday]);
		}

		$today = DateUtil :: getDatetime(time());

		return $this->ui->userWeekList($items, $days, $daysexpanded, $navigationValues['t'], $date, $navigationValues['startDate'], $navigationValues['endDate'], $today, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * Page with day's events list
	 *
	 * @return string html
	 */
	function listDayEvents()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'day_view');
		SessionUtil :: setVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'day_view'));

		$navigationValues = $this->collectNavigationFromInput();
		SessionUtil :: delVar('crpCalendar_export_events');

		$date = $this->timeToDMY($navigationValues['t']);

		$day = DateUtil :: getDatetime($navigationValues['t']);
		$tomorrow = DateUtil :: getDatetime(mktime(0, 0, 0, $date['m'], $date['d'] + 1, $date['y']));

		$navigationValues['startDate'] = DateUtil :: getDatetime(DateUtil :: parseUIDateTime($day));
		$navigationValues['endDate'] = DateUtil :: getDatetime(DateUtil :: parseUIDateTime($tomorrow));
		$navigationValues['sortOrder'] = 'ASC';

		// reset page limit for daylist counter
		if ($navigationValues['modvars']['daylist_categorized'])
		{
			$navigationValues['modvars']['itemsperpage'] = '-1';
			$navigationValues['startnum'] = '1';
		}
		// all matching events
		$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $navigationValues);

		if (!$items)
			$items = array ();

		$exports = array ();
		foreach ($items as $key => $item)
		{
			$exports[] = $item['eventid'];
			$options = array ();

			// subscribe to event
			if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation'))
			{
				if (!$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
					$options[] = crpCalendar :: buildLinkArray(__("Add partecipation", $this->dom), $item, 'user');
				else
					$options[] = crpCalendar :: buildLinkArray(__("Delete partecipation", $this->dom), $item, 'user');
			}

			// edit, copy, delete
			if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']))
			{
				$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
				$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
				if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
					$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
			}
			elseif ($this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])) $options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'user');

			// Add the calculated menu options to the item array
			$items[$key]['options'] = $options;
		}

		SessionUtil :: setVar('crpCalendar_export_events', $exports);
		SessionUtil :: setVar('crpCalendar_choosed_time', $navigationValues['t']);

		$today = DateUtil :: getDatetime(time());

		if ($navigationValues['modvars']['daylist_categorized'])
		{
			$cats = CategoryUtil :: getSubCategories($navigationValues['mainCat']);
			$userLang = ZLanguage :: getLanguageCode();
			foreach ($cats as $cat)
			{
				foreach ($items as $kitem => $vitem)
				{
					if ($cat['id'] == $vitem[__CATEGORIES__]['Main']['id'])
						$categorizedEvents[$vitem[__CATEGORIES__]['Main']['display_name'][$userLang]][] = $vitem;
				}
			}
			$items = $categorizedEvents;
		}

		return $this->ui->userDayList($items, $navigationValues['startnum'], $day, $navigationValues['t'], $date, $navigationValues['startDate'], $navigationValues['endDate'], $today, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * List event's partecipation by current user
	 */
	function listUserPartecipations()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'get_attendees');

		$navigationValues = $this->collectNavigationFromInput();
		$navigationValues['uid'] = (int) FormUtil :: getPassedValue('uid', pnUserGetVar('uid'), 'GET');
		//
		$items = pnModAPIFunc('crpCalendar', 'user', 'getall_partecipations', $navigationValues);

		$rows = array ();
		$exports = array ();
		foreach ($items as $kevent => $item)
		{
			$options = array ();
			// display link
			$options[] = crpCalendar :: buildLinkArray(__("View", $this->dom), $item, 'user');

			// edit, copy, delete
			if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']))
			{
				$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
				$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
				if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
					$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
			}
			elseif ($this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])) $options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'user');

			// Add the calculated menu options to the item array
			$item['options'] = $options;
			$rows[] = $item;
			$exports[] = $item['eventid'];
		}

		SessionUtil :: setVar('crpCalendar_export_events', $exports);

		return $this->ui->userPartecipations($rows, $navigationValues['uid'], $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * List overall attendance
	 */
	function listAttendees()
	{
		SessionUtil :: setVar('crpCalendar_choosed_view', 'get_attendees');

		$navigationValues = $this->collectNavigationFromInput();
		// TODO : -1 -> $navigationValues['modvars'] to be changed when Zikula ticket #49 is resolved
		$items = $this->dao->getEventPartecipations(null, $navigationValues['startnum'], array (
			'itemsperpage',
			'-1'
		), null, 'A', 'DESC', null, 'uid');

		$rows = array ();
		$exports = array ();
		foreach ($items as $kevent => $item)
		{
			$options = array ();
			$options[] = crpCalendar :: buildLinkArray(__("attendance to events", $this->dom), $item, 'user');

			$options[] = array (
				'url' => pnModURL('Profile', 'user', 'view', array (
					'uid' => $item['uid']
				)),
				'image' => 'personal.gif',
				'title' => _VIEW
			);

			// Add the calculated menu options to the item array
			$item['options'] = $options;
			$rows[] = $item;
		}

		return $this->ui->attendeesList($rows, $navigationValues['category'], $navigationValues['mainCat'], $navigationValues['modvars']);
	}

	/**
	 * Display user page with event
	 *
	 * @return string html
	 */
	function displayEvent()
	{
		$eventid = FormUtil :: getPassedValue('eventid', isset ($args['eventid']) ? $args['eventid'] : null, 'REQUEST');
		$objectid = FormUtil :: getPassedValue('objectid', isset ($args['objectid']) ? $args['objectid'] : null, 'REQUEST');
		$cat = (string) FormUtil :: getPassedValue('cat', isset ($args['cat']) ? $args['cat'] : null, 'GET');
		if (!empty ($objectid))
		{
			$eventid = $objectid;
		}

		SessionUtil :: delVar('crpCalendar_export_events');

		// Get the event
		$item = $this->dao->getAdminData($eventid);

		// get all module vars
		$modvars = $this->modvars;

		// The return value of the function is checked here
		if ($item == false || ($item['obj_status'] == 'P' && !$this->authAction(ACCESS_EDIT, $item['cr_uid'], $item['eventid'], $item['title'])))
		{
			return LogUtil :: registerError(__('No such item found.', $this->dom));
		}

		$this->dao->updateCounter($eventid);

		$exports = array ();
		$exports = crpCalendar :: buildLinkArray(__("crpCalendar iCal event", $this->dom), $item, 'user');

		$item['exports'] = $exports;

		// edit, copy, delete
		if ($this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']))
		{
			$options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'admin');
			$options[] = crpCalendar :: buildLinkArray(__("Copy", $this->dom), $item, 'admin');
			if ($this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
				$options[] = crpCalendar :: buildLinkArray(__("Delete", $this->dom), $item, 'admin');
		}
		elseif ($this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])) $options[] = crpCalendar :: buildLinkArray(__("Edit", $this->dom), $item, 'user');

		// subscribe to event
		if (pnUserLoggedIn() && pnModGetVar('crpCalendar', 'enable_partecipation'))
		{
			if (!$this->dao->existPartecipation(pnUserGetVar('uid'), $item['eventid']))
				$options[] = crpCalendar :: buildLinkArray(__("Add partecipation", $this->dom), $item, 'user');
			else
				$options[] = crpCalendar :: buildLinkArray(__("Delete partecipation", $this->dom), $item, 'user');
		}

		$item['options'] = $options;

		// no pager for users
		$modvars['itemsperpage'] = -1;
		$item['partecipations'] = $this->dao->getEventPartecipations($item['eventid'], -1, $modvars, null, 'A', 'DESC', null, 'uid');

		$dayDate = DateUtil :: formatDatetime($item['start_date'], '%Y-%m-%d');

		SessionUtil :: setVar('crpCalendar_choosed_time', DateUtil :: makeTimestamp($dayDate));
		SessionUtil :: setVar('crpCalendar_return_url', pnModURL('crpCalendar', 'user', 'display', array (
			'eventid' => $item['eventid']
		)));

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations') && is_numeric($item['location']))
		{
			$item['location'] = pnModAPIFunc('locations', 'user', 'getLocationByID', array (
				'locationid' => $item['location']
			));
		}
		//SessionUtil::setVar('crpCalendar_choosed_view', 'display');
		//SessionUtil::setVar('crpCalendar_choosed_event', $item['eventid']);

		return $this->ui->userDisplay($eventid, $item, $modvars);
	}

	/**
	 * Display simple event
	 *
	 * @return string html
	 */
	function simpleDisplayEvent($eventid = null)
	{
		// Get the event
		$item = $this->dao->getAdminData($eventid);

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations') && is_numeric($item['location']))
		{
			$item['location'] = pnModAPIFunc('locations', 'user', 'getLocationByID', array (
				'locationid' => $item['location']
			));
		}

		// get all module vars
		$modvars = $this->modvars;

		// The return value of the function is checked here
		if ($item == false || ($item['obj_status'] == 'P' && !$this->authAction(ACCESS_EDIT, $item['cr_uid'], $item['eventid'], $item['title'])))
		{
			return LogUtil :: registerError(__('No such item found.', $this->dom));
		}

		$this->dao->updateCounter($eventid);

		return $this->ui->userSimpleDisplay($eventid, $item, $modvars);
	}

	/**
	 * Insert an event
	 *
	 * @return string html
	 */
	function newEvent()
	{
		$inputValues = $this->collectDataFromInput();
		$temp_values = array ();
		$temp_values = SessionUtil :: getVar('crpCalendar_temp_values');
		$avail = array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
			$avail = $this->getAvailableLocations();

		return $this->ui->newEvent($temp_values, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * Insert an event
	 *
	 * @return string html
	 */
	function submitEvent()
	{
		$inputValues = $this->collectDataFromInput();
		$temp_values = array ();
		$temp_values = SessionUtil :: getVar('crpCalendar_temp_values');
		$avail = array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
			$avail = $this->getAvailableLocations();

		return $this->ui->submitEvent($temp_values, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * update an event
	 *
	 * @param int $eventid item identifier
	 * @param array $inputValues array of updated values
	 *
	 * @return string html
	 */
	function createEvent()
	{
		$returnType = '';
		$returnFunc = 'view';
		$inputValues = array ();

		if (!$this->authAction(ACCESS_EDIT))
		{
			$returnType = 'user';
			$returnFunc = SessionUtil :: getVar('crpCalendar_choosed_view');
		}
		else
			$returnType = 'admin';

		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', $returnType, 'view'));

		$inputValues = $this->collectDataFromInput();
		SessionUtil :: setVar('crpCalendar_temp_values', $inputValues['event']);

		$startDate = $this->buildDate($inputValues['event']['startDay'], $inputValues['event']['startMonth'], $inputValues['event']['startYear']);
		$startTime = $this->buildTime($inputValues['event']['startMinute'], $inputValues['event']['startHour']);

		if (!$inputValues['event']['day_event'])
		{
			$endDate = $this->buildDate($inputValues['event']['endDay'], $inputValues['event']['endMonth'], $inputValues['event']['endYear']);
			$endTime = $this->buildTime($inputValues['event']['endMinute'], $inputValues['event']['endHour']);
		}
		else
		{
			$endDate = $startDate;
			$endTime = $this->buildTime('59', '23');
		}

		if (!$startDate || !$endDate)
		{
			LogUtil :: registerError(__('Invalid date', $this->dom));
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		}

		$inputValues['event']['start_date'] = $startDate . ' ' . $startTime;
		$inputValues['event']['end_date'] = $endDate . ' ' . $endTime;

		if (!$inputValues['event']['day_event'] && (strtotime($inputValues['event']['start_date']) > strtotime($inputValues['event']['end_date'])))
		{
			LogUtil :: registerError(__('Invalid date interval', $this->dom));
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		}

		// create if non existent
		if (!$this->dao->existEvent($inputValues['event']['title'], $inputValues['event']['location'], $inputValues['event']['start_date']))
			$eventid = $this->dao->create($inputValues);
		else
			LogUtil :: registerError(__('Event already existent', $this->dom));

		if (!$eventid)
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		}

		// if multiple creation is enabled
		if ($inputValues['serial']['startDay'] && $inputValues['modvars']['multiple_insert'])
		{
			$serialHourDiff = $inputValues['event']['endHour'] - $inputValues['event']['startHour'];
			$serialMinuteDiff = $inputValues['event']['endMinute'] - $inputValues['event']['startMinute'];

			foreach ($inputValues['serial']['startDay'] as $kserial => $vserial)
			{
				$serialStartDate = $this->buildDate($vserial, $inputValues['serial']['startMonth'][$kserial], $inputValues['serial']['startYear'][$kserial]);
				if (!$inputValues['event']['day_event'])
				{
					// calculate end translation
					$serialDayDiff = $inputValues['event']['endDay'] - $inputValues['event']['startDay'];
					$serialMonthDiff = $inputValues['event']['endMonth'] - $inputValues['event']['startMonth'];
					$serialYearDiff = $inputValues['event']['endYear'] - $inputValues['event']['startYear'];
					$serialEndDate = $this->buildDate($vserial + $serialDayDiff, $inputValues['serial']['startMonth'][$kserial] + $serialMonthDiff, $inputValues['serial']['startYear'][$kserial] + $serialYearDiff);
				}
				else
					$serialEndDate = $serialStartDate;

				$serialStartTime = $this->buildTime($inputValues['serial']['startMinute'][$kserial], $inputValues['serial']['startHour'][$kserial]);
				$serialEndTime = $this->buildTime($inputValues['serial']['startMinute'][$kserial] + $serialMinuteDiff, $inputValues['serial']['startHour'][$kserial] + $serialHourDiff);

				$inputValues['event']['start_date'] = $serialStartDate . ' ' . $serialStartTime;
				$inputValues['event']['end_date'] = $serialEndDate . ' ' . $serialEndTime;

				// don't create in the same day
				if (!$this->dao->existEvent($inputValues['event']['title'], $inputValues['event']['location'], $inputValues['event']['start_date']))
					$serialid = $this->dao->create($inputValues);
			}
		}

		// notify by mail if not an admin
		if (!$this->authAction(ACCESS_EDIT) && $inputValues['modvars']['crpcalendar_notification'])
			$this->notifyByMail($inputValues, $eventid);

		// all went fine
		LogUtil :: registerStatus(__('Done! Item created.', $this->dom) . ' ' . (($returnType == 'user' && $inputValues['modvars']['submitted_status'] != 'A') ? _CRPCALENDAR_WAITING : ''));

		if ($inputValues['reenter'])
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'new'));
		else
			SessionUtil :: delVar('crpCalendar_temp_values');

		return pnRedirect(pnModURL('crpCalendar', $returnType, $returnFunc));
	}

	/**
	 * Modify an event
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function modifyEvent()
	{
		$inputValues = $this->collectDataFromInput();

		// Get the event
		$item = $this->dao->getAdminData($inputValues['eventid']);

		if ($item == false)
		{
			return LogUtil :: registerError(__('No such item found.', $this->dom));
		}

		// Security check
		if (!$this->authAction(ACCESS_ADD, $item['cr_uid'], $item['eventid'], $item['title']) && (!$this->authAction(ACCESS_EDIT, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		$avail = array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
		{
			if (is_numeric($item['location']))
			{
				$item['locations'] = $item['location'];
				unset ($item['location']);
			}
			$avail = $this->getAvailableLocations();
		}

		return $this->ui->modifyEvent($item, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * Modify an event
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function editEvent()
	{
		$inputValues = $this->collectDataFromInput();

		// Get the event
		$item = $this->dao->getAdminData($inputValues['eventid']);

		if ($item == false)
		{
			return LogUtil :: registerError(__('No such item found.', $this->dom));
		}

		// Security check
		if (!$this->authAction(ACCESS_MODERATE, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid']))
		{
			return LogUtil :: registerPermissionError();
		}

		$avail = array ();

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
		{
			if (is_numeric($item['location']))
			{
				$item['locations'] = $item['location'];
				unset ($item['location']);
			}
			$avail = $this->getAvailableLocations();
		}

		return $this->ui->editEvent($item, $inputValues['mainCat'], $inputValues['modvars'], $avail);
	}

	/**
	 * update an event
	 *
	 * @param int $eventid item identifier
	 * @param array $inputValues array of updated values
	 *
	 * @return string html
	 */
	function updateEvent()
	{
		$inputValues = array ();
		$returnFunc = 'view';

		if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_EDIT))
		{
			$returnType = 'user';
			$returnFunc = SessionUtil :: getVar('crpCalendar_choosed_view');
		}
		else
			$returnType = 'admin';

		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', $returnType, 'view'));

		$inputValues = $this->collectDataFromInput();

		// Security check
		if (!$this->authAction(ACCESS_ADD) && !($this->authAction(ACCESS_MODERATE) && $this->isAuthor($inputValues['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		$startDate = $this->buildDate($inputValues['event']['startDay'], $inputValues['event']['startMonth'], $inputValues['event']['startYear']);
		$startTime = $this->buildTime($inputValues['event']['startMinute'], $inputValues['event']['startHour']);

		if (!$inputValues['event']['day_event'])
		{
			$endDate = $this->buildDate($inputValues['event']['endDay'], $inputValues['event']['endMonth'], $inputValues['event']['endYear']);
			$endTime = $this->buildTime($inputValues['event']['endMinute'], $inputValues['event']['endHour']);
		}
		else
		{
			$endDate = $startDate;
			$endTime = $this->buildTime('59', '23');
		}

		if (!$startDate || !$endDate)
		{
			LogUtil :: registerError(__('Invalid date', $this->dom));
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'modify', array (
				'eventid' => $inputValues['eventid']
			)));
		}

		$inputValues['event']['start_date'] = $startDate . ' ' . $startTime;
		$inputValues['event']['end_date'] = $endDate . ' ' . $endTime;

		if (!$inputValues['event']['day_event'] && (strtotime($inputValues['event']['start_date']) > strtotime($inputValues['event']['end_date'])))
		{
			LogUtil :: registerError(__('Invalid date interval', $this->dom));
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'modify', array (
				'eventid' => $inputValues['eventid']
			)));
		}

		if (!$this->dao->update($inputValues))
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', $returnType, 'modify', array (
				'eventid' => $inputValues['eventid']
			)));
		}

		// all went fine
		LogUtil :: registerStatus(__('Done! Item updated.', $this->dom));

		return pnRedirect(pnModURL('crpCalendar', $returnType, $returnFunc));
	}

	/**
	 * Clone an event
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function cloneEvent()
	{
		$inputValues = $this->collectDataFromInput();

		// Get the event
		$item = $this->dao->getAdminData($inputValues['eventid']);

		if ($item == false)
		{
			return LogUtil :: registerError(__('No such item found.', $this->dom));
		}

		// change values
		$inputValues['event'] = array (
			'title' => __('Copy of', $this->dom) . ' ' . $item['title'],
			'urltitle' => null,
			'location' => $item['location'],
			'url' => $item['url'],
			'contact' => $item['contact'],
			'organiser' => $item['organiser'],
			'__CATEGORIES__' => array (
				'Main' => $item['__CATEGORIES__']['Main']['id']
			),
			'event_text' => $item['event_text'],
			'start_date' => $item['start_date'],
			'end_date' => $item['end_date'],
			'obj_status' => $item['obj_status'],
			'day_event' => $item['day_event']
		);

		$inputValues['image'] = array (
			'id' => $item['image']['id']
		);
		$inputValues['document'] = array (
			'id' => $item['document']['id']
		);

		// Security check
		if (!$this->authAction(ACCESS_ADD) && !($this->authAction(ACCESS_EDIT, $item['cr_uid'], $item['eventid'], $item['title']) && $this->isAuthor($item['eventid'])))
		{
			return LogUtil :: registerPermissionError();
		}

		if (!$this->dao->cloneEvent($inputValues))
		{
			// Error
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'view'));
		}

		// all went fine
		LogUtil :: registerStatus(__('Done! Item created.', $this->dom));
		SessionUtil :: delVar('crpCalendar_temp_values');

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Delete an event
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function deleteEvent()
	{
		$eventid = FormUtil :: getPassedValue('eventid', null);

		// Get the event
		$item = $this->dao->getAdminData($eventid);

		if ($item == false)
		{
			return LogUtil :: registerError(__('No such item found.', $this->dom));
		}

		// Security check
		if (!$this->authAction(ACCESS_DELETE, $item['cr_uid'], $item['eventid'], $item['title']))
		{
			return LogUtil :: registerPermissionError();
		}

		return $this->ui->deleteEvent($eventid);
	}

	/**
	 * Delete afile
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function deleteFile()
	{
		$eventid = FormUtil :: getPassedValue('eventid', null, 'GET');
		$file_type = FormUtil :: getPassedValue('file_type', null, 'GET');

		// Security check
		if (!$this->authAction(ACCESS_ADD) && !($this->authAction(ACCESS_EDIT) && $this->isAuthor($eventid)))
		{
			return LogUtil :: registerPermissionError();
		}

		if ($this->dao->deleteFile($file_type, $eventid))
			LogUtil :: registerStatus(__('Done! Item deleted.', $this->dom));

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Delete an event
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function removeEvent()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', 'admin', 'view'));

		$eventid = FormUtil :: getPassedValue('eventid', null);

		// Security check
		if (!$this->authAction(ACCESS_DELETE))
		{
			return LogUtil :: registerPermissionError();
		}

		// Delete the page
		if ($this->dao->removeEvent($eventid))
			LogUtil :: registerStatus(__('Done! Item deleted.', $this->dom));

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Change item status
	 *
	 * @param int $eventid item identifier
	 * @param string $obj_status active or pending
	 *
	 * @return string html
	 */
	function changeStatus()
	{
		$eventid = FormUtil :: getPassedValue('eventid', null);
		$obj_status = FormUtil :: getPassedValue('obj_status', null);

		if ($obj_status == 'P' || $obj_status == 'A')
		{
			($obj_status == 'A') ? $obj_status = 'P' : $obj_status = 'A';
			if (!$this->dao->updateStatus($eventid, $obj_status))
				LogUtil :: registerError(__('Error! Update attempt failed.', $this->dom));
			else
				LogUtil :: registerStatus(__('Done! Item updated.', $this->dom));
		}
		else
			LogUtil :: registerError(__('Error! Update attempt failed.', $this->dom));

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Add event partecipation
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function addPartecipation()
	{
		$eventid = FormUtil :: getPassedValue('eventid', null);

		if (!$this->dao->addPartecipation(pnUserGetVar('uid'), $eventid))
			LogUtil :: registerError(__('Error! Update attempt failed.', $this->dom));
		else
			LogUtil :: registerStatus(__('Done! Item updated.', $this->dom));

		return pnRedirect(SessionUtil :: getVar('crpCalendar_return_url'));
	}

	/**
	 * Delete event partecipation
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function deletePartecipation()
	{
		$eventid = FormUtil :: getPassedValue('eventid', null);

		if (!$this->dao->deletePartecipation(pnUserGetVar('uid'), $eventid))
			LogUtil :: registerError(__('Error! Update attempt failed.', $this->dom));
		else
			LogUtil :: registerStatus(__('Done! Item updated.', $this->dom));

		return pnRedirect(SessionUtil :: getVar('crpCalendar_return_url'));
	}

	/**
	 * Modify module's configuration
	 */
	function modifyConfig()
	{
		// get all module vars
		$modvars = $this->modvars;

		return $this->ui->modifyConfig($modvars, $this->gd);
	}

	/**
	 * Update module's configuration
	 */
	function updateConfig()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', 'admin', 'view'));

		$navigationValues = $this->collectNavigationFromInput();

		// Update module variables
		$itemsperpage = (int) FormUtil :: getPassedValue('itemsperpage', 25, 'POST');
		if ($itemsperpage < 1)
		{
			$itemsperpage = 25;
		}
		pnModSetVar('crpCalendar', 'itemsperpage', $itemsperpage);
		$enablecategorization = (bool) FormUtil :: getPassedValue('enablecategorization', false, 'POST');
		pnModSetVar('crpCalendar', 'enablecategorization', $enablecategorization);
		$addcategorytitletopermalink = (bool) FormUtil :: getPassedValue('addcategorytitletopermalink', false, 'POST');
		pnModSetVar('crpCalendar', 'addcategorytitletopermalink', $addcategorytitletopermalink);
		// RSS
		$crpcalendar_enable_rss = (bool) FormUtil :: getPassedValue('crpcalendar_enable_rss', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_enable_rss', $crpcalendar_enable_rss);
		$crpcalendar_show_rss = (bool) FormUtil :: getPassedValue('crpcalendar_show_rss', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_show_rss', $crpcalendar_show_rss);
		$crpcalendar_rss = (string) FormUtil :: getPassedValue('crpcalendar_rss', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_rss', $crpcalendar_rss);
		$file_dimension = (int) FormUtil :: getPassedValue('file_dimension', 35000, 'POST');
		pnModSetVar('crpCalendar', 'file_dimension', $file_dimension);
		$image_width = (int) FormUtil :: getPassedValue('image_width', 100, 'POST');
		pnModSetVar('crpCalendar', 'image_width', $image_width);
		$crpcalendar_use_gd = (bool) FormUtil :: getPassedValue('crpcalendar_use_gd', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_use_gd', $crpcalendar_use_gd);
		$crpcalendar_userlist_image = (bool) FormUtil :: getPassedValue('crpcalendar_userlist_image', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_userlist_image', $crpcalendar_userlist_image);
		$userlist_width = (int) FormUtil :: getPassedValue('userlist_width', 32, 'POST');
		pnModSetVar('crpCalendar', 'userlist_width', $userlist_width);
		$crpcalendar_theme = FormUtil :: getPassedValue('crpcalendar_theme', 'default', 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_theme', $crpcalendar_theme);
		$crpcalendar_start_year = (int) FormUtil :: getPassedValue('crpcalendar_start_year', date("Y"), 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_start_year', $crpcalendar_start_year);
		$document_dimension = (int) FormUtil :: getPassedValue('document_dimension', 55000, 'POST');
		pnModSetVar('crpCalendar', 'document_dimension', $document_dimension);
		$enable_partecipation = (bool) FormUtil :: getPassedValue('enable_partecipation', false, 'POST');
		pnModSetVar('crpCalendar', 'enable_partecipation', $enable_partecipation);
		$enable_locations = (bool) FormUtil :: getPassedValue('enable_locations', false, 'POST');
		pnModSetVar('crpCalendar', 'enable_locations', $enable_locations);
		$daylist_categorized = (bool) FormUtil :: getPassedValue('daylist_categorized', false, 'POST');
		pnModSetVar('crpCalendar', 'daylist_categorized', $daylist_categorized);
		$yearlist_categorized = (bool) FormUtil :: getPassedValue('yearlist_categorized', false, 'POST');
		pnModSetVar('crpCalendar', 'yearlist_categorized', $yearlist_categorized);
		$crpcalendar_notification = FormUtil :: getPassedValue('crpcalendar_notification', null, 'POST');
		if ($crpcalendar_notification && !pnVarValidate($crpcalendar_notification, 'email'))
		{
			LogUtil :: registerError(__('Invalid notification e-mail address', $this->dom));
			return pnRedirect(pnModUrl('crpCalendar', 'admin', 'modifyconfig'));
		}
		pnModSetVar('crpCalendar', 'crpcalendar_notification', $crpcalendar_notification);
		$mandatory_description = (bool) FormUtil :: getPassedValue('mandatory_description', false, 'POST');
		pnModSetVar('crpCalendar', 'mandatory_description', $mandatory_description);
		$submitted_status = FormUtil :: getPassedValue('submitted_status', 'P', 'POST');
		pnModSetVar('crpCalendar', 'submitted_status', $submitted_status);
		$multiple_insert = (bool) FormUtil :: getPassedValue('multiple_insert', false, 'POST');
		pnModSetVar('crpCalendar', 'multiple_insert', $multiple_insert);
		$enable_formicula = (bool) FormUtil :: getPassedValue('enable_formicula', false, 'POST');
		pnModSetVar('crpCalendar', 'enable_formicula', $enable_formicula);
		$crpcalendar_weekday_start = (int) FormUtil :: getPassedValue('crpcalendar_weekday_start', false, 'POST');
		pnModSetVar('crpCalendar', 'crpcalendar_weekday_start', $crpcalendar_weekday_start);
		$complete_date_format = FormUtil :: getPassedValue('complete_date_format', '%d/%m/%Y - %H:%M', 'POST');
		pnModSetVar('crpCalendar', 'complete_date_format', $complete_date_format);
		$only_date_format = FormUtil :: getPassedValue('only_date_format', '%d/%m/%Y', 'POST');
		pnModSetVar('crpCalendar', 'only_date_format', $only_date_format);
		$subcategory_listing = (bool) FormUtil :: getPassedValue('subcategory_listing', false, 'POST');
		pnModSetVar('crpCalendar', 'subcategory_listing', $subcategory_listing);

		// Let any other modules know that the modules configuration has been updated
		pnModCallHooks('module', 'updateconfig', 'crpCalendar', array (
			'module' => 'crpCalendar'
		));

		// the module configuration has been updated successfuly
		LogUtil :: registerStatus(__('Done! Module configuration updated.', $this->dom));

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * Collect navigation input value
	 *
	 * @param int $startnum pager offset
	 * @param int $category current category if specified
	 * @param bool clear clean category
	 * @param bool $ignoreml ignore multilanguage
	 *
	 * @return array input values
	 */
	function collectNavigationFromInput()
	{
		// Get parameters from whatever input we need.
		$startnum = (int) FormUtil :: getPassedValue('startnum', null);
		$category = FormUtil :: getPassedValue('events_category', null);
		$active = FormUtil :: getPassedValue('events_status', null);
		$clear = FormUtil :: getPassedValue('clear');
		$month = FormUtil :: getPassedValue('Date_Month', null);
		$year = FormUtil :: getPassedValue('Date_Year', null);
		$day = FormUtil :: getPassedValue('Date_Day', null);
		$typeList = FormUtil :: getPassedValue('typeList', null);
		$viewForm = FormUtil :: getPassedValue('viewForm', 'table');

		if ($day && $month && $year)
			SessionUtil :: setVar('crpCalendar_choosed_time', DateUtil :: makeTimestamp($year . '-' . $month . '-' . $day));

		$t = FormUtil :: getPassedValue('t', null);
		if (!$t && SessionUtil :: getVar('crpCalendar_choosed_time'))
			$t = SessionUtil :: getVar('crpCalendar_choosed_time');
		elseif (!$t) $t = time();

		if ($clear)
		{
			$active = null;
			$category = null;
			$t = time();
			$typeList = null;
			$viewForm = 'table';
		}

		$ignoreml = FormUtil :: getPassedValue('ignoreml', true);
		$sortOrder = FormUtil :: getPassedValue('sortOrder', (SessionUtil :: getVar('crpCalendar_choosed_view') == 'mont_view') ? 'ASC' : 'DESC');
		$sortColumn = FormUtil :: getPassedValue('sortColumn', 'start_date');

		// load the category registry util
		if (!($class = Loader :: loadClass('CategoryRegistryUtil')))
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		if (!($class = Loader :: loadClass('CategoryUtil')))
			pn_exit('Unable to load class [CategoryUtil] ...');

		$mainCat = CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');
		$cats = CategoryUtil :: getCategoriesByParentID($mainCat);

		// get all module vars
		$modvars = $this->modvars;

		$data = compact('startnum', 'category', 'active', 'clear', 'ignoreml', 'mainCat', 'cats', 'modvars', 'sortOrder', 't', 'typeList', 'viewForm', 'sortColumn');

		return $data;
	}

	/**
	 * Collect data from insert/modification form
	 *
	 * @param int $eventid item identifier
	 * @param int $objectid object identifier
	 * @param array page item values
	 *
	 * @return array collection of values
	 */
	function collectDataFromInput()
	{
		$eventid = FormUtil :: getPassedValue('eventid', null);
		$objectid = FormUtil :: getPassedValue('objectid', null);

		if (!empty ($objectid))
		{
			$eventid = $objectid;
		}

		$event = FormUtil :: getPassedValue('event', null, 'POST');
		$event_image = FormUtil :: getPassedValue('event_image', null, 'FILES');
		$event_document = FormUtil :: getPassedValue('event_document', null, 'FILES');
		$serial = FormUtil :: getPassedValue('serial', null, 'POST');
		$reenter = FormUtil :: getPassedValue('reenter', null, 'POST');

		(!empty ($event['objectid'])) ? $event['eventid'] = $event['objectid'] : '';
		(!$event['day_event']) ? $event['day_event'] = 0 : '';

		// load the category registry util
		if (!($class = Loader :: loadClass('CategoryRegistryUtil')))
		{
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		}
		$mainCat = CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');

		// get all module vars
		$modvars = $this->modvars;

		$data = compact('eventid', 'objectid', 'event', 'event_image', 'event_document', 'mainCat', 'modvars', 'serial', 'reenter');

		return $data;
	}

	/**
	 * Build date
	 *
	 * @param int $day day number
	 * @param int $month month number
	 * @param int $year year value
	 *
	 * @return string yyyy-mm-dd
	 */
	function buildDate($day = null, $month = null, $year = null)
	{
		if (!checkdate($month, $day, $year))
			$dateBuild = null;
		else
			$dateBuild = $year . '-' . $month . '-' . $day;

		return $dateBuild;
	}

	/**
	 * Build date
	 *
	 * @param int $day day number
	 * @param int $month month number
	 *
	 * @return string hh:mm:00
	 */
	function buildTime($minute, $hour)
	{
		$timeBuild = $hour . ':' . $minute . ':00';

		return $timeBuild;
	}

	/**
	 * Return event last modified date
	 *
	 * @param int $eventid identifier
	 *
	 * return bool
	 */
	function getEventDate($eventid = null, $dateType = null)
	{
		$modifiedDate = false;
		$modifiedDate = $this->dao->getEventDate($eventid, $dateType);

		return $modifiedDate;
	}

	/**
	 * Retrieve info about a rss module plugin
	 *
	 *
	 * */
	function loadRSS($modname, $modrss, $id_lang = '')
	{
		$result = false;

		$modinfo = pnModGetInfo(pnModGetIdFromName($modname));
		$moddir = 'modules/' . pnVarPrepForOS($modinfo['directory']) . '/pnrss';
		$langdir = 'modules/' . pnVarPrepForOS($modinfo['directory']) . '/pnlang';
		$infofunc = "{$modname}_{$modrss}rss_info";

		if (!$id_lang)
			$id_lang = ZLanguage :: getLanguageCode();

		// Load the rss
		$incfile = $modrss . '.php';
		$filepath = $moddir . '/' . pnVarPrepForOS($incfile);
		if (!file_exists($filepath))
			return false;

		include_once $filepath;

		// Load the RSS language files
		$currentlangfile = $langdir . '/' . pnVarPrepForOS($id_lang) . '/' . pnVarPrepForOS($incfile);
		$defaultlangfile = $langdir . '/' . pnVarPrepForOS(pnConfigGetVar('language')) . '/' . pnVarPrepForOS($incfile);
		if (file_exists($currentlangfile))
			include_once $currentlangfile;
		elseif (file_exists($defaultlangfile)) include_once $defaultlangfile;

		// get the rss info
		if (function_exists($infofunc) && ($info = $infofunc ()) && ($info !== false))
		{
			// set the module and keys for the new rss
			if (!isset ($info['module']))
				$info['module'] = $modname;
			$info['mid'] = pnModGetIDFromName($$modname);

			// Initialise rss if required (new-style)
			$initfunc = "{$modname}_{$modrss}rss_init";
			if (function_exists($initfunc))
			{
				$initfunc ();
			}
			$result = $info;
		}
		//
		return $result;
	}

	/**
	 * Display RSS content
	 *
	 * */
	function getFeed()
	{
		$result = '';

		// Return if not enabled
		if (!pnModGetVar('crpCalendar', 'crpcalendar_enable_rss'))
			return $result;
		//	header("Content-Type: text/plain\n\n");	//debug

		$rssinfo = $this->loadRSS('crpCalendar', 'events', ZLanguage :: getLanguageCode());

		$feedfunc = "crpCalendar_events_rss_feed";
		$list = array ();
		if (function_exists($feedfunc))
			$list = $feedfunc ();

		$data['xml_lang'] = substr(ZLanguage :: getLanguageCode(), 0, 2);
		$data['publ_date'] = date('Y-m-d H:i:s', time());
		$selfurl = pnModUrl('crpCalendar', 'user', 'getfeed');
		$data['selfurl'] = $selfurl;
		$data['format'] = pnModGetVar('crpCalendar', 'crpcalendar_rss');
		$sitename = pnConfigGetVar('sitename');

		Header("Content-Disposition: inline; filename=" . $sitename . "_events.xml");
		if ($data['format'] == __('ATOM', $this->dom))
			header("Content-Type: application/atom+xml\n\n");
		else
			header("Content-Type: application/rss+xml\n\n");
		//	header("Content-Type: text/xml\n\n");

		// get all module vars
		$modvars = $this->modvars;

		$result = $this->ui->drawFeed($data, $list, $modvars);
		echo $result;
		pnShutDown();
	}

	/**
	 * Display iCal content
	 *
	 * */
	function getICal()
	{
		$eventid = FormUtil :: getPassedValue('eventid', isset ($args['eventid']) ? $args['eventid'] : null, 'REQUEST');

		$result = '';

		$selfurl = pnModUrl('crpCalendar', 'user', 'display', array (
			'eventid' => $eventid
		));
		$result .= $this->ui->drawICalHeader($selfurl) . "\n";

		// Get the event
		$item = $this->dao->getAdminData($eventid, false);

		$data['uid'] = pnUserGetVar('uname', $item['cr_uid']);
		$data['summary'] = $item['title'];
		$data['dtstart'] = $item['start_date'];
		$data['dtend'] = $item['end_date'];
		$data['description'] = $item['event_text'];
		$data['categories'] = $item['__CATEGORIES__']['Main']['display_name']['' . ZLanguage :: getLanguageCode() . ''];
		$data['dtstamp'] = $item['cr_date'];
		$data['location'] = $item['location'];
		$data['url'] = $item['url'];
		$data['eventid'] = $eventid;

		$result .= $this->ui->drawICal($data) . "\n";
		$result .= $this->ui->drawICalFooter();

		Header("Content-Disposition: attachment; filename={$eventid}.ics");
		Header("Content-Type: text/calendar; charset={_CHARSET}\n\n");
		//header("Content-Type: text/plain\n\n");

		echo $result;
		pnShutDown();
	}

	/**
	 * Display iCal content
	 *
	 * */
	function listICal()
	{
		$exports = SessionUtil :: getVar('crpCalendar_export_events');

		$result = '';

		$selfurl = pnModUrl('crpCalendar', 'user', 'view');
		$result .= $this->ui->drawICalHeader($selfurl) . "\n";
		// Get the events
		foreach ($exports as $eventid)
		{
			$item = $this->dao->getAdminData($eventid, false);

			$data['uid'] = pnUserGetVar('uname', $item['cr_uid']);
			$data['summary'] = $item['title'];
			$data['dtstart'] = $item['start_date'];
			$data['dtend'] = $item['end_date'];
			$data['description'] = $item['event_text'];
			$data['categories'] = $item['__CATEGORIES__']['Main']['display_name']['' . ZLanguage :: getLanguageCode() . ''];
			$data['dtstamp'] = $item['cr_date'];
			$data['location'] = $item['location'];
			$data['url'] = $item['url'];
			$data['eventid'] = $eventid;

			$result .= $this->ui->drawICal($data) . "\n";
		}

		$result .= $this->ui->drawICalFooter();

		Header("Content-Disposition: attachment; filename=crpCalendar.ics");
		Header("Content-Type: text/calendar; charset={_CHARSET}\n\n");
		//header("Content-Type: text/plain\n\n");

		echo $result;
		pnShutDown();
	}

	/**
	 * Generate thumbnail for image
	 *
	 * @param int id doc
	 * @param string width doc
	 * @return nothing
	 */
	function getThumbnail()
	{
		$eventid = FormUtil :: getPassedValue('eventid', isset ($args['eventid']) ? $args['eventid'] : null, 'REQUEST');
		$width = FormUtil :: getPassedValue('width', isset ($args['width']) ? $args['width'] : null, 'REQUEST');
		if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
			pnShutDown();

		$file = $this->dao->getFile($eventid, 'image', true);
		$modifiedDate = $this->dao->getEventDate($eventid, 'lu_date');

		if (!(is_numeric($width) && $width > 0))
			$width = pnModGetVar('crpCalendar', 'image_width');
		$params['width'] = $width; //	$params['append_ghosted']=1;
		$params['modifiedDate'] = $modifiedDate;

		crpCalendar :: imageGetThumbnail($file['binary_data'], $file['name'], $file['content_type'], $params);
	}

	function imageGetThumbnail(& $pSrcImage, $filename, $content_type, $params = array ())
	{
		// we need a timestamp
		$server_etag = DateUtil :: makeTimestamp($params['modifiedDate']);
		$server_date = gmdate('D, d M Y H:i:s', $server_etag) . " GMT";

		// Check cached versus modified date
		$client_etag = $_SERVER['HTTP_IF_NONE_MATCH'];
		$client_date = $_SERVER['HTTP_IF_MODIFIED_SINCE'];

		if (($client_etag == $server_etag) && (!$client_date || ($client_date == $server_date)))
		{
			header("HTTP/1.1 304 Not Modified");
			header("ETag: $server_etag");
			pnShutDown();
		}
		else
		{
			header("Expires: " . gmdate('D, d M Y H:i:s', time() + 24 * 3600) . " GMT");
			header('Pragma: cache');
			header('Cache-Control: public, must-revalidate');
			header("ETag: $server_etag");
			header("Last-Modified: " . gmdate('D, d M Y H:i:s', $server_etag) . " GMT");
			header("Content-Type: $content_type");
			header("Content-Disposition: inline; filename=thumb_{$filename}");
		}

		/***************************************************************************/

		$alphaThreshold = isset ($params['alpha_threshold']) ? $params['alpha_threshold'] : 64;
		$appendGhosted = $params['append_ghosted'];
		//
		$srcImage = imagecreatefromstring($pSrcImage);

		if ($srcImage)
		{
			//obtain the original image Height and Width
			$srcWidth = imagesx($srcImage);
			$srcHeight = imagesy($srcImage);

			$newWidth = isset ($params['width']) ? $params['width'] : $srcWidth;

			$destWidth = round($newWidth, '0');
			$destHeight = round(($srcHeight / $srcWidth) * $newWidth, '0');

			// creating the destination image with the new Width and Height
			if (!$appendGhosted)
				$destImage = imagecreatetruecolor($destWidth, $destHeight);
			else
				$destImage = imagecreatetruecolor($destWidth, 2 * $destHeight);

			//png transparency
			switch ($content_type)
			{
				case 'image/png' :
				case 'image/x-png' :
					imageantialias($destImage, true);
					imagealphablending($destImage, false);
					imagesavealpha($destImage, true);
					$transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 80);
					imagefill($destImage, 0, 0, $transparent);
					break;

				case 'image/gif' :
					imageantialias($destImage, true);
					imagealphablending($destImage, false);
					break;
			}

			//copy the srcImage to the destImage
			imagecopyresampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

			//
			if ($appendGhosted)
			{
				imagecopyresampled($destImage, $srcImage, 0, $destHeight, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);

				$ghostImage = imagecreatetruecolor($destWidth, $destHeight);
				imageantialias($ghostImage, true);
				imagealphablending($ghostImage, false);
				imagesavealpha($ghostImage, true);
				$whitetrasp = imagecolorallocatealpha($ghostImage, 255, 255, 255, 0);
				imagefill($ghostImage, 0, 0, $whitetrasp);
				imagecopymerge($destImage, $ghostImage, 0, $destHeight, 0, 0, $destWidth, $destHeight, 50);
				if ($content_type == 'image/png')
				{ //	problems mergins transparent png.. need to restore original pixel transparency
					for ($x = 0; $x < $destWidth; $x++)
						for ($y = 0; $y < $destHeight; $y++)
						{
							$srcPixel = imagecolorsforindex($destImage, imagecolorat($destImage, $x, $y));
							$destPixel = imagecolorsforindex($destImage, imagecolorat($destImage, $x, $y + $destHeight));
							imagesetpixel($destImage, $x, $y + $destHeight, imagecolorallocatealpha($destImage, $destPixel['red'], $destPixel['green'], $destPixel['blue'], $srcPixel['alpha']));
						}

				}
				imagedestroy($ghostImage);

			}

			//save output to a buffer
			ob_start();

			//create the image
			switch ($content_type)
			{
				case 'image/gif' :
					imagetruecolortopalette($destImage, true, 255);
					//
					if (imagecolortransparent($srcImage) >= 0)
					{
						$maskImage = imagecreatetruecolor($destWidth, $destHeight);
						imageantialias($maskImage, true);
						imagealphablending($maskImage, false);
						imagecopyresampled($maskImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight);
						//
						$transp = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
						imagecolortransparent($destImage, $transp);
						//
						for ($x = 0; $x < $destWidth; $x++)
							for ($y = 0; $y < $destHeight; $y++)
							{
								$c = imagecolorsforindex($maskImage, imagecolorat($maskImage, $x, $y));
								if ($c['alpha'] >= $alphaThreshold)
								{
									imagesetpixel($destImage, $x, $y, $transp);
									if ($appendGhosted)
										imagesetpixel($destImage, $x, $y + $destHeight, $transp);
								}
							}
						imagedestroy($maskImage);
					}
					//
					imagegif($destImage);
					break;

				case 'image/jpeg' :
				case 'image/pjpeg' :
					imagejpeg($destImage);
					break;

				case 'image/png' :
				case 'image/x-png' :
					imagepng($destImage);
					break;
			}

			//copy output buffer to string
			$resizedImage = ob_get_contents();

			//clear output buffer that was saved
			ob_end_clean();

			//fre the memory used for the images
			imagedestroy($srcImage);
			imagedestroy($destImage);

			echo $resizedImage;
			pnShutDown();
		}
	}

	/**
	 * Retrieve time of first DayOfWeek from given time
	 *
	 * @param long time starting time
	 * @return long time for first DayOfWeek
	 * */
	function backToFirstDOW($time)
	{
		$dow = date('w', $time);
		$counter = 0;

		while ($dow != pnModGetVar('crpCalendar', 'crpcalendar_weekday_start'))
		{
			$date = $this->timeToDMY($time);
			$time = mktime(0, 0, 0, $date['m'], $date['d'] - 1, $date['y']);
			$dow = date('w', $time);
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return $time;
	}

	/**
	 * Retrieve time of last DayOfWeek from given time
	 *
	 * @param long time starting time
	 * @return long time for last DayOfWeek
	 * */
	function forwardToLastDOW($time)
	{
		$dow = date('w', $time);
		$counter = 0;

		switch (pnModGetVar('crpCalendar', 'crpcalendar_weekday_start'))
		{
			case "1";
				$limit = 0;
				break;
			default :
				$limit = 6;
				break;
		}

		while ($dow != $limit)
		{
			$date = $this->timeToDMY($time);
			$time = mktime(23, 59, 59, $date['m'], $date['d'] + 1, $date['y']);
			$dow = date('w', $time);
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return $time;
	}

	/**
	 * Retrieve time of first DayOfWeek from given time
	 *
	 * @param long time starting time
	 * @return long time for first DayOfWeek
	 * */
	function expandFirstDOW($time, & $days)
	{
		$dow = date('w', $time);
		$counter = 0;

		while ($dow != pnModGetVar('crpCalendar', 'crpcalendar_weekday_start'))
		{
			$date = $this->timeToDMY($time);
			$time = mktime(0, 0, 0, $date['m'], $date['d'] - 1, $date['y']);
			$dow = date('w', $time);
			array_unshift($days, DateUtil :: getDatetime($time));
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return;
	}

	/**
	 * Retrieve time of last DayOfWeek from given time
	 *
	 * @param long time starting time
	 * @return long time for last DayOfWeek
	 * */
	function expandLastDOW($time, & $days)
	{
		$dow = date('w', $time);
		$counter = 0;

		switch (pnModGetVar('crpCalendar', 'crpcalendar_weekday_start'))
		{
			case "1";
				$limit = 0;
				break;
			default :
				$limit = 6;
				break;
		}

		while ($dow != $limit)
		{
			$date = $this->timeToDMY($time);
			$time = mktime(0, 0, 0, $date['m'], $date['d'] + 1, $date['y']);
			$dow = date('w', $time);
			array_push($days, DateUtil :: getDatetime($time));
			//
			$counter++;
			//
			if ($counter > 7)
				break;
		}
		return;
	}

	/**
	 * Transform time into a d,m,y array
	 *
	 * @param int time
	 * @return array data splitted into d,m,y components
	 * */
	function timeToDMY($t)
	{
		$result['d'] = date('d', $t);
		$result['m'] = date('m', $t);
		$result['y'] = date('Y', $t);
		//
		return $result;
	}

	/**
	 * Retrieve array of places from locations
	 */
	function getAvailableLocations()
	{
		$locations = $this->dao->getLocations();

		return $locations;
	}

	/**
	 * send an email notification
	 */
	function notifyByMail($inputValues = array (), $eventid = null)
	{
		// send the email
		$render = & pnRender :: getInstance('crpCalendar', false);
		$render->assign('inputValues', $inputValues['event']);
		$render->assign('eventid', $eventid);
		$body = $render->fetch('crpcalendar_user_notify_newevent.htm');

		$subject = __('Notification of Event Creation', $this->dom);
		$to = pnModGetVar('crpCalendar', 'crpcalendar_notification');
		;

		$result = pnModAPIFunc('Mailer', 'user', 'sendmessage', array (
			'toaddress' => $to,
			'subject' => $subject,
			'body' => $body,
			'html' => true,
			'fromname' => pnConfigGetVar('sitename'),
			'fromaddress' => pnConfigGetVar('adminmail'),
			'replytoname' => pnConfigGetVar('sitename'),
			'replytoaddress' => pnConfigGetVar('adminmail')
		));
	}

	/**
	 * Display form for import ical files
	 * */
	function importIcs()
	{
		// load the category registry util
		if (!($class = Loader :: loadClass('CategoryRegistryUtil')))
		{
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		}
		$mainCat = CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');

		return $this->ui->drawImportForm($mainCat);
	}

	/**
	 * Create from a new import
	 *
	 */
	function createIcs()
	{
		$result = '';
		$docPerm = array ();
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', 'admin', 'view'));
		else
		{
			$id_category = FormUtil :: getPassedValue('id_category', null, 'POST');
			$ics_file = FormUtil :: getPassedValue('ics_file', null, 'FILES');
			$array_import = $this->parse_ical($ics_file['tmp_name']);

			if (!is_array($array_import))
			{
				LogUtil :: registerError(constant($array_import));
			}

			foreach ($array_import as $kevent => $vevent)
			{
				($kevent != '0') ? $this->dao->createFromIcs($vevent, $kevent, $id_category) : '';
			}
		}

		LogUtil :: registerStatus(__('Done! Item created.', $this->dom));
		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * iCalendar Parser
	 *
	 * Pass it the name (minus extension) of a .ics file and it'll return
	 * a multidimensional array of calendar events on success, a string on some errors
	 * and nothing on others as it's quite incomplete.
	 *
	 * CANT_OPEN_FILE ==> Function can't read ics file
	 * INVALID_FILETYPE ==> The file isn't recognised as an ics file
	 *
	 * @param string $calendar The file to open
	 * @return array An array of calendar events
	 */
	function parse_ical($calendar)
	{
		// Open calendar
		$fp = @ fopen($calendar, 'r');
		if (!$fp)
		{
			return 'CANT_OPEN_FILE';
		}

		// Read first line
		$buffer_temp = fgets($fp, 1024);

		// Check to see if this is actually an iCalendar file.
		if (trim(strtoupper($buffer_temp)) != 'BEGIN:VCALENDAR')
		{
			return 'INVALID_FILETYPE';
		}

		// And set a few variables...
		$cal = array ();
		$event = 0;
		$cal[0]['generator'] = 'Menial iCal Parser';

		// Set variable to enable sorting of array.
		$cal[0]['start_unix'] = '';
		$flag_valarm = false;

		// Now loop through line by line...
		while (!feof($fp))
		{
			// Save prev read-ahead data
			$buffer = $buffer_temp;

			// Then read ahead again
			$buffer_temp = fgets($fp, 1024);

			// Remove newlines from new buffer
			$buffer_temp = ereg_replace("[\r\n]", '', $buffer_temp);

			// Check to see if this is a multi-line part,
			// (they begin with a space)
			while (substr($buffer_temp, 0, 1) == " ")
			{
				// If yes, process it and keep reading until
				// new buffer line doesn't begin with " ".
				$buffer = $buffer . substr($buffer_temp, 1);
				$buffer_temp = fgets($fp, 1024);
				$buffer_temp = ereg_replace("[\r\n]", '', $buffer_temp);
			}

			// Begin parsing directives in current buffer
			switch ($buffer)
			{
				// New event
				case 'BEGIN:VEVENT' :
					$attendee = 1;
					$event = $event +1;
					$cal[$event] = array ();
					break;

					// End current event
				case 'END:VEVENT' :

					break;

					// Begin alarm for current event
				case 'BEGIN:VALARM' :
					$flag_valarm = true;
					break;

					// End alarm for current event
				case 'END:VALARM' :
					$flag_valarm = false;
					break;

				default :
					$line = '';
					//Break up the line. We want indices 1 and 2. Not 0.
					ereg("([^:]+):(.*)", $buffer, $line);

					// Need to both trim the field down and keep a copy for later processing.
					$field = $line[1];
					$data = $line[2];
					//****************echo '>>'.$data.'<br />';*****************//
					// Need to keep a copy of each property line.
					$property = $field;

					// Trim the property values off the last ';'
					$property_p = strpos($property, ';');
					if ($property_p != false)
					{
						$property = substr($property, 0, $property_p);

						// And make it upper-case
						$property = strtoupper($property);
					}

					switch ($property)
					{

						/********** CALENDER INFO ***********/
						// Calendar Name
						case 'X-WR-CALNAME' :
							$cal[0]['name'] = $data;
							break;

							// Calendar Description
						case 'X-WR-CALDESC' :
							$cal[0]['description'] = stripslashes($data);
							break;

							// Main timezone of calendar
						case 'X-WR-TIMEZONE' :
							$cal[0]['timezone'] = $data;
							break;

							// Calendar ID
						case 'X-WR-RELCALID' :
							$cal[0]['relcalid'] = $data;
							break;

							// Calendar Scale
						case 'CALSCALE' :
							$cal[0]['calscale'] = $data;
							break;

							// iCalendar Version
						case 'VERSION' :
							$cal[0]['cal_version'] = $data;
							break;

							// Product ID of file generator
						case 'PRODID' :
							$cal[0]['prodid'] = stripslashes($data);
							break;

							/********** END CALENDER INFO ***********/

							/********** EVENT INFO ***********/

							// Unique ID of event
						case 'UID' :
							$cal[$event]['uid'] = $data;
							break;

							// Start time of event
						case 'DTSTART' :
							$date = '';
							$data = str_replace('T', '', $data);

							if (ereg('DTSTART;VALUE=DATE', $field))
							{
								// ALL-DAY EVENT
								ereg('([0-9]{4})([0-9]{2})([0-9]{2})', $data, $date);

								// UNIX timestamps can't deal with pre 1970 dates
								if ($date[1] <= 1970)
								{
									$date[1] = 1971;
								}

								$cal[$event]['day_event'] = 1;
								$cal[$event]['start_date'] = $date[1] . '-' . $date[2] . '-' . $date[3];
								$cal[$event]['start_time'] = 0;
								$cal[$event]['start_unix'] = mktime(0, 0, 0, $date[2], $date[3], $date[1]);
							}
							else
							{
								// TIME LIMITED EVENT
								ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

								// UNIX timestamps can't deal with pre 1970 dates
								if ($date[1] <= 1970)
								{
									$date[1] = 1971;
								}

								$cal[$event]['day_event'] = 0;
								$cal[$event]['start_date'] = $date[1] . '-' . $date[2] . '-' . $date[3];
								$cal[$event]['start_time'] = $date[4] . ':' . $date[5];
								$cal[$event]['start_unix'] = mktime($date[4], $date[5], $date[6], $date[2], $date[3], $date[1]);
							}
							break;

							// Start time of event
						case 'CREATED' :
							$date = '';
							$data = str_replace('T', '', $data);
							$data = str_replace('Z', '', $data);

							// TIME LIMITED EVENT
							ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

							$cal[$event]['cr_date'] = $date[1] . '-' . $date[2] . '-' . $date[3] . ' ' . $date[4] . ':' . $date[5];
							break;

							// Start time of event
						case 'LAST-MODIFIED' :
							$date = '';
							$data = str_replace('T', '', $data);
							$data = str_replace('Z', '', $data);

							// TIME LIMITED EVENT
							ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

							$cal[$event]['lu_date'] = $date[1] . '-' . $date[2] . '-' . $date[3] . ' ' . $date[4] . ':' . $date[5];
							break;

							// End time of event
						case 'DTEND' :

							$data = str_replace('T', '', $data);

							// TIME LIMITED EVENT
							ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

							// UNIX timestamps can't deal with pre 1970 dates
							if ($date[1] <= 1970)
							{
								$date[1] = 1971;
							}

							$cal[$event]['end_date'] = $date[1] . '-' . $date[2] . '-' . $date[3];
							$cal[$event]['end_time'] = $date[4] . ':' . $date[5];
							$cal[$event]['end_unix'] = mktime($date[4], $date[5], $date[6], $date[2], $date[3], $date[1]);
							break;

							// Timestamp of event
						case 'DTSTAMP' :

							$data = str_replace('T', '', $data);
							$data = str_replace('Z', '', $data);

							// TIME LIMITED EVENT
							ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

							// UNIX timestamps can't deal with pre 1970 dates
							if ($date[1] <= 1970)
							{
								$date[1] = 1971;
							}

							$cal[$event]['stamp_date'] = $date[1] . '-' . $date[2] . '-' . $date[3];
							$cal[$event]['stamp_time'] = $date[4] . ':' . $date[5];
							$cal[$event]['stamp_unix'] = mktime($date[4], $date[5], $date[6], $date[2], $date[3], $date[1]);
							break;

							// Summary of event
						case 'SUMMARY' :
							$data = str_replace("\\n", '<br />', $data);
							$data = str_replace("\\r", '<br />', $data);
							$data = stripslashes($data);
							$data = htmlentities($data);
							$cal[$event]['title'] = $data;
							break;

							// Event description
						case 'DESCRIPTION' :
							$data = str_replace("\\n", '<br />', $data);
							$data = str_replace("\\r", '<br />', $data);
							$data = stripslashes($data);
							$data = htmlentities($data);
							if ($flag_valarm == false)
							{
								$cal[$event]['event_text'] = $data;
							}
							else
							{
								$cal[$event]['alarm']['event_text'] = $data;
							}
							break;

							//NOT REMOTELY COMPLIANT WITH
							//ICALENDAR RFC. READ AND DO IT AGAIN!

							// List of attendees
						case 'ATTENDEE' :

							$att = explode(';', $buffer);
							foreach ($att as $value)
							{
								$att_content = explode('=', $value);

								switch ($att_content[0])
								{
									// Calendar User Type
									case 'CUTYPE' :
										$cal[$event]['attendee'][$attendee]['cutype'] = $att_content[1];
										break;

										//
									case 'MEMBER' :

										break;

										//
									case 'PARTSTAT' :

										break;

										//
									case 'ROLE' :
										$cal[$event]['attendee'][$attendee]['role'] = $att_content[1];
										break;

										// RSVP? True/False
									case 'RSVP' :
										$cal[$event]['attendee'][$attendee]['rsvp'] = $att_content[1];
										break;

										//
									case 'SENT-BY' :

										break;

										// Common Name
									case 'CN' :
										$cal[$event]['attendee'][$attendee]['name'] = $att_content[1];
										break;

										//
									case 'DIR' :

										break;

										//
									case 'DELEGATED-TO' :

										break;

										//
									case 'DELEGATED-FROM' :

										break;
								}

							}

							$attendee++;
							unset ($temp, $att, $value);
							break;

							// List of organiser
						case 'ORGANIZER' :

							$org = explode(';', $buffer);
							foreach ($org as $value)
							{
								$org_content = explode('=', $value);

								switch ($org_content[0])
								{
									// Common Name
									case 'CN' :
										$cal[$event]['organiser'] = $org_content[1];
										break;

										//
									case 'MAILTO' :
										$cal[$event]['contact'] = $org_content[1];
										break;
								}

							}

							unset ($temp, $org, $value);
							break;

							// URL of event
						case 'URL' :
							$cal[$event]['url'] = $data;
							break;

							// Location of event
						case 'LOCATION' :
							$cal[$event]['location'] = $data;
							break;

							// Status of event
						case 'STATUS' :
							// TODO: check this out
							//$cal[$event]['obj_status'] = $data;
							$cal[$event]['obj_status'] = 'A';
							break;

							/********** ALARM INFO ***********/

							// Alarm Action
						case 'ACTION' :
							$cal[$event]['alarm']['action'] = $data;
							break;

							// When should the alarm go off?
						case 'TRIGGER' :
							$cal[$event]['alarm']['trigger'] = $data;
							break;

							// Alarm attachment
						case 'ATTACH' :
							$cal[$event]['alarm']['attach'] = $data;

							$temp = explode(';', $field);
							$temp = explode('=', $temp[1]);
							$cal[$event]['alarm']['attach_value'] = $temp[1];
							unset ($temp);
							break;

							// Alarm description handler is joined
							// with event description handler

							/********** END ALARM INFO ***********/

							/********** RECURRENCE RULE INFO ***********/
							/* TODO: implement with recursion
							 case 'RRULE' :
							 $cal[$event]['rrule'] = array ();
							 $rrule = explode(';', $data);

							 foreach ($rrule as $value)
							 {
							 $rrule_content = explode('=', $value);

							 switch ($rrule_content[0])
							 {
							 // Frequency of repeating event
							  case 'FREQ' :
							  $cal[$event]['rrule']['freq'] = $rrule_content[1];
							  break;

							  // Interval to repeat the frequency
							   // eg. FREQ=WEEKLY;INTERVAL=2 ==> repeat every 2 weeks
							    case 'INTERVAL' :
							    $cal[$event]['rrule']['interval'] = $rrule_content[1];
							    break;

							    // Number of times to repeat event
							     case 'COUNT' :
							     $cal[$event]['rrule']['count'] = $rrule_content[1];
							     break;

							     // Repeat event until date/time
							      case 'UNTIL' :
							      $data = str_replace('T', '', $data);
							      $data = str_replace('Z', '', $data);

							      // TIME LIMITED EVENT
							       ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

							       // UNIX timestamps can't deal with pre 1970 dates
							        if ($date[1] <= 1970)
							        {
							        $date[1] = 1971;
							        }

							        $cal[$event]['rrule']['until_date'] = $date[1] . '-' . $date[2] . '-' . $date[3];
							        $cal[$event]['rrule']['until_time'] = $date[4] . $date[5];
							        $cal[$event]['rrule']['until_unix'] = mktime($date[4], $date[5], $date[6], $date[2], $date[3], $date[1]);
							        break;

							        case 'BYSECOND' :
							        $cal[$event]['rrule']['bysecond'] = $rrule_content[1];
							        break;

							        case 'BYMINUTE' :
							        $cal[$event]['rrule']['byminute'] = $rrule_content[1];
							        break;

							        case 'BYHOUR' :
							        $cal[$event]['rrule']['byhour'] = $rrule_content[1];
							        break;

							        case 'BYDAY' :
							        $cal[$event]['rrule']['byday'] = $rrule_content[1];
							        break;

							        case 'BYMONTH' :
							        $cal[$event]['rrule']['bymonth'] = $rrule_content[1];
							        break;

							        case 'BYYEAR' :
							        $cal[$event]['rrule']['byyear'] = $rrule_content[1];
							        break;

							        case 'BYMONTHDAY' :
							        $cal[$event]['rrule']['bymonthday'] = $rrule_content[1];
							        break;

							        case 'BYYEARDAY' :
							        $cal[$event]['rrule']['byyearday'] = $rrule_content[1];
							        break;

							        case 'BYWEEKNO' :
							        $cal[$event]['rrule']['byweekno'] = $rrule_content[1];
							        break;

							        // Day that work week start
							         case 'WKST' :
							         $cal[$event]['rrule']['wkst'] = $rrule_content[1];
							         break;

							         //
							          case 'BYSETPOS' :
							          $cal[$event]['rrule']['bysetpos'] = $rrule_content[1];
							          break;

							          }
							          }
							          unset ($rrule, $rrule_content, $value);
							          break;
							          */

							/********** RECURRENCE RULE INFO ***********/

							/* TODO: implement with recursion
							 case 'EXDATE' :
							 $data = str_replace('T', '', $data);

							 if (ereg('EXDATE;VALUE=DATE', $field))
							 {
							 // ALL-DAY EVENT
							  ereg('([0-9]{4})([0-9]{2})([0-9]{2})', $data, $date);

							  // UNIX timestamps can't deal with pre 1970 dates
							   if ($date[1] <= 1970)
							   {
							   $date[1] = 1971;
							   }

							   $cal[$event]['exdate'][] = $date[1] . '-' . $date[2] . '-' . $date[3];
							   $cal[$event]['exdate_unix'][] = mktime(0, 0, 0, $date[2], $date[3], $date[1]);
							   }
							   else
							   {
							   // TIME LIMITED EVENT
							    ereg('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})([0-9]{0,2})', $data, $date);

							    // UNIX timestamps can't deal with pre 1970 dates
							     if ($date[1] <= 1970)
							     {
							     $date[1] = 1971;
							     }

							     $cal[$event]['exdate'][] = $date[1] . '-' . $date[2] . '-' . $date[3];
							     $cal[$event]['exdate_unix'][] = mktime($date[4], $date[5], $date[6], $date[2], $date[3], $date[1]);
							     }
							     break;
							     */
					}
					/********** END EVENT INFO ***********/
					break;

			}

		}

		fclose($fp);

		// Puts events in order using UNIX timestamp as
		// a comparison point.
		usort($cal, 'compare');

		// Unset "padding" varible
		unset ($cal[0]['start_unix']);

		// Return parsed data.
		return $cal;
	}

	// The function that does the comparing to
	// order events.
	function compare($a, $b)
	{
		return strnatcasecmp($a['start_unix'], $b['start_unix']);
	}

	/**
	 * Purge events from database
	 * */
	function purgeEvents()
	{
		return $this->ui->drawPurgeEvents();
	}

	/**
	 * Delete events
	 *
	 * @param int $eventid item identifier
	 *
	 * @return string html
	 */
	function removePurge()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpCalendar', 'admin', 'view'));

		$event = FormUtil :: getPassedValue('event', null, 'POST');

		// Security check
		if (!$this->authAction(ACCESS_DELETE))
		{
			return LogUtil :: registerPermissionError();
		}

		// Delete the page
		if ($this->dao->removePurge($event['endDay'], $event['endMonth'], $event['endYear']))
			LogUtil :: registerStatus(__('Done! Item deleted.', $this->dom));

		return pnRedirect(pnModURL('crpCalendar', 'admin', 'view'));
	}

	/**
	 * build an array link by define
	 *
	 * @param string $mlname link define
	 * @param array $item values
	 * @param string $actiontype user level action
	 *
	 * @return array link
	 */
	function buildLinkArray($mlname = null, $item = array (), $actiontype = null)
	{

		switch ($mlname)
		{
			case "".__("View", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', $actiontype, 'display', array (
						'eventid' => $item['eventid']
					)),
					'image' => 'demo.gif',
					'title' => __("View", $this->dom)
				);
				break;
			case "".__("Add partecipation", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', $actiontype, 'add_partecipation', array (
						'eventid' => $item['eventid']
					)),
					'image' => 'add_user.gif',
					'title' => __("Add partecipation", $this->dom)
				);
				break;
			case "".__("Delete partecipation", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', $actiontype, 'delete_partecipation', array (
						'eventid' => $item['eventid']
					)),
					'image' => 'delete_user.gif',
					'title' => __("Delete partecipation", $this->dom)
				);
				break;
			case "".__("Edit", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', $actiontype, 'modify', array (
						'eventid' => $item['eventid']
					)),
					'image' => 'xedit.gif',
					'title' => __("Edit", $this->dom)
				);
				break;
			case "".__("Copy", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', $actiontype, 'clone', array (
						'eventid' => $item['eventid']
					)),
					'image' => 'editcopy.gif',
					'title' => __("Copy", $this->dom)
				);
				break;
			case "".__("Delete", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', $actiontype, 'delete', array (
						'eventid' => $item['eventid']
					)),
					'image' => '14_layer_deletelayer.gif',
					'title' => __("Delete", $this->dom)
				);
				break;
			case "".__("attendance to events", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', 'user', 'get_partecipations', array (
						'uid' => $item['uid']
					)),
					'image' => 'vcalendar.gif',
					'title' => __("attendance to events", $this->dom)
				);
				break;
			case "".__("crpCalendar iCal event", $this->dom)."" :
				$linkArray = array (
					'url' => pnModURL('crpCalendar', 'user', 'getICal', array (
						'eventid' => $item['eventid']
					)),
					'image' => 'ical.gif',
					'title' => __("crpCalendar iCal event", $this->dom)
				);
				break;
			default :
				$linkArray = "";
				break;
		}

		return $linkArray;
	}

}