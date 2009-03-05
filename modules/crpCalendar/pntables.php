<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007,2008 Daniele Conca
 * @link http://code.zikula.org/crpcalendar Support and documentation
 * @author Daniele Conca <conca dot daniele at gmail dot com>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 */

/**
 * crpCalendar table information
 *
 * @return array table definitions
 */
function crpCalendar_pntables()
{
	// Initialise table array
	$pntable = array ();

	// Full table definition
	$pntable['crpcalendar'] = DBUtil :: getLimitedTablename('crpcalendar');
	$pntable['crpcalendar_column'] = array (
		'eventid' 		=> 'eventid',
		'title' 			=> 'title',
		'urltitle' 		=> 'urltitle',
		'location' 		=> 'location',
		'url' 				=> 'url',
		'contact' 		=> 'contact',
		'organiser' 	=> 'organiser',
		'event_text'	=> 'event_text',
		'start_date'	=> 'start_date',
		'end_date' 		=> 'end_date',
		'day_event' 	=> 'day_event',
		'language' 		=> 'language',
		'counter' 		=> 'counter',
		'id_formicula'=> 'id_formicula',
	);
	$pntable['crpcalendar_column_def'] = array (
		'eventid' 		=> 'I(11) AUTOINCREMENT PRIMARY',
		'title' 			=> "X NOTNULL DEFAULT ''",
		'urltitle' 		=> "X NOTNULL DEFAULT ''",
		'location' 		=> "C(255) NOTNULL DEFAULT ''",
		'url' 				=> "C(255) NOTNULL DEFAULT ''",
		'contact' 		=> "C(255) NOTNULL DEFAULT ''",
		'organiser' 	=> "C(255) NOTNULL DEFAULT ''",
		'event_text' 	=> "X NOTNULL DEFAULT ''",
		'start_date' 	=> "T NOTNULL DEFAULT '1970-01-01 00:00:00'",
		'end_date' 		=> "T NOTNULL DEFAULT '1970-01-01 00:00:00'",
		'day_event' 	=> "I(1) NOTNULL DEFAULT 1",
		'language' 		=> "C(30) NOTNULL DEFAULT ''",
		'counter' 		=> "I(11) NOTNULL DEFAULT 0",
		'id_formicula'=> "C(255) NOTNULL DEFAULT ''"
	);

	$pntable['crpcalendar_files'] = DBUtil :: getLimitedTablename('crpcalendar_files');
	$pntable['crpcalendar_files_column'] = array (
			'id'						=> 'id',
			'eventid'				=> 'eventid',
			'document_type'	=> 'document_type',
			'name'					=> 'name',
			'content_type'	=> 'content_type',
			'size'					=> 'size',
			'binary_data'		=> 'binary_data');

	$pntable['crpcalendar_files_column_def'] = array (
		'id' 						=> 'I(11) AUTOINCREMENT PRIMARY',
		'eventid' 			=> "I(11) NOTNULL DEFAULT 0",
		'document_type' => "C(255) NOTNULL DEFAULT ''",
		'name' 					=> "C(255) NOTNULL DEFAULT ''",
		'content_type'	=> "C(255) NOTNULL DEFAULT ''",
		'size' 					=> "I NOTNULL DEFAULT 0",
		'binary_data' 	=> "B NOTNULL"
	);

	$pntable['crpcalendar_attendee'] = DBUtil :: getLimitedTablename('crpcalendar_attendee');
	$pntable['crpcalendar_attendee_column'] = array (
			'uid'						=> 'uid',
			'eventid'				=> 'eventid',);
	$pntable['crpcalendar_attendee_column_def'] = array (
		'uid' 					=> "I(11) NOTNULL DEFAULT 0",
		'eventid' 			=> "I(11) NOTNULL DEFAULT 0"
	);

	// Enable categorization services
	$pntable['crpcalendar_db_extra_enable_categorization'] = pnModGetVar('crpCalendar', 'enablecategorization');
	$pntable['crpcalendar_primary_key_column'] = 'eventid';

	// add standard data fields
	ObjectUtil :: addStandardFieldsToTableDefinition($pntable['crpcalendar_column']);
	ObjectUtil :: addStandardFieldsToTableDataDefinition($pntable['crpcalendar_column_def']);

	return $pntable;
}
?>