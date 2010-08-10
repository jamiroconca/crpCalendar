<?php

/**
 * crpTalk
 *
 * @copyright (c) 2010 Daniele Conca
 * @link http://code.zikula.org/crptalk Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpTalk
 */

Loader :: includeOnce('modules/crpTalk/pnclass/crpTalk.php');

/**
 * main module function
 *
 * @return blob image
 */
function crpTalk_admin_main()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpTalk::', '::', ACCESS_ADMIN))
	{
		return LogUtil :: registerPermissionError();
	}

	$talk= new crpTalk();
	return $talk->modifyConfig();
}

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function crpTalk_admin_updateconfig()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpTalk::', '::', ACCESS_ADMIN))
	{
		return LogUtil :: registerPermissionError();
	}

	$talk= new crpTalk();
	return $talk->updateConfig();
}
?>
