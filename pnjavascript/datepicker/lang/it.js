var fdLocale = {
	fullMonths : [ "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio",
			"Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre",
			"Dicembre" ],
	monthAbbrs : [ "Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago",
			"Set", "Ott", "Nov", "Dic" ],
	fullDays : [ "Luned\u00ec", "Marted\u00ec", "Mercoled\u00ec",
			"Gioved\u00ec", "Venerd\u00ec", "Sabato", "Domenica" ],
	dayAbbrs : [ "Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom" ],
	titles : [ "Mese precedente", "Mese successivo", "Anno precedente",
			"Anno successivo", "Oggi", "Apri Calendario", "sett",
			"Settimana [[%0%]] di [[%1%]]", "Settimana", "Seleziona una data",
			"Clicca e sposta per mouvere", "Mostra \u201C[[%0%]]\u201D prima",
			"Vai alla data odierna", "Date disabilitate:" ],
	firstDayOfWeek : 0
};
try {
	datepickerController.loadLanguage();
} catch (err) {
}
