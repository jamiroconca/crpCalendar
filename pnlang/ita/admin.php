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

//
define('_CRPCALENDAR', 'Calendario');
define('_CRPCALENDAR_GENERAL','Impostazioni modulo');
define('_CRPCALENDAR_IMPORT_ICAL','Importazione iCal');
define('_CRPCALENDAR_PURGE_EVENTS','Pulizia eventi');

// admin list
define('_CRPCALENDAR_CONTENT', 'Dettaglio evento');
define('_CRPCALENDAR_CHANGE_STATUS', 'Cambia stato');
define('_CRPCALENDAR_CHANGE_STATUS_MODIFYING','Modificare l\'evento per cambiarne lo stato');
define('_CRPCALENDAR_EVENT', 'Evento');
define('_CRPCALENDAR_EVENTS', 'Calendario');
define('_CRPCALENDAR_NOT_SPECIFIED','Non specificato');
define('_CRPCALENDAR_INVALID_DATE', 'Data utilizzate non valida');
define('_CRPCALENDAR_STATUS', 'Stato');
define('_CRPCALENDAR_TITLE', 'Titolo evento');

// event detail
define('_CRPCALENDAR_CLONE_TITLE','Copia di');
define('_CRPCALENDAR_CONTACT','Contatto');
define('_CRPCALENDAR_DAY_EVENT', 'Evento giornaliero');
define('_CRPCALENDAR_END_DATE','Data termine');
define('_CRPCALENDAR_EVENT_DOCUMENT','Documento allegato');
define('_CRPCALENDAR_EVENT_IMAGE','Immagine evento (.gif, .jpg, .png) - Max');
define('_CRPCALENDAR_ID_FORMICULA','ID modulo Formicula');
define('_CRPCALENDAR_LOCATION','Luogo');
define('_CRPCALENDAR_LOCATIONS','dal modulo Locations');
define('_CRPCALENDAR_IMAGE_WIDTH','Larghezza delle immagini per gli eventi');
define('_CRPCALENDAR_ORGANISER','Organizzazione');
define('_CRPCALENDAR_PENDING','In attesa');
define('_CRPCALENDAR_REJECTED','Rifiutato');
define('_CRPCALENDAR_START_DATE','Data inizio');
define('_CRPCALENDAR_URL','URL');
define('_CRPCALENDAR_URL_HINT','URL (iniziare con http://)');

// form define
define('_CRPCALENDAR_CREATE_REENTER','Create and reenter');
define('_CRPCALENDAR_CURRENT_FILE','File corrente');
define('_CRPCALENDAR_DELETE_FILE','Elimina file');
define('_CRPCALENDAR_EVENT_OTHER_DATE','Evento da inserire anche per le date');
define('_CRPCALENDAR_FROM_DATE','Inclusi dalla data');
define('_CRPCALENDAR_NONE', 'Nessuno');
define('_CRPCALENDAR_NOT_REVERSIBLE','azione non reversible, gli eventi verranno eliminati dal database');
define('_CRPCALENDAR_REQUIRED','*');
define('_CRPCALENDAR_REQUIRED_TEXT','Campo obbligatorio');
define('_CRPCALENDAR_INVALID_INTERVAL', 'Data di inizio posteriore alla data di termine');
define('_CRPCALENDAR_SHOW_FILE','Visualizza file');

// config
define('_CRPCALENDAR_DAYLIST_CATEGORIZED','Vista giornaliera categorizzata');
define('_CRPCALENDAR_DOCUMENT_DIMENSION','Massima dimensione dei documenti per l\'upload (bytes)');
define('_CRPCALENDAR_ENABLE_FORMICULA','Abilita formicula');
define('_CRPCALENDAR_ENABLE_LOCATIONS','Abilita Locations');
define('_CRPCALENDAR_ENABLE_PARTECIPATION','Abilita partecipazione degli utenti agli eventi');
define('_CRPCALENDAR_FILE_DIMENSION','Massima dimensione delle immagini per l\'upload (bytes)');
define('_CRPCALENDAR_GD_AVAILABLE','GD Library');
define('_CRPCALENDAR_IMAGE_RESIZE','Le immagini saranno scalate (dal browser) a');
define('_CRPCALENDAR_IMAGES','Immagini');
define('_CRPCALENDAR_MANDATORY_DESCRIPTION','Contenuto dell\'evento obbligatorio');
define('_CRPCALENDAR_MULTIPLE_INSERT','Date multiple per creazioni amministrative');
define('_CRPCALENDAR_NOTIFICATION_MAIL','Indirizzo per notifiche di creazione eventi (nessuna se vuoto)');
define('_CRPCALENDAR_OTHER_MODULES','Altri moduli');
define('_CRPCALENDAR_START_YEAR','Anno d\'inizio del calendario');
define('_CRPCALENDAR_USE_BROWSER','Le GD Library richieste non sono state trovate');
define('_CRPCALENDAR_USE_GD','crpCalendar usa le GD Library');
define('_CRPCALENDAR_USERLIST_IMAGE','Thumbnail nell\'elenco utenti');
define('_CRPCALENDAR_USERLIST_WIDTH','Larghezza delle thumbnail nell\'elenco utenti');
define('_CRPCALENDAR_SUBMITTED_STATUS','Stato iniziale degli eventi inseriti dagli utenti');
define('_CRPCALENDAR_THEME','crpCalendar theme');
define('_CRPCALENDAR_VISUALIZATION','Visualizzazione');
define('_CRPCALENDAR_YEARLIST_CATEGORIZED','Vista annuale categorizzata');

// RSS define
define('_CRPCALENDAR_ATOM','ATOM');
define('_CRPCALENDAR_RSS','crpCalendar feed');
define('_CRPCALENDAR_RSS1','RSS 1.0');
define('_CRPCALENDAR_RSS2','RSS 2.0');
define('_CRPCALENDAR_ENABLE_RSS','Abilita feed RSS degli eventi');
define('_CRPCALENDAR_SHOW_RSS','Visualizza link al feed RSS');
define('_CRPCALENDAR_USE_RSS','Formato del feed');

// error messages
define('_CRPCALENDAR_ERROR_DOCUMENT_FILE_SIZE_TOO_BIG','Dimiensioni del documento non consentite');
define('_CRPCALENDAR_ERROR_DOCUMENT_NO_FILE','File del documento non caricato');
define('_CRPCALENDAR_ERROR_IMAGE_FILE_SIZE_TOO_BIG','Dimensioni dell\'immagine non permesse');
define('_CRPCALENDAR_ERROR_IMAGE_NO_FILE','Immagine non caricata');
define('_CRPCALENDAR_ERROR_EVENT_EXISTENT','Evento pre-esistente');
define('_CRPCALENDAR_ERROR_EVENT_NO_CATEGORY','Categorizzazione abilitata, scegliere una categoria');
define('_CRPCALENDAR_INVALID_NOTIFICATION','E-mail per le notifiche in formato non valido');
define('_CRPCALENDAR_IMAGE_INVALID_TYPE','Formato di immagine non valido');
define('_CRPCALENDAR_INVALID_URL','URL non valido');

?>
