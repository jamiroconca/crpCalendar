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
 * Return an array of items to show in the your account panel
 *
 * @return   array   array of items, or false on failure
 */
function crpCalendar_accountapi_getall($args)
{
    if (!isset($args['uname'])) {
        if (!pnUserloggedIn()) {
            $uname = null;
        } else {
            $uname = pnUserGetVar('uname');
        }
    }		
		
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_COMMENT))
		$uname = null;
	  
  // Create an array of links to return
  if ($uname != null) 
  {
  	pnModLangLoad('crpCalendar');
    $items = array(array('url'     => pnModURL('crpCalendar', 'user', 'new'),
                         'module'  => 'crpCalendar',
                         'set'     => 'pnimages',
                         'title'   => _CRPCALENDAR_SUBMIT,
                         'icon'    => 'admin.gif'));
  } 
  else 
  	$items = null;

  // Return the items
  return $items;
}
