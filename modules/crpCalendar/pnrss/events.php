<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @author Daniele Conca <jami at cremonapalloza dot org>
 * @license GNU/GPL - v.2
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
 */
function crpCalendar_events_rss_info()
{
    return array('name'       		 => 'Events',
                 'module'          => 'crpCalendar',
                 'long_descr'  		 => 'Events Titles');
}

/**
 * display rss
 *
 * @param        array       $rssinfo     a rssinfo structure
 * @return       output      the rendered rss
 */
function crpCalendar_events_rss_feed($rssinfo)
{
	if(!pnModAvailable('crpCalendar'))
	{
      return;
  }
  
  // Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
  
  // get the current language
  $currentlang = pnUserGetLang();

  // Break out options from our content field
  $vars['limit'] = 10;
  
  $apiargs['interval'] = $vars['limit'];
  $apiargs['startnum'] = 1;
  $apiargs['active'] = 1;
  $apiargs['modvars']['itemsperpage'] = 9999;
  $apiargs['sortOrder'] = 'DESC';

  // call the api
  $items = pnModAPIFunc('crpCalendar', 'user', 'getall', $apiargs);

  //
	$list = array();
	foreach($items as $item)
	{
		$list[] = array(
			'title'	=>	$item['title'],
			'link'	=>	pnModURL('crpCalendar','user','display', array('eventid'=>$item['eventid'])),
			'descr'	=>	$item['event_text'],
			'start_date'=> $item['start_date'],
			'end_date'=> $item['end_date'],
			'publ_date'	=>	$item['cr_date'],
			'author_uid'	=> $item['cr_uid']

		);
	}

	//
	return $list;
}

?>