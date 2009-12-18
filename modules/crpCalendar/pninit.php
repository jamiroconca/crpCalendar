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

/**
 * init crpCalendar module
 *
 * @return bool true on success
 */
function crpCalendar_init()
{
	// create table
	if (!DBUtil :: createTable('crpcalendar'))
	{
		return false;
	}

	if (!DBUtil :: createTable('crpcalendar_files'))
	{
		return false;
	}

	if (!DBUtil :: createTable('crpcalendar_attendee'))
	{
		return false;
	}

	$dom = ZLanguage::getModuleDomain('crpCalendar');

	// Create the index
	if (!DBUtil :: createIndex('event_image', 'crpcalendar_files', array (
			'eventid',
			'document_type'
		), array (
			'UNIQUE' => '1'
		)))
		return false;

	// Create the index
	if (!DBUtil :: createIndex('event_uid', 'crpcalendar_attendee', array (
			'uid',
			'eventid'
		), array (
			'UNIQUE' => '1'
		)))
		return false;

	// create our default category
	if (!_crpCalendar_createdefaultcategory())
	{
		return LogUtil :: registerError(__('Error! Creation attempt failed.', $dom));
	}

	// Set default pages per page
	pnModSetVar('crpCalendar', 'itemsperpage', 20);
	pnModSetVar('crpCalendar', 'enablecategorization', true);
	pnModSetVar('crpCalendar', 'addcategorytitletopermalink', false);
	pnModSetVar('crpCalendar', 'crpcalendar_enable_rss', true);
	pnModSetVar('crpCalendar', 'crpcalendar_show_rss', true);
	pnModSetVar('crpCalendar', 'crpcalendar_rss', 'rss2');
	pnModSetVar('crpCalendar', 'file_dimension', '35000');
	pnModSetVar('crpCalendar', 'image_width', '150');
	pnModSetVar('crpCalendar', 'crpcalendar_use_gd', false);
	pnModSetVar('crpCalendar', 'crpcalendar_userlist_image', false);
	pnModSetVar('crpCalendar', 'userlist_width', '96');
	pnModSetVar('crpCalendar', 'crpcalendar_theme', 'default');
	pnModSetVar('crpCalendar', 'crpcalendar_start_year', date("Y"));
	pnModSetVar('crpCalendar', 'document_dimension', '100000');
	pnModSetVar('crpCalendar', 'enable_partecipation', false);
	pnModSetVar('crpCalendar', 'enable_locations', false);
	pnModSetVar('crpCalendar', 'crpcalendar_notification', null);
	pnModSetVar('crpCalendar', 'daylist_categorized', false);
	pnModSetVar('crpCalendar', 'yearlist_categorized', false);
	pnModSetVar('crpCalendar', 'mandatory_description', true);
	pnModSetVar('crpCalendar', 'submitted_status', 'P');
	pnModSetVar('crpCalendar', 'multiple_insert', false);
	pnModSetVar('crpCalendar', 'enable_formicula', false);
	pnModSetVar('crpCalendar', 'crpcalendar_weekday_start', '1');
	pnModSetVar('crpCalendar', 'complete_date_format', '%d/%m/%Y - %H:%M');
	pnModSetVar('crpCalendar', 'only_date_format', '%d/%m/%Y');
	pnModSetVar('crpCalendar', 'subcategory_listing', false);

	// Initialisation successful
	return true;
}

/**
 * upgrade the pages module
 *
 * @return bool true on success
 */
