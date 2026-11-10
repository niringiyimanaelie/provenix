<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=es
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'La configuración del cartero fue guardada correctamente',
	'configuration_adresse_envoi' => 'Dirección de envío predeterminada',
	'configuration_facteur' => 'Cartero',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Validación del certificado SSL',
	'configuration_mailer' => 'Configuración del método de envío',
	'configuration_smtp' => 'Elección del método de envío del correo electrónico',
	'configuration_smtp_descriptif' => 'Si no estás seguro, elige la función de correo electrónico de PHP.',
	'corps_email_de_test' => 'Éste es un mensaje de prueba acentuado',

	// E
	'email_envoye_par' => 'Enviado por @site@',
	'email_test_envoye' => 'El correo electrónico de prueba se ha enviado correctamente. Si no lo recibes, verifica la configuración de tu servidor o contacta a un administrador del servicio. ',
	'erreur' => 'Error',
	'erreur_dans_log' => ': consulta el archivo de registro (log) para más detalles',
	'erreur_envoi_bloque_constante' => 'Envío bloqueado por la constante <tt>_TEST_EMAIL_DEST</tt>.
Verifique su archivo <tt>mes_options.php</tt>',
	'erreur_generale' => 'Hay uno o más errores de configuración. Por favor, compruebe el contenido del formulario. ',
	'erreur_invalid_host' => 'El nombre del host es incorrecto',
	'erreur_invalid_port' => 'El número del puerto es incorrecto',

	// F
	'facteur_adresse_envoi_email' => 'Correo electrónico:',
	'facteur_adresse_envoi_nom' => 'Nombre:',
	'facteur_bcc' => 'Copia Oculta (CCO):',
	'facteur_cc' => 'Copia (CC) :',
	'facteur_copies' => 'Copias:',
	'facteur_copies_descriptif' => 'Un correo electrónico será enviado en copia a las direcciones definidas. Una sola dirección en copia y/o una sola dirección en copia oculta.',
	'facteur_email_test' => 'Enviar un correo electrónico de prueba a:',
	'facteur_filtre_accents' => 'Transformar los acentos en su versión html (especialmente útil para Hotmail).',
	'facteur_filtre_css' => 'Transformar los estilos contenidos entre <head> y </head> en estilos en línea, útil para los webmails porque los estilos lineales tienen prioridad sobre los estilos externos. ',
	'facteur_filtre_images' => 'Incorporar las imágenes de referencia en los correos electrónicos',
	'facteur_filtre_iso_8859' => 'Convertir en ISO-8859-1',
	'facteur_filtres' => 'Filtros',
	'facteur_filtres_descriptif' => 'Los filtros pueden aplicarse a los correos electrónicos al ser enviados.',
	'facteur_smtp_auth' => 'Requiere autentificación:',
	'facteur_smtp_auth_non' => 'no',
	'facteur_smtp_auth_oui' => 'sí',
	'facteur_smtp_host' => 'Host:',
	'facteur_smtp_password' => 'Contraseña:',
	'facteur_smtp_port' => 'Puerto:',
	'facteur_smtp_secure' => 'Conexión segura:',
	'facteur_smtp_secure_non' => 'no',
	'facteur_smtp_secure_ssl' => 'SSL (obsoleto)',
	'facteur_smtp_secure_tls' => 'TLS (recomendado)',
	'facteur_smtp_sender' => 'Rebote de errores (opcional)',
	'facteur_smtp_sender_descriptif' => 'Escribe la dirección del correo electrónico del rebote de errores (o "Return-Path"), y en caso de un envío a través del método SMTP indica, también, la dirección del remitente.',
	'facteur_smtp_tls_allow_self_signed_non' => 'El certificado SSL del servidor SMTP es emitido por una Autoridad Certificada (recomendado).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'El certificado SSL del servidor SMTP está autofirmado.',
	'facteur_smtp_username' => 'Nombre de usuario:',

	// I
	'info_envois_bloques_constante' => 'Todos los envíos están bloqueados por la constante <tt>_TEST_EMAIL_DEST</tt>',
	'info_envois_forces_vers_email' => 'Todos los envíos son forzados a enviarse mediante el correo<b>@email@</b> por <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_email_test_avec_piece_jointe' => 'Con un archivo adjunto',
	'label_email_test_from' => 'Remitente',
	'label_email_test_from_placeholder' => 'desde@ejemplo.org (opcional)',
	'label_email_test_important' => 'Este correo electrónico es importante',
	'label_facteur_forcer_from' => 'Forzar esta dirección de envío cuando el <tt>Remitente</tt> no está en el mismo dominio',
	'label_message_envoye' => 'Mensaje enviado:',

	// M
	'message_identite_email' => 'La <a href="@url@"> configuración del plugin <i>Cartero</i></a> substituye esta dirección de correo con <b>@email@</b> para el envío.',

	// N
	'note_test_configuration' => 'Se enviará un correo electrónico a la dirección de envío definida (o a la del del webmaster).',

	// P
	'personnaliser' => 'Personalizar esta configuración',

	// S
	'sujet_alerte_mail_fail' => '[CORREO] enviar a @dest@ (era: @sujet@)',

	// T
	'tester' => 'Probar',
	'tester_la_configuration' => 'Probar la configuración',
	'titre_configurer_facteur' => 'Configurar Cartero',

	// U
	'utiliser_mail' => 'Utilice la función <tt>mail()</tt> de PHP',
	'utiliser_reglages_site' => 'Utilizar la configuración del sitio SPIP: el nombre mostrado será el nombre del sitio SPIP y la dirección de correo electrónico será la del webmaster',
	'utiliser_smtp' => 'Utilizar SMTP',

	// V
	'valider' => 'Validar',
	'version_html' => 'Versión HTML.',
	'version_texte' => 'Versión texto.',
];
