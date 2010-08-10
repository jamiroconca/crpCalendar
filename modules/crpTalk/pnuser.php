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

//Loader :: includeOnce('modules/crpTalk/pnincludes/gtalkStatus.class.php');
Loader :: includeOnce('modules/crpTalk/pnclass/crpTalk.php');

/**
 * main module function
 *
 * @return blob image
 */
function crpTalk_user_main()
{
	// Security check
	if (!SecurityUtil :: checkPermission('crpTalk::', '::', ACCESS_COMMENT) || !pnUserLoggedIn())
	{
		return LogUtil :: registerPermissionError();
	}

	$talk= new crpTalk();
	return $talk->displayWidget();

}
?>
