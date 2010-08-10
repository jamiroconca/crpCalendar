<?php

/**
 * crpTalk
 *
 * @copyright (c) 2009, Daniele Conca
 * @link http://code.zikula.org/crptalk Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpTalk
 */

Loader :: includeOnce('modules/crpTalk/pnclass/crpTalkUI.php');

/**
 * crpTalk Object
 */
class crpTalk
{

	function crpTalk()
	{
		$this->ui= new crpTalkUI();
	}

	/**
	 * Display widget
	 */
	function displayWidget()
	{
		// get all module vars
		$modvars= pnModGetVar('crpTalk');

		return $this->ui->displayWidget($modvars);
	}

	/**
	 * Modify module's configuration
	 */
	function modifyConfig()
	{
		// get all module vars
		$modvars= pnModGetVar('crpTalk');

		return $this->ui->modifyConfig($modvars);
	}

	/**
	 * Update module's configuration
	 */
	function updateConfig()
	{
		// Confirm authorisation code
		if (!SecurityUtil :: confirmAuthKey())
			return LogUtil :: registerAuthidError(pnModURL('crpTalk', 'admin'));

		// Update module variables
		$talk_widget_width= (int) FormUtil :: getPassedValue('talk_widget_width', 400, 'POST');
		pnModSetVar('crpTalk', 'talk_widget_width', $talk_widget_width);
		$talk_widget_height= (int) FormUtil :: getPassedValue('talk_widget_height', 400, 'POST');
		pnModSetVar('crpTalk', 'talk_widget_height', $talk_widget_height);
		$talk_chatback_hash_key= FormUtil :: getPassedValue('talk_chatback_hash_key', null, 'POST');
		pnModSetVar('crpTalk', 'talk_chatback_hash_key', $talk_chatback_hash_key);

		// the module configuration has been updated successfuly
		LogUtil :: registerStatus(_CONFIGUPDATED);

		return pnRedirect(pnModURL('crpTalk', 'admin'));
	}

}