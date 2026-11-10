<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=en
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'Postman’s configuration is now saved',
	'configuration_adresse_envoi' => 'Default sending address',
	'configuration_facteur' => 'Postman',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'SSL certificate validation',
	'configuration_mailer' => 'Sending method',
	'configuration_smtp' => 'Choose your mailer',
	'configuration_smtp_descriptif' => 'If you’re not sure about the settings, leave them set to "PHP mail".',
	'corps_email_de_test' => 'This is a test email',

	// E
	'email_envoye_par' => 'Sent by @site@',
	'email_test_envoye' => 'The test email was successfully sent. If you do not receive it correctly, check the configuration of your server or contact a server administrator.',
	'erreur' => 'Error',
	'erreur_confirm_ip_sans_hostname' => 'Do you really want to use this IP address as SMTP Host?',
	'erreur_dans_log' => ': check the log file for more details',
	'erreur_envoi_bloque_constante' => 'Sending is blocked by the constant <tt> _TEST_EMAIL_DEST </tt>.
Check your <tt> mes_options.php </tt> file ',
	'erreur_generale' => 'There are one or more configuration errors. Please check the contents of the form.',
	'erreur_invalid_host' => 'This host name is not valid',
	'erreur_invalid_port' => 'This port number is not valid',
	'erreur_ip_sans_hostname' => 'This IP address does not correspond to any domain name.',

	// F
	'facteur_adresse_envoi_email' => 'Email:',
	'facteur_adresse_envoi_nom' => 'Name:',
	'facteur_bcc' => 'Blind Carbon Copy (BCC):',
	'facteur_cc' => 'Carbon Copy (CC):',
	'facteur_copies' => 'Copies',
	'facteur_copies_descriptif' => 'An email will be sent to specified adresses. One Carbon Copy and/or one Blind Carbon Copy.',
	'facteur_email_test' => 'Send a test email to:',
	'facteur_filtre_accents' => 'Transform accents into their html entities (useful for Hotmail).',
	'facteur_filtre_css' => 'Transform styles present between &lt;head&gt; and &lt;/head&gt; into inline styles, useful for webmails because inline styles overwrite external styles.',
	'facteur_filtre_images' => 'Embed images referenced in emails',
	'facteur_filtre_iso_8859' => 'Convert to ISO-8859-1',
	'facteur_filtres' => 'Filters',
	'facteur_filtres_descriptif' => 'Some filters can be applied before sending an email.',
	'facteur_smtp_auth' => 'Requires authentication:',
	'facteur_smtp_auth_non' => 'no',
	'facteur_smtp_auth_oui' => 'yes',
	'facteur_smtp_host' => 'Host:',
	'facteur_smtp_password' => 'Password:',
	'facteur_smtp_port' => 'Port:',
	'facteur_smtp_secure' => 'Secure:',
	'facteur_smtp_secure_non' => 'no',
	'facteur_smtp_secure_ssl' => 'SSL (depreciated)',
	'facteur_smtp_secure_tls' => 'TLS (recommended)',
	'facteur_smtp_sender' => 'Return-Path (optional)',
	'facteur_smtp_sender_descriptif' => 'Define the Return-Path in the mail header, useful for error feedback.',
	'facteur_smtp_tls_allow_self_signed_non' => 'the SSL certificate of the SMTP server is issued by a Certificate Authority (recommended).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'the SSL certificate of the SMTP server is self-signed.',
	'facteur_smtp_username' => 'Username:',

	// I
	'info_envois_bloques_constante' => 'All sendings are blocked by the constant <tt> _TEST_EMAIL_DEST </tt>. ',
	'info_envois_forces_vers_email' => 'All sendings are forced to the address <b> @email@ </b> by the constant <tt> _TEST_EMAIL_DEST </tt> ',

	// L
	'label_email_test_avec_piece_jointe' => 'With an attached file',
	'label_email_test_from' => 'Sender',
	'label_email_test_from_placeholder' => 'from@example.org (optional)',
	'label_email_test_important' => 'This email is important',
	'label_facteur_forcer_from' => 'Force this sending address when <tt>From</tt> is not on the same domain',
	'label_message_envoye' => 'Send email :',

	// M
	'message_identite_email' => 'The <a href="@url@">configuration of the plugin <i>"Postman"</i></a>  preset this email address <b>@email@</b> for sending emails.',

	// N
	'note_test_configuration' => 'A test email will be sent to this address.',

	// P
	'personnaliser' => 'Customize',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] Failed to deliver @dest@ (subject : @sujet@)',

	// T
	'tester' => 'Test',
	'tester_la_configuration' => 'Test the config',
	'titre_configurer_facteur' => 'Configuration of  Postman',

	// U
	'utiliser_mail' => 'Use <tt>mail()</tt> function from PHP',
	'utiliser_reglages_site' => 'Use the SPIP site’s settings',
	'utiliser_smtp' => 'Use SMTP',

	// V
	'valider' => 'Submit',
	'version_html' => 'HTML version.',
	'version_texte' => 'Text version.',
];
