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

Loader :: includeOnce('modules/crpCalendar/pnlang/eng/global.php');
//
define('_CRPCALENDAR', 'Calendar');

// user list
define('_CRPCALENDAR_ADD_PARTECIPATION', 'Add partecipation');
define('_CRPCALENDAR_ARCHIVED', 'Archived');
define('_CRPCALENDAR_CONTACT', 'Contact');
define('_CRPCALENDAR_CONTENT', 'Event detail');
define('_CRPCALENDAR_CREATEDON', 'Posted on');
define('_CRPCALENDAR_DELETE_PARTECIPATION', 'Delete partecipation');
define('_CRPCALENDAR_EVENT', 'Event');
define('_CRPCALENDAR_EVENTS', 'Calendar');
define('_CRPCALENDAR_EVENTS_MYLIST', 'attendance to events');
define('_CRPCALENDAR_EVENTS_LIST', 'Calendar event\'s list');
define('_CRPCALENDAR_IMAGES', 'Images');
define('_CRPCALENDAR_INVALID_DATE', 'Invalid date');
define('_CRPCALENDAR_LOCATION', 'Location');
define('_CRPCALENDAR_LOCATIONS', 'from Locations module');
define('_CRPCALENDAR_NOT_SPECIFIED', 'Not specified');
define('_CRPCALENDAR_NOAVATARSELECTED', 'No avatar selected');
define('_CRPCALENDAR_ORGANISER', 'Organiser');
define('_CRPCALENDAR_STATUS', 'Status');
define('_CRPCALENDAR_TITLE', 'Title');
define('_CRPCALENDAR_UPCOMING', 'Upcoming');
define('_CRPCALENDAR_URL', 'URL');
define('_CRPCALENDAR_URL_HINT', 'URL (with http://)');

// event detail
define('_CRPCALENDAR_DAY_EVENT', 'Day event');
define('_CRPCALENDAR_END_DATE', 'End date');
define('_CRPCALENDAR_EVENT_DOCUMENT', 'Event document');
define('_CRPCALENDAR_EVENT_IMAGE', 'Event image (.gif, .jpg, .png) - Max');
define('_CRPCALENDAR_IMAGE_CAPTION','Image caption');
define('_CRPCALENDAR_IMAGE_RESIZE', 'Image would be resized (by browser) to');
define('_CRPCALENDAR_NONE', 'None');
define('_CRPCALENDAR_PARTECIPATIONS', 'Event partecipations');
define('_CRPCALENDAR_READS', 'Reads');
define('_CRPCALENDAR_START_DATE', 'Start date');
define('_CRPCALENDAR_SUBMIT', 'Submit event');
define('_CRPCALENDAR_VIEW', 'Events list');
define('_CRPCALENDAR_USER_PARTECIPATIONS', 'user\'s attendance to events');
define('_CRPCALENDAR_WORDSINTHISTEXT', 'Words in text');

// form define
define('_CRPCALENDAR_CURRENT_FILE', 'Current file');
define('_CRPCALENDAR_REQUIRED', '*');
define('_CRPCALENDAR_INVALID_INTERVAL', 'Invalid date interval');
define('_CRPCALENDAR_WAITING', 'Waiting to be approved.');

//RSS define
define('_CRPCALENDAR_HCALENDAR', 'hCalendar microformat');
define('_CRPCALENDAR_ICAL', 'crpCalendar iCal event');
define('_CRPCALENDAR_RSS', 'crpCalendar feed');

// error messages
define('_CRPCALENDAR_ERROR_DOCUMENT_FILE_SIZE_TOO_BIG', 'Document size not allowed');
define('_CRPCALENDAR_ERROR_DOCUMENT_NO_FILE', 'Document file not uploaded');
define('_CRPCALENDAR_ERROR_IMAGE_FILE_SIZE_TOO_BIG', 'Image File size not allowed');
define('_CRPCALENDAR_ERROR_IMAGE_NO_FILE', 'Image file not uploaded');
define('_CRPCALENDAR_ERROR_EVENT_EXISTENT', 'Event already existent');
define('_CRPCALENDAR_ERROR_EVENT_NO_CATEGORY', 'Categorisation is enabled, choose a category');
define('_CRPCALENDAR_INVALID_NOTIFICATION', 'Invalid notification e-mail address');
define('_CRPCALENDAR_IMAGE_INVALID_TYPE', 'Image invalid type');
define('_CRPCALENDAR_INVALID_URL', 'Invalid URL');

// view navigation
define('_CRPCALENDAR_NEXT_DAY', 'Next day');
define('_CRPCALENDAR_NEXT_MONTH', 'Next month');
define('_CRPCALENDAR_NEXT_YEAR', 'Next year');
define('_CRPCALENDAR_NEXT_WEEK', 'Next week');
define('_CRPCALENDAR_NO_EVENTS', 'No events to be shown');
define('_CRPCALENDAR_PREV_DAY', 'Previous day');
define('_CRPCALENDAR_PREV_MONTH', 'Previous month');
define('_CRPCALENDAR_PREV_YEAR', 'Previous year');
define('_CRPCALENDAR_PREV_WEEK', 'Previous week');
define('_CRPCALENDAR_VIEW_DAY', 'Daily view');
define('_CRPCALENDAR_VIEW_MONTH', 'Monthly view');
define('_CRPCALENDAR_VIEW_YEAR', 'Yearly view');
define('_CRPCALENDAR_VIEW_WEEK', 'Weekly view');
//
define('_CRPCALENDAR_VIEW_FORM', 'View as');
define('_CRPCALENDAR_VIEW_LIST', 'List');
define('_CRPCALENDAR_VIEW_TABLE', 'Table');
define('_CRPCALENDAR_VIEW_THIS_MONTH', 'This month');
define('_CRPCALENDAR_VIEW_THIS_WEEK', 'This week');
define('_CRPCALENDAR_VIEW_TODAY', 'Today');

// mail text
define('_CRPCALENDAR_EVENT_NOTIFICATION', 'Notification of Event Creation');
define('_CRPCALENDAR_NOTIFICATION', 'Event creation');
define('_CRPCALENDAR_DONOTREPLY', 'Please do not reply to this email! This is just an automatical generated text...');