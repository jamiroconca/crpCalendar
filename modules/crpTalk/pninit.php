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

function crpTalk_init()
{
	pnModSetVar('crpTalk', 'talk_widget_width', '400');
	pnModSetVar('crpTalk', 'talk_widget_height', '400');
	pnModSetVar('crpTalk', 'talk_chatback_hash_key', null);

	// Initialisation successful
	return true;
}

function crpLicense_upgrade($oldversion)
{
	switch ($oldversion)
	{
		case "0.1.0" :
			break;
	}
	// Update successful
	return true;
}

function crpLicense_delete()
{
	// Delete any module variables
	pnModDelVar('crpTalk');

	// Deletion successful
	return true;
}