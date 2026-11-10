<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=it
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'La configurazione di Postino è stata registrata',
	'configuration_adresse_envoi' => 'Indirizzo mittente predefinito',
	'configuration_facteur' => 'Postino',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Convalida del certificato SSL',
	'configuration_mailer' => 'Metodo di invio',
	'configuration_smtp' => 'Scelta del metodo di invio delle email',
	'configuration_smtp_descriptif' => 'Se non siete sicuri, scegliete la funzione mail di PHP.',
	'corps_email_de_test' => 'Questa è un mail di prova con accento',

	// E
	'email_envoye_par' => 'Inviato da @site@',
	'email_test_envoye' => 'La mail di prova è stata correttamente inviata. Se non la ricevete correttamente, verficate la configurazione del server o contattate un amministratore del server.',
	'erreur' => 'Errore',
	'erreur_confirm_ip_sans_hostname' => 'Vuoi davvero utilizzare questo indirizzo IP come host SMTP?',
	'erreur_dans_log' => ': consultate il file log per maggiori dettagli',
	'erreur_envoi_bloque_constante' => 'Invio bloccato dalla costante <tt>_TEST_EMAIL_DEST</tt>. Verifica il file <tt>mes_options.php</tt>',
	'erreur_generale' => 'Ci sono uno o più errori di configurazione. Verificate il contenuto del formulario.',
	'erreur_invalid_host' => 'Questo nome di host non è corretto',
	'erreur_invalid_port' => 'QuestO numero di porta non è corretto',
	'erreur_ip_sans_hostname' => 'Questo indirizzo IP non corrisponde a nessun nome di dominio.',

	// F
	'facteur_adresse_envoi_email' => 'Email:',
	'facteur_adresse_envoi_nom' => 'Nome:',
	'facteur_bcc' => 'Copia nascosta (CCN):',
	'facteur_cc' => 'Copia (CC):',
	'facteur_copies' => 'Copie',
	'facteur_copies_descriptif' => 'Una mail sarà mandata in copia agli indirizzi definiti. Un solo indirizzo in copia e/o un solo indirizzo in copia nascosta.',
	'facteur_email_test' => 'Invia email di prova a:',
	'facteur_filtre_accents' => 'Trasformate gli accenti nella loro entity html (utile sopratutto per Hotmail).',
	'facteur_filtre_css' => 'Trasformare gli stili contenuti tra <head> e </head> negli stili "in linea", utile per le webmail perché gli stili in linea hanno la precedenza sugli stili estermi.',
	'facteur_filtre_images' => 'Integrate le immagini citate nelle mail',
	'facteur_filtre_iso_8859' => 'Convertire in ISO-8859-1',
	'facteur_filtres' => 'Filtri',
	'facteur_filtres_descriptif' => 'Alcuni filtri possono essere applicati alle mail al momento dell’invio.',
	'facteur_smtp_auth' => 'Richiede un’autenticazione:',
	'facteur_smtp_auth_non' => 'no',
	'facteur_smtp_auth_oui' => 'si',
	'facteur_smtp_host' => 'Host:',
	'facteur_smtp_password' => 'Password:',
	'facteur_smtp_port' => 'Porta:',
	'facteur_smtp_secure' => 'Connessione sicura:',
	'facteur_smtp_secure_non' => 'no',
	'facteur_smtp_secure_ssl' => 'SSL (obsoleto)',
	'facteur_smtp_secure_tls' => 'TLS (consigliato)',
	'facteur_smtp_sender' => 'Indirizzo di ritorno per gli errori (opzionale)',
	'facteur_smtp_sender_descriptif' => 'Definisce nell’intestazione dell’e-mail l’indirizzo e-mail di ritorno dell’errore (o Return-Path)',
	'facteur_smtp_tls_allow_self_signed_non' => 'il certificato SSL del server SMTP è emesso da un’Autorità di Certificazione (consigliato).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'il certificato SSL del server SMTP è autofirmato.',
	'facteur_smtp_username' => 'Nome dell’utente:',

	// I
	'info_envois_bloques_constante' => 'Tutti gli invii sono bloccati dalla costante <tt>_TEST_EMAIL_DEST</tt>.',
	'info_envois_forces_vers_email' => 'Tutti gli invii sono forzati all’indirizzo <b>@email@</b> dalla constante <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_email_test_avec_piece_jointe' => 'Con un allegato',
	'label_email_test_from' => 'Mittente',
	'label_email_test_from_placeholder' => 'from@example.org (opzionale)',
	'label_email_test_important' => 'Questa email è importante',
	'label_facteur_forcer_from' => 'Forza questo indirizzo mittente quando <tt>DA</tt> non si trova sullo stesso dominio',
	'label_message_envoye' => 'Messaggio inviato:',

	// M
	'message_identite_email' => 'La <a href="@url@">configurazione del plugin <i>Postino</i></a sostituisce questo indirizzo e-mail con <b>@email@</b> per l’invio di e-mail.',

	// N
	'note_test_configuration' => 'Una mail verrà inviata a questo indirizzo (o all’indirizzo del webmaster).',

	// P
	'personnaliser' => 'Personalizza queste impostazioni',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] FAIL inviato a @dest@ (era: @sujet@)',

	// T
	'tester' => 'Esegui test',
	'tester_la_configuration' => 'Test di configurazione',
	'titre_configurer_facteur' => 'Configurazione di Postino',

	// U
	'utiliser_mail' => 'Usa la funzione <tt>mail()</tt> di PHP',
	'utiliser_reglages_site' => 'Utilizzare le impostazioni del sito SPIP: il nome visualizzato sarà il nome del sito e l’indirizzo mail sarà quello del webmaster',
	'utiliser_smtp' => 'Utilizzare SMTP',

	// V
	'valider' => 'Confermare',
	'version_html' => 'Versione HTML.',
	'version_texte' => 'Versione testo.',
];
