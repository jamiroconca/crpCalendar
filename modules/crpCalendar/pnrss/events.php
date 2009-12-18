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
 * initialise rss feed
 *
 */
function crpCalendar_events_rss_init()
{

}

/**
 * get information on rss
 *
 * @return array info
 */
function crpCalendar_events_rss_info()
{
	return array (
		'name' => 'Events',
		'module' => 'crpCalendar',
		'long_descr' => 'Events Titles'
	);
}

/**
 * display rss
 *
 * @param array $rssinfo a rssinfo structure
 *
 * @return array feed list
 */
function crpCalendar_events_rss_feed($rssinfo)
{
	if (!pnModAvailable('crpCalendar'))
	{
		return;
	}

	// Security check
	if (!SecurityUtil :: checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil :: registerPermissionError();
	}

	// get the current language
	$currentlang = ZLanguage::getLanguageCode();

	// Break out options from our content field
	$vars['limit'] = 10;

	$apiargs['interval'] = $vars['limit'];
	$apiargs['startnum'] = 1;
	$apiargs['active'] = 1;
	$apiargs['modvars']['itemsperpage'] = '-1';
	$apiargs['sortOrder'] = 'DESC';

	if (pnModGetVar('crpCalendar','enablecategorization'))
	{
		// load the category registry util
		if (!($class= Loader :: loadClass('CategoryRegistryUtil')))
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		if (!($class= Loader :: loadClass('CategoryUtil')))
			pn_exit('Unable to load class [CategoryUtil] ...');

		$mainCat= CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');
		$category= (int) FormUtil :: getPassedValue('events_category', null, 'GET');
		if ($category)
		{
			$apiargs['mainCat'] = $mainCat;
			$apiargs['category'] = $category;
		}
	}

	// call the api
	$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $apiargs);

	//
	$list = array ();
	foreach ($items as $item)
	{
		$list[] = array (
			'title' => $item['title'],
			'link' => pnModURL('crpCalendar',
			'user',
			'display',
			array (
				'eventid' => $item['eventid']
			)
		), 'descr' => $item['event_text'], 'start_date' => $item['start_date'], 'end_date' => $item['end_date'], 'publ_date' => $item['cr_date'], 'author_uid' => $item['cr_uid']);
	}

	//
	return $list;
}
?>