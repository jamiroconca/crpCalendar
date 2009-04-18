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

/**
 * search plugin info
 *
 * @return array info
 **/
function crpCalendar_searchapi_info()
{
	return array (
		'title' => 'crpCalendar',
		'functions' => array (
			'crpCalendar' => 'search'
		)
	);
}

/**
 * search form component
 *
 * @return html form
 **/
function crpCalendar_searchapi_options($args)
{
	if (SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		// Create output object - this object will store all of our output so that
		// we can return it easily when required
		$pnRender = new pnRender('crpCalendar');
		$pnRender->assign('active', (isset ($args['active']) && isset ($args['active']['crpCalendar'])) || (!isset ($args['active'])));
		return $pnRender->fetch('crpcalendar_search_options.htm');
	}
	return '';

}

/**
 * search plugin main function
 *
 * @return bool true on success
 **/
function crpCalendar_searchapi_search($args)
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	pnModLangLoad('crpCalendar', 'search');
	$startnum = (int) $args['startnum'];
	$total = (int) $args['total'];
	$bool = (string) $args['bool'];
	$q = (string) $args['q'];
	$numlimit = (int) $args['numlimit'];
	$archive = (bool) $args['archive'];
	if (!isset ($startnum) || !is_numeric($startnum))
	{
		$startnum = 1;
	}
	if (isset ($total) && !is_numeric($total))
	{
		unset ($total);
	}

	// get the db and table info
	pnModDBInfoLoad('Search');
	$pntable = pnDBGetTables();

	$w = search_split_query($q);
	$flag = false;
	$eventstable = $pntable['crpcalendar'];
	$eventscolumn = $pntable['crpcalendar_column'];
	$searchTable = & $pntable['search_result'];
	$searchColumn = & $pntable['search_result_column'];
	$nowDate = DateUtil :: getDatetime();

	$query = "SELECT $eventscolumn[title] as title,
	                   $eventscolumn[event_text] as event_text,
										 $eventscolumn[location] as location,
										 $eventscolumn[cr_date] as cr_date,
										 $eventscolumn[start_date] as start_date,
										 $eventscolumn[eventid] as eventid FROM $eventstable WHERE ";
	foreach ($w as $word)
	{
		if ($flag)
		{
			switch ($bool)
			{
				case 'AND' :
					$query .= ' AND ';
					break;
				case 'OR' :
				default :
					$query .= ' OR ';
					break;
			}
		}
		$query .= '(';
		$query .= "$eventscolumn[title] LIKE '" . DataUtil :: formatForStore($word) . "' OR ";
		$query .= "$eventscolumn[location] LIKE '" . DataUtil :: formatForStore($word) . "' OR ";
		$query .= "$eventscolumn[organiser] LIKE '" . DataUtil :: formatForStore($word) . "' OR ";
		$query .= "$eventscolumn[event_text] LIKE '" . DataUtil :: formatForStore($word) . "' ";
		$query .= ')';
		$flag = true;
	}
	(!$archive) ? $query .= " AND $eventscolumn[start_date] > '" . DataUtil :: formatForStore($nowDate) . "' " : '';
	$query .= " AND $eventscolumn[obj_status]='A' ";
	$query .= " ORDER BY $eventscolumn[start_date] DESC";

	$result = DBUtil :: executeSQL($query);
	if (!$result)
	{
		return LogUtil :: registerError(_GETFAILED);
	}
	//$result = $dbconn->SelectLimit($query, pnModGetVar('Search', 'itemsperpage'), $startnum-1);

	$insertSql = "INSERT INTO $searchTable
									  ($searchColumn[title],
									   $searchColumn[text],
										 $searchColumn[extra],
									   $searchColumn[module],
									   $searchColumn[created],
									   $searchColumn[session])
									VALUES ";

	$sessionId = session_id();

	// Process the result set and insert into search result table
	for (; !$result->EOF; $result->MoveNext())
	{
		$event = $result->GetRowAssoc(2);

		// add date to title
		$event['title'] .= " / " . DataUtil :: formatForStore(DateUtil :: formatDatetime($event['start_date'], '%d-%m-%Y'));

		if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations') && is_numeric($event['location']))
		{
			$location = pnModAPIFunc('locations', 'user', 'getLocationByID', array (
				'locationid' => $event['location']
			));
			$event['location'] = $location['name'] . ", " . $location['street'] . " " . $location['city'];
		}

		// add location to title
		if ($event['location'])
			$event['title'] .= " - " . DataUtil :: formatForStore($event['location']);

		if (SecurityUtil :: checkPermission('crpCalendar::', "$event[cr_uid]:$event[title]:$event[eventid]", ACCESS_READ))
		{
			$sql = $insertSql . "(
			                  '" . DataUtil :: formatForStore($event['title']) . "',
			                  '" . DataUtil :: formatForStore($event['event_text']) . "',
			                  '" . DataUtil :: formatForStore($event['eventid']) . "',
			                  '" . 'crpCalendar' . "',
			                  '" . DataUtil :: formatForStore($event['cr_date']) . "',
			                  '" . DataUtil :: formatForStore($sessionId) . "')";
			$insertResult = DBUtil :: executeSQL($sql);
			if (!$insertResult)
			{
				return LogUtil :: registerError(_GETFAILED);
			}
		}
	}

	return true;
}

/**
 * do last minute access checking and assign URL to items
 *
 * both access checking and URL creation is ignored: access check has
 * already been done and there's no URL to the events.
 *
 * @return bool true
 */
function crpCalendar_searchapi_search_check(& $args)
{
	$datarow = & $args['datarow'];
	$eventid = $datarow['extra'];

	$datarow['url'] = pnModUrl('crpCalendar', 'user', 'display', array (
		'eventid' => $eventid
	));

	// True = has access
	return true;
}