function crpCalendar_upgrade($oldversion)
{
	$tables = pnDBGetTables();
	$dom = ZLanguage::getModuleDomain('crpCalendar');

	// Upgrade dependent on old version number
	switch ($oldversion)
	{
		case "0.1.0" :
			$sql = "ALTER TABLE $tables[crpcalendar] ADD pn_counter INT( 11 ) NOT NULL DEFAULT '0' AFTER pn_language";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			return crpCalendar_upgrade("0.1.1");
		case "0.1.1" :
			pnModSetVar('crpCalendar', 'crpcalendar_enable_rss', true);
			pnModSetVar('crpCalendar', 'crpcalendar_show_rss', true);
			pnModSetVar('crpCalendar', 'crpcalendar_rss', 'rss2');
			return crpCalendar_upgrade("0.2.0");
		case "0.2.0" :
			pnModSetVar('crpCalendar', 'file_dimension', '35000');
			pnModSetVar('crpCalendar', 'image_width', '150');

			if (!DBUtil :: createTable('crpcalendar_files'))
			{
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			}

			if (!DBUtil :: createIndex('event_image', 'crpcalendar_files', array (
					'eventid',
					'document_type'
				), array (
					'UNIQUE' => '1'
				)))
				LogUtil :: registerError(__('Error! Table update failed.', $dom));
			return crpCalendar_upgrade("0.3.0");
		case "0.3.0" :
			pnModSetVar('crpCalendar', 'crpcalendar_use_gd', false);
			pnModSetVar('crpCalendar', 'crpcalendar_userlist_image', false);
			pnModSetVar('crpCalendar', 'userlist_width', '96');
			return crpCalendar_upgrade("0.3.1");
		case "0.3.1" :
			$sql = "ALTER TABLE $tables[crpcalendar] ADD pn_location VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER pn_urltitle";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			$sql = "ALTER TABLE $tables[crpcalendar] ADD pn_url VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER pn_location";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			$sql = "ALTER TABLE $tables[crpcalendar] ADD pn_contact VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER pn_url";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			$sql = "ALTER TABLE $tables[crpcalendar] ADD pn_organiser VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER pn_contact";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			return crpCalendar_upgrade("0.4.0");
		case "0.4.0" :
			pnModSetVar('crpCalendar', 'crpcalendar_theme', 'default');
			pnModSetVar('crpCalendar', 'crpcalendar_start_year', date("Y"));
			return crpCalendar_upgrade("0.4.1");
		case "0.4.1" :
			pnModSetVar('crpCalendar', 'document_dimension', '100000');
			return crpCalendar_upgrade("0.4.2");
		case "0.4.2" :
			if (!DBUtil :: createTable('crpcalendar_attendee'))
			{
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));
			}
			if (!DBUtil :: createIndex('event_uid', 'crpcalendar_attendee', array (
					'uid',
					'eventid'
				), array (
					'UNIQUE' => '1'
				)))
				LogUtil :: registerError(__('Error! Table update failed.', $dom));
			pnModSetVar('crpCalendar', 'enable_partecipation', false);
			return crpCalendar_upgrade("0.4.3");
		case "0.4.3" :
			pnModSetVar('crpCalendar', 'enable_locations', false);
			return crpCalendar_upgrade("0.4.4");
		case "0.4.4" :
			pnModSetVar('crpCalendar', 'crpcalendar_notification', null);
			return crpCalendar_upgrade("0.4.5");
			break;
		case "0.4.5" :
			pnModSetVar('crpCalendar', 'daylist_categorized', false);
			pnModSetVar('crpCalendar', 'yearlist_categorized', false);
			return crpCalendar_upgrade("0.4.6");
			break;
		case "0.4.6" :
			pnModSetVar('crpCalendar', 'mandatory_description', true);
			return crpCalendar_upgrade("0.4.7");
			break;
		case "0.4.7" :
			pnModSetVar('crpCalendar', 'submitted_status', 'P');
			return crpCalendar_upgrade("0.4.8");
			break;
		case "0.4.8" :
			pnModSetVar('crpCalendar', 'multiple_insert', false);
			return crpCalendar_upgrade("0.4.9");
			break;
		case "0.4.9" :
			pnModSetVar('crpCalendar', 'enable_formicula', false);

			$sql = "ALTER TABLE $tables[crpcalendar] ADD id_formicula VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER pn_counter";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));

			$sql = "ALTER TABLE $tables[crpcalendar] CHANGE `pn_eventid` `eventid` INT( 11 ) NOT NULL AUTO_INCREMENT ,
													CHANGE `pn_title` `title` TEXT NOT NULL DEFAULT '' ,
													CHANGE `pn_urltitle` `urltitle` TEXT NOT NULL DEFAULT '' ,
													CHANGE `pn_location` `location` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_url` `url` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_contact` `contact` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_organiser` `organiser` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_event_text` `event_text` TEXT NOT NULL DEFAULT '' ,
													CHANGE `pn_start_date` `start_date` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' ,
													CHANGE `pn_end_date` `end_date` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' ,
													CHANGE `pn_day_event` `day_event` INT( 1 ) NOT NULL DEFAULT '1' ,
													CHANGE `pn_language` `language` VARCHAR(30) NOT NULL DEFAULT '' ,
													CHANGE `pn_counter` `counter` INT( 1 ) NOT NULL DEFAULT '0' ,
													CHANGE `pn_obj_status` `obj_status` VARCHAR( 1 ) NOT NULL DEFAULT 'A' ,
													CHANGE `pn_cr_date` `cr_date` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' ,
													CHANGE `pn_cr_uid` `cr_uid` INT( 11 ) NOT NULL DEFAULT '0' ,
													CHANGE `pn_lu_date` `lu_date` DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00' ,
													CHANGE `pn_lu_uid` `lu_uid` INT( 11 ) NOT NULL DEFAULT '0' ";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));

			$sql = "ALTER TABLE $tables[crpcalendar_files] CHANGE `pn_id` `id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
													CHANGE `pn_eventid` `eventid` INT( 11 ) NOT NULL DEFAULT '0' ,
													CHANGE `pn_document_type` `document_type` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_name` `name` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_content_type` `content_type` VARCHAR(255) NOT NULL DEFAULT '' ,
													CHANGE `pn_size` `size` INT NOT NULL DEFAULT '0' ,
													CHANGE `pn_binary_data` `binary_data` LONGBLOB NOT NULL DEFAULT '' ";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));

			$sql = "ALTER TABLE $tables[crpcalendar_attendee] CHANGE `pn_uid` `uid` INT( 11 ) NOT NULL DEFAULT '0' ,
													CHANGE `pn_eventid` `eventid` INT( 11 ) NOT NULL DEFAULT '0' ";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));

			return crpCalendar_upgrade("0.5.0");
			break;
		case "0.5.0" :
			pnModSetVar('crpCalendar', 'crpcalendar_weekday_start', '1');
			return crpCalendar_upgrade("0.5.1");
			break;
		case "0.5.1" :
			pnModSetVar('crpCalendar', 'complete_date_format', '%d/%m/%Y - %H:%M');
			pnModSetVar('crpCalendar', 'only_date_format', '%d/%m/%Y');
			return crpCalendar_upgrade("0.5.2");
			break;
		case "0.5.2" :
			pnModSetVar('crpCalendar', 'subcategory_listing', false);
			return crpCalendar_upgrade("0.5.3");
			break;
		case "0.5.3" :
			$sql = "ALTER TABLE $tables[crpcalendar] ADD image_caption VARCHAR( 255 ) NOT NULL DEFAULT '' AFTER organiser";
			if (!DBUtil :: executeSQL($sql))
				return LogUtil :: registerError(__('Error! Table update failed.', $dom));

			return crpCalendar_upgrade("0.5.4");
			break;
		case "0.5.4" :
			break;
	}
	return true;
}

