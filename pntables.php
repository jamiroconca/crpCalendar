<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @author Daniele Conca <jami at cremonapalloza dot org>
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
		'eventid' 		=> 'pn_eventid',
		'title' 			=> 'pn_title',
		'urltitle' 		=> 'pn_urltitle',
		'location' 		=> 'pn_location',
		'url' 				=> 'pn_url',
		'contact' 		=> 'pn_contact',
		'organiser' 	=> 'pn_organiser',
		'event_text'	=> 'pn_event_text',
		'start_date'	=> 'pn_start_date',
		'end_date' 		=> 'pn_end_date',
		'day_event' 	=> 'pn_day_event',
		'language' 		=> 'pn_language',
		'counter' 		=> 'pn_counter'
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
		'start_date' 	=> "T DEFAULT NULL",
		'end_date' 		=> "T DEFAULT NULL",
		'day_event' 	=> "I(1) NOTNULL DEFAULT '1'",
		'language' 		=> "C(30) NOTNULL DEFAULT ''",
		'counter' 		=> "I(11) NOTNULL DEFAULT '0'"
	);
	
	$pntable['crpcalendar_files'] = DBUtil :: getLimitedTablename('crpcalendar_files');
	$pntable['crpcalendar_files_column'] = array (
			'id'						=> 'pn_id',
			'eventid'				=> 'pn_eventid',
			'document_type'	=> 'pn_document_type',
			'name'					=> 'pn_name',
			'content_type'	=> 'pn_content_type',
			'size'					=> 'pn_size',
			'binary_data'		=> 'pn_binary_data');
	$pntable['crpcalendar_files_column_def'] = array (
		'id' 						=> 'I(11) AUTOINCREMENT PRIMARY',
		'eventid' 			=> "I(11) NOTNULL DEFAULT '0'",
		'document_type' => "C(255) NOTNULL DEFAULT ''",
		'name' 					=> "C(255) NOTNULL DEFAULT ''",
		'content_type'	=> "C(255) NOTNULL DEFAULT ''",
		'size' 					=> "I NOTNULL DEFAULT '0'",
		'binary_data' 	=> "B NOTNULL DEFAULT ''"
	);
	
	$pntable['crpcalendar_attendee'] = DBUtil :: getLimitedTablename('crpcalendar_attendee');
	$pntable['crpcalendar_attendee_column'] = array (
			'uid'						=> 'pn_uid',
			'eventid'				=> 'pn_eventid',);
	$pntable['crpcalendar_attendee_column_def'] = array (
		'uid' 					=> "I(11) NOTNULL DEFAULT '0'",
		'eventid' 			=> "I(11) NOTNULL DEFAULT '0'"
	);
	
	// Enable categorization services
	$pntable['crpcalendar_db_extra_enable_categorization'] = pnModGetVar('crpCalendar', 'enablecategorization');
	$pntable['crpcalendar_primary_key_column'] = 'eventid';

	// add standard data fields
	ObjectUtil :: addStandardFieldsToTableDefinition($pntable['crpcalendar_column'], 'pn_');
	ObjectUtil :: addStandardFieldsToTableDataDefinition($pntable['crpcalendar_column_def']);

	return $pntable;
}
?>