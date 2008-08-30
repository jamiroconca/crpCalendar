<?php
/**
 * crpCalendar
 *
 * @copyright (c) 2007, Daniele Conca
 * @link http://noc.postnuke.com/projects/crpcalendar Support and documentation
 * @version $Id: $
 * @author Daniele Conca <jami at cremonapalloza dot org>
 * @license GNU/GPL - v.2.1
 * @package crpCalendar
 *
 * @version $Id$
 * translation by sven schomacker (hilope)
 */

//
define('_CRPCALENDAR', 'Kalender');
define('_CRPCALENDAR_GENERAL','Allgemein');
define('_CRPCALENDAR_IMPORT_ICAL','iCal Import');
define('_CRPCALENDAR_PURGE_EVENTS','Termine bereinigen');

// admin list
define('_CRPCALENDAR_CONTENT', 'Termindetails');
define('_CRPCALENDAR_CHANGE_STATUS', 'Status ändern');
define('_CRPCALENDAR_CHANGE_STATUS_MODIFYING','Bearbeite Termin für Statusänderung');
define('_CRPCALENDAR_EVENT', 'Termin');
define('_CRPCALENDAR_EVENTS', 'Kalender');
define('_CRPCALENDAR_NOT_SPECIFIED','Nicht spezifiziert');
define('_CRPCALENDAR_INVALID_DATE', 'Ungültiges Datum');
define('_CRPCALENDAR_STATUS', 'Status');
define('_CRPCALENDAR_TITLE', 'Titel');

// event detail
define('_CRPCALENDAR_CLONE_TITLE','Kopie von');
define('_CRPCALENDAR_CONTACT','Kontakt');
define('_CRPCALENDAR_DAY_EVENT', 'Tagestermin');
define('_CRPCALENDAR_END_DATE','Enddatum');
define('_CRPCALENDAR_EVENT_DOCUMENT','Termindokument');
define('_CRPCALENDAR_EVENT_IMAGE','Terminbild (.gif, .jpg, .png) - Max');
define('_CRPCALENDAR_LOCATION','Ort');
define('_CRPCALENDAR_LOCATIONS','vom Locations-Modul');
define('_CRPCALENDAR_IMAGE_WIDTH','Terminbildbreite');
define('_CRPCALENDAR_ORGANISER','Veranstalter');
define('_CRPCALENDAR_PENDING','Wartend');
define('_CRPCALENDAR_REJECTED','Abgewiesen');
define('_CRPCALENDAR_START_DATE','Startdatum');
define('_CRPCALENDAR_URL','URL');
define('_CRPCALENDAR_URL_HINT','URL (mit http://)');

// form define
define('_CRPCALENDAR_CREATE_REENTER','Erstellen und Seite neu laden');
define('_CRPCALENDAR_CURRENT_FILE','Aktuelle Datei');
define('_CRPCALENDAR_DELETE_FILE','Datei löschen');
define('_CRPCALENDAR_EVENT_OTHER_DATE','Anderes Startdatum für diesen Termin');
define('_CRPCALENDAR_FROM_DATE','Einschließlich ab Datum');
define('_CRPCALENDAR_NONE', 'Nichts');
define('_CRPCALENDAR_NOT_REVERSIBLE','Aktion kann nicht rückgängig gemacht werden, die Termine werden unwiderruflich aus der Datenbank gelöscht');
define('_CRPCALENDAR_REQUIRED','*');
define('_CRPCALENDAR_REQUIRED_TEXT','Erforderliche Felder');
define('_CRPCALENDAR_INVALID_INTERVAL', 'Ungültiger Datumszeitraum');
define('_CRPCALENDAR_SHOW_FILE','Zeige Datei');

// config
define('_CRPCALENDAR_DAYLIST_CATEGORIZED','Tagesansicht kategorisieren');
define('_CRPCALENDAR_DOCUMENT_DIMENSION','max. Uploadgröße von Dokumenten (bytes)');
define('_CRPCALENDAR_ENABLE_LOCATIONS','Aktiviere Locations');
define('_CRPCALENDAR_ENABLE_PARTECIPATION','Erlaube Benutzerteilnahmen an Terminen');
define('_CRPCALENDAR_FILE_DIMENSION','max. Uploadgröße von Bildern (bytes)');
define('_CRPCALENDAR_GD_AVAILABLE','GD Bibliothek');
define('_CRPCALENDAR_IMAGE_RESIZE','Bild wird heruntergerechnet auf');
define('_CRPCALENDAR_IMAGES','Bilder');
define('_CRPCALENDAR_MANDATORY_DESCRIPTION','Erforderliche Terminbeschreibung');
define('_CRPCALENDAR_MULTIPLE_INSERT','Benutze multiple Daten für Erstellung durch Admins');
define('_CRPCALENDAR_NOTIFICATION_MAIL','Benachrichtigungsadresse bei Terminerstellung von Benutzern (leer für keine)');
define('_CRPCALENDAR_OTHER_MODULES','Andere Module');
define('_CRPCALENDAR_START_YEAR','Startjahr des Kalenders');
define('_CRPCALENDAR_USE_BROWSER','erforderliche GD Bibliothek nicht gefunden');
define('_CRPCALENDAR_USE_GD','crpCalendar benutzt die GD Bibliothek');
define('_CRPCALENDAR_USERLIST_IMAGE','Zeige Thumbnails in Benutzerliste');
define('_CRPCALENDAR_USERLIST_WIDTH','Thumbnail-Breite in Benutzerliste');
define('_CRPCALENDAR_SUBMITTED_STATUS','Status für von Benutzern gemeldete Termine');
define('_CRPCALENDAR_THEME','crpCalendar-Theme');
define('_CRPCALENDAR_VISUALIZATION','Visualization');
define('_CRPCALENDAR_YEARLIST_CATEGORIZED','kategorisierte Jahreslistenansicht');

// RSS define
define('_CRPCALENDAR_ATOM','ATOM');
define('_CRPCALENDAR_RSS','crpCalendar Feed');
define('_CRPCALENDAR_RSS1','RSS 1.0');
define('_CRPCALENDAR_RSS2','RSS 2.0');
define('_CRPCALENDAR_ENABLE_RSS','Aktiviere RSS-Feed');
define('_CRPCALENDAR_SHOW_RSS','Zeige Verknüpfung auf RSS-Feed');
define('_CRPCALENDAR_USE_RSS','Feed-Format');

// error messages
define('_CRPCALENDAR_ERROR_DOCUMENT_FILE_SIZE_TOO_BIG','Dokumentengröße nicht erlaubt');
define('_CRPCALENDAR_ERROR_DOCUMENT_NO_FILE','Dokument nicht hochgeladen');
define('_CRPCALENDAR_ERROR_IMAGE_FILE_SIZE_TOO_BIG','Bildgröße nicht erlaubt');
define('_CRPCALENDAR_ERROR_IMAGE_NO_FILE','Bild nicht hochgeladen');
define('_CRPCALENDAR_ERROR_EVENT_EXISTENT','Termin existiert bereits');
define('_CRPCALENDAR_ERROR_EVENT_NO_CATEGORY','Kategorisierung ist eingeschaltet, bitte Kategorie wählen');
define('_CRPCALENDAR_INVALID_NOTIFICATION','Ungültige Benachrichtigungsadresse');
define('_CRPCALENDAR_IMAGE_INVALID_TYPE','Ungültiger Bildtyp');
define('_CRPCALENDAR_INVALID_URL','Ungültige URL');
