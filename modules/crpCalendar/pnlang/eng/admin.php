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
define('_CRPCALENDAR_GENERAL', 'General');
define('_CRPCALENDAR_IMPORT_ICAL', 'iCal import');
define('_CRPCALENDAR_PURGE_EVENTS', 'Purge events');

// admin list
define('_CRPCALENDAR_CONTENT', 'Event detail');
define('_CRPCALENDAR_CHANGE_STATUS', 'Change status');
define('_CRPCALENDAR_CHANGE_STATUS_MODIFYING', 'Modify event for status change');
define('_CRPCALENDAR_EVENT', 'Event');
define('_CRPCALENDAR_EVENTS', 'Calendar');
define('_CRPCALENDAR_NOT_SPECIFIED', 'Not specified');
define('_CRPCALENDAR_INVALID_DATE', 'Invalid date');
define('_CRPCALENDAR_STATUS', 'Status');
define('_CRPCALENDAR_TITLE', 'Title');

// event detail
define('_CRPCALENDAR_CLONE_TITLE', 'Copy of');
define('_CRPCALENDAR_CONTACT', 'Contact');
define('_CRPCALENDAR_DAY_EVENT', 'Day event');
define('_CRPCALENDAR_END_DATE', 'End date');
define('_CRPCALENDAR_EVENT_DOCUMENT', 'Event document');
define('_CRPCALENDAR_EVENT_IMAGE', 'Event image (.gif, .jpg, .png) - Max');
define('_CRPCALENDAR_ID_FORMICULA', 'Formicula form identifier');
define('_CRPCALENDAR_LOCATION', 'Location');
define('_CRPCALENDAR_LOCATIONS', 'from Locations module');
define('_CRPCALENDAR_IMAGE_WIDTH', 'Event image width');
define('_CRPCALENDAR_ORGANISER', 'Organiser');
define('_CRPCALENDAR_PENDING', 'Pending');
define('_CRPCALENDAR_REJECTED', 'Rejected');
define('_CRPCALENDAR_START_DATE', 'Start date');
define('_CRPCALENDAR_URL', 'URL');
define('_CRPCALENDAR_URL_HINT', 'URL (with http://)');

// form define
define('_CRPCALENDAR_CREATE_REENTER', 'Create and reenter');
define('_CRPCALENDAR_CURRENT_FILE', 'Current file');
define('_CRPCALENDAR_DELETE_FILE', 'Delete file');
define('_CRPCALENDAR_EVENT_OTHER_DATE', 'Other start date for this event');
define('_CRPCALENDAR_FROM_DATE', 'Including from date');
define('_CRPCALENDAR_NONE', 'None');
define('_CRPCALENDAR_NOT_REVERSIBLE', 'action not reversible, events will be deleted from database');
define('_CRPCALENDAR_REQUIRED', '*');
define('_CRPCALENDAR_REQUIRED_TEXT', 'Mandatory fields');
define('_CRPCALENDAR_INVALID_INTERVAL', 'Invalid date interval');
define('_CRPCALENDAR_SHOW_FILE', 'Show file');

// config
define('_CRPCALENDAR_COMPLETE_DATE_FORMAT', 'Complete date format (date and time)');
define('_CRPCALENDAR_COMPLETE_DATE_FORMAT_HINT', 'For available formats, see the PHP\'s strftime() documentation');
define('_CRPCALENDAR_DAYLIST_CATEGORIZED', 'Categorized day-list view');
define('_CRPCALENDAR_DOCUMENT_DIMENSION', 'Max upload document size (bytes)');
define('_CRPCALENDAR_ENABLE_FORMICULA', 'Enable formicula');
define('_CRPCALENDAR_ENABLE_LOCATIONS', 'Enable Locations');
define('_CRPCALENDAR_ENABLE_PARTECIPATION', 'Enable user\'s partecipation to events');
define('_CRPCALENDAR_FILE_DIMENSION', 'Max upload image size (bytes)');
define('_CRPCALENDAR_GD_AVAILABLE', 'GD Library');
define('_CRPCALENDAR_IMAGE_RESIZE', 'Image would be resized to');
define('_CRPCALENDAR_IMAGES', 'Images');
define('_CRPCALENDAR_MANDATORY_DESCRIPTION', 'Mandatory event description');
define('_CRPCALENDAR_MULTIPLE_INSERT', 'Use multiple dates for admin\'s creation');
define('_CRPCALENDAR_NOTIFICATION_MAIL', 'Notification for user\'s creation (none if empty)');
define('_CRPCALENDAR_ONLY_DATE_FORMAT', 'Date format (only date)');
define('_CRPCALENDAR_OTHER_MODULES', 'Other modules');
define('_CRPCALENDAR_START_YEAR', 'Calendar start Year');
define('_CRPCALENDAR_USE_BROWSER', 'GD Library requirement failed');
define('_CRPCALENDAR_USE_GD', 'crpCalendar use GD Library');
define('_CRPCALENDAR_USERLIST_IMAGE', 'Show thumbnails in user list');
define('_CRPCALENDAR_USERLIST_WIDTH', 'User list thumbnail width');
define('_CRPCALENDAR_SUBMITTED_STATUS', 'Initial status for events submitted by users');
define('_CRPCALENDAR_THEME', 'crpCalendar theme');
define('_CRPCALENDAR_VISUALIZATION', 'Visualization');
define('_CRPCALENDAR_WEEKDAY_START', 'Week starts on');
define('_CRPCALENDAR_YEARLIST_CATEGORIZED', 'Categorized year-list view');

// RSS define
define('_CRPCALENDAR_ATOM', 'ATOM');
define('_CRPCALENDAR_RSS', 'crpCalendar feed');
define('_CRPCALENDAR_RSS1', 'RSS 1.0');
define('_CRPCALENDAR_RSS2', 'RSS 2.0');
define('_CRPCALENDAR_ENABLE_RSS', 'Enable RSS feed');
define('_CRPCALENDAR_SHOW_RSS', 'Display link to RSS feed');
define('_CRPCALENDAR_USE_RSS', 'Feed format');

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