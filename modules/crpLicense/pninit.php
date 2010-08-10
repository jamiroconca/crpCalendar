<?php

/**
 * crpLicense
 *
 * @copyright (c) 2009, Daniele Conca
 * @link http://code.zikula.org/crplicense Support and documentation
 * @author Daniele Conca <conca.daniele@gmail.com>
 * @license GNU/GPL - v.2.1
 * @package crpLicense
 */

function crpLicense_init()
{
	// create table
	if (!DBUtil :: createTable('crplicense'))
	{
		return false;
	}

	// create table
	if (!DBUtil :: createTable('crplicense_images'))
	{
		return false;
	}

	// create table
	if (!DBUtil :: createTable('crplicense_archive'))
	{
		return false;
	}

	// Create the index
	if (!DBUtil :: createIndex('license_module', 'crplicense_archive', array (
			'modname'
		)))
		return false;

	if (!DBUtil :: createIndex('license_archive', 'crplicense_archive', array (
			'id_license',
			'id_module',
			'modname'
		), array (
			'UNIQUE' => '1'
		)))
		return false;

	// Create the index
	if (!DBUtil :: createIndex('license_image', 'crplicense_images', array (
			'id_license',
			'document_type'
		), array (
			'UNIQUE' => '1'
		)))
		return false;

	// Set up module hooks for users
	// embed on creation form
	if (!pnModRegisterHook('item', 'new', 'GUI', 'crpLicense', 'user', 'newlicense'))
		return false;
	// embed on edit form
	if (!pnModRegisterHook('item', 'create', 'API', 'crpLicense', 'user', 'createlicense'))
		return false;
	// embed on edit form
	if (!pnModRegisterHook('item', 'modify', 'GUI', 'crpLicense', 'user', 'modifylicense'))
		return false;
	// embed on update
	if (!pnModRegisterHook('item', 'update', 'API', 'crpLicense', 'user', 'updatelicense'))
		return false;
	// display hooked tags
	if (!pnModRegisterHook('item', 'display', 'GUI', 'crpLicense', 'user', 'embedlicense'))
		return false;

	// remove embed on item deletion
	if (!pnModRegisterHook('item', 'delete', 'API', 'crpLicense', 'admin', 'deletelicense'))
		return false;

	// remove hook on module deletion
	if (!pnModRegisterHook('module', 'remove', 'API', 'crpLicense', 'admin', 'removelicense'))
		return false;

	// Set default pages per page
	pnModSetVar('crpLicense', 'itemsperpage', 25);
	pnModSetVar('crpLicense', 'file_dimension', '25000');
	pnModSetVar('crpLicense', 'image_width', '88');
	pnModSetVar('crpLicense', 'crplicense_use_gd', false);
	pnModSetVar('crpLicense', 'crplicense_userlist_image', false);
	pnModSetVar('crpLicense', 'userlist_width', '88');

	// Initialisation successful
	return true;
}

function crpLicense_upgrade($oldversion)
{
	$tables= pnDBGetTables();
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
	// Remove module hooks for users
	// embed on creation form
	if (!pnModUnRegisterHook('item', 'new', 'GUI', 'crpLicense', 'user', 'newlicense'))
		return false;
	// embed on edit form
	if (!pnModUnRegisterHook('item', 'create', 'API', 'crpLicense', 'user', 'createlicense'))
		return false;
	// embed on edit form
	if (!pnModUnRegisterHook('item', 'modify', 'GUI', 'crpLicense', 'user', 'modifylicense'))
		return false;
	// embed on update
	if (!pnModUnRegisterHook('item', 'update', 'API', 'crpLicense', 'user', 'updatelicense'))
		return false;
	// display hooked video
	if (!pnModUnRegisterHook('item', 'display', 'GUI', 'crpLicense', 'user', 'embedlicense'))
		return false;

	// Set up module hooks for admins
	// remove embed on item deletion
	if (!pnModUnRegisterHook('item', 'delete', 'API', 'crpLicense', 'admin', 'deletelicense'))
		return false;

	// remove hook on module deletion
	if (!pnModUnRegisterHook('module', 'remove', 'API', 'crpLicense', 'admin', 'removelicense'))
		return false;

	// drop table
	if (!DBUtil :: dropTable('crplicense'))
	{
		return false;
	}

	if (!DBUtil :: dropTable('crplicense_images'))
	{
		return false;
	}

	if (!DBUtil :: dropTable('crplicense_archive'))
	{
		return false;
	}

	// Delete any module variables
	pnModDelVar('crpLicense');

	// Deletion successful
	return true;
}