/**
 * uninstall the module
 *
 * @return bool true on success
 */
function crpCalendar_delete()
{
	// drop table
	if (!DBUtil :: dropTable('crpcalendar'))
	{
		return false;
	}

	if (!DBUtil :: dropTable('crpcalendar_files'))
	{
		return false;
	}

	if (!DBUtil :: dropTable('crpcalendar_attendee'))
	{
		return false;
	}

	// Delete any module variables
	pnModDelVar('crpCalendar');

	// Deletion successful
	return true;
}

/**
 * create default category for module
 *
 * @return bool true on success
 */
function _crpCalendar_createdefaultcategory()
{
	// load necessary classes
	Loader :: loadClass('CategoryUtil');
	Loader :: loadClassFromModule('Categories', 'Category');
	Loader :: loadClassFromModule('Categories', 'CategoryRegistry');

	// get the language file
	$lang = ZLanguage::getLanguageCode();

	// get the category path for which we're going to insert our place holder category
	$rootcat = CategoryUtil :: getCategoryByPath('/__SYSTEM__/Modules');

	// create placeholder for all our migrated categories
	$cat = new PNCategory();
	$cat->setDataField('parent_id', $rootcat['id']);
	$cat->setDataField('name', 'crpCalendar');
	$cat->setDataField('value', '-1');

	$cat->setDataField('display_name', array (
		$lang => _CRPCALENDAR_NAME
	));
	$cat->setDataField('display_desc', array (
		$lang => _CRPCALENDAR_CATEGORY_DESCRIPTION
	));
	$cat->setDataField('security_domain', $rootcat['security_domain']);

	if (!$cat->validate('admin'))
	{
		return false;
	}
	$cat->insert();
	$cat->update();

	// get the category path for which we're going to insert our upgraded categories
	$rootcat = CategoryUtil :: getCategoryByPath('/__SYSTEM__/Modules/crpCalendar');

	// create an entry in the categories registry
	$registry = new PNCategoryRegistry();
	$registry->setDataField('modname', 'crpCalendar');
	$registry->setDataField('table', 'crpcalendar');
	$registry->setDataField('property', 'Main');
	$registry->setDataField('category_id', $rootcat['id']);
	$registry->insert();

	return true;
}