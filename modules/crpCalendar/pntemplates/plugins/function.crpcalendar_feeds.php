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
 * Smarty function to add rss link into header
 *
 * Example
 * <!--[crpcalendar_feeds]-->
 * 
 * @return void
 */
function smarty_function_crpcalendar_feeds($params, &$smarty)
{
	// Security check
	if (!SecurityUtil::checkPermission('crpCalendar::', '::', ACCESS_READ))
	{
		return LogUtil::registerPermissionError();
	}
	
	if (pnModGetVar('crpCalendar','enablecategorization') && pnModGetVar('crpCalendar','crpcalendar_enable_rss'))
	{
		// load the category registry util
		if (!($class= Loader :: loadClass('CategoryRegistryUtil')))
			pn_exit('Unable to load class [CategoryRegistryUtil] ...');
		if (!($class= Loader :: loadClass('CategoryUtil')))
			pn_exit('Unable to load class [CategoryUtil] ...');
		
		$mainCat= CategoryRegistryUtil :: getRegisteredModuleCategory('crpCalendar', 'crpcalendar', 'Main', '/__SYSTEM__/Modules/crpCalendar');
		$cats= CategoryUtil :: getCategoriesByParentID($mainCat);
		$userLang = pnUserGetLang();

		foreach ($cats as $cat)
		{
			PageUtil :: addVar('rawtext','<link rel="alternate" type="application/rss+xml" href="'.pnModUrl('crpCalendar','user','get_feed',array('events_category'=>$cat['id'])).'" title="'._CRPCALENDAR_RSS.' '.$cat['display_name'][$userLang].'" />');
		}
	}
	
 
  return;
}

?>
