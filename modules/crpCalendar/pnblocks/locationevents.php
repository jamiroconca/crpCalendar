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

Loader :: includeOnce('modules/crpCalendar/pnclass/crpCalendar.php');

/**
 * initialise block
 *
 */
function crpCalendar_locationeventsblock_init()
{
	// Security
	pnSecAddSchema('Locationeventsblock::', 'Block title::');
}

/**
 * get information on block
 *
 */
function crpCalendar_locationeventsblock_info()
{
	return array (
		'text_type' => 'crpEvents',
		'module' => 'crpCalendar',
		'text_type_long' => 'Location\'s events',
		'allow_multiple' => true,
		'form_content' => false,
		'form_refresh' => false,
		'show_preview' => true
	);
}

/**
 * display block
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the rendered bock
 */
function crpCalendar_locationeventsblock_display($blockinfo)
{
	// security check
	if (!SecurityUtil :: checkPermission('Locationeventsblock::', "$blockinfo[title]::", ACCESS_READ))
		return;

	if (!pnModAvailable('crpCalendar'))
		return;

	// get the current language
	$currentlang = pnUserGetLang();

	// Break out options from our content field
	$vars = pnBlockVarsFromContent($blockinfo['content']);

	if (!isset ($vars['limit']))
		$vars['limit'] = 10;

	if (!isset ($vars['numitems']))
		$vars['numitems'] = 10;

	if (!isset ($vars['bylocation']))
		$vars['bylocation'] = null;

	$apiargs['interval'] = $vars['limit'];
	$apiargs['startnum'] = 1;
	$apiargs['active'] = 'A';
	$apiargs['modvars']['itemsperpage'] = $vars['numitems'];
	$apiargs['sortOrder'] = 'ASC';
	$apiargs['bylocation'] = $vars['bylocation'];

	// call the api
	$items = pnModAPIFunc('crpCalendar', 'user', 'getall', $apiargs);

	// create the output object
	$pnRender = pnRender :: getInstance('crpCalendar', false);

	$pnRender->assign('events', $items);
	$pnRender->assign('interval', $vars['limit']);
	$pnRender->assign(pnModGetVar('crpCalendar'));

	$blockinfo['content'] = $pnRender->fetch('blocks/crpcalendar_block_events.htm');
	return pnBlockThemeBlock($blockinfo);
}

/**
 * modify block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       output      the bock form
 */
function crpCalendar_locationeventsblock_modify($blockinfo)
{
	// Break out options from our content field
	$vars = pnBlockVarsFromContent($blockinfo['content']);

	// Defaults
	if (empty ($vars['limit']))
		$vars['limit'] = 10;

	if (!isset ($vars['numitems']))
		$vars['numitems'] = 10;

	if (!isset ($vars['bylocation']))
		$vars['bylocation'] = null;

	// Create output object
	$pnRender = pnRender :: getInstance('crpCalendar', false);

	if (pnModAvailable('locations') && pnModGetVar('crpCalendar', 'enable_locations'))
		$avail = crpCalendar :: getAvailableLocations();

	// assign the block vars
	$pnRender->assign($vars);
	$pnRender->assign('avail', $avail);

	// Return the output that has been generated by this function
	return $pnRender->fetch('blocks/crpcalendar_block_locationevents_modify.htm');

}

/**
 * update block settings
 *
 * @param        array       $blockinfo     a blockinfo structure
 * @return       $blockinfo  the modified blockinfo structure
 */
function crpCalendar_locationeventsblock_update($blockinfo)
{
	// Get current content
	$vars = pnBlockVarsFromContent($blockinfo['content']);

	// alter the corresponding variable
	$vars['limit'] = (int) FormUtil :: getPassedValue('limit', null, 'POST');
	$vars['numitems'] = (int) FormUtil :: getPassedValue('numitems', null, 'POST');
	$vars['bylocation'] = FormUtil :: getPassedValue('bylocation', null);

	// write back the new contents
	$blockinfo['content'] = pnBlockVarsToContent($vars);

	// clear the block cache
	$pnRender = pnRender :: getInstance('crpCalendar');
	$pnRender->clear_cache('blocks/crpcalendar_block_events.htm');

	return $blockinfo;
}