<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=uk
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'Налаштування плагіну збережено',
	'configuration_adresse_envoi' => 'Інформація про відправника',
	'configuration_facteur' => 'Листоноша (Facteur)',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Перевірка SSL сертифіката',
	'configuration_mailer' => 'Надсилати за допомогою',
	'configuration_smtp' => 'Оберіть спосіб надсилання:',
	'configuration_smtp_descriptif' => 'Якщо не знаєте що вибрати - використовуйте mail() PHP.',
	'corps_email_de_test' => 'Тестовий лист',

	// E
	'email_envoye_par' => 'Відправлено з сайту @site@',
	'email_test_envoye' => 'Тестовий електронний лист успішно надіслано. Якщо ви його отримали і лист відображається некоректно, перевірте налаштування вашого сервера або зверніться до його адміністратора. ',
	'erreur' => 'Помилка',
	'erreur_dans_log' => ': більше подробиць може бути у лог файлі (tmp/logs)',
	'erreur_envoi_bloque_constante' => 'Надсилання листа заборонено константою <tt>_TEST_EMAIL_DEST</tt>.
Перевірте файл <tt>mes_options.php</tt>',
	'erreur_generale' => 'В налаштуваннях плагіну одна чи декілька помилок, вони показані у формі. ',
	'erreur_invalid_host' => 'Назва хоста вказана некоректно',
	'erreur_invalid_port' => 'Номер порту вказано з невірно',

	// F
	'facteur_adresse_envoi_email' => 'Ємейл:',
	'facteur_adresse_envoi_nom' => 'Імя:',
	'facteur_bcc' => 'Прихована копія (BCC) :',
	'facteur_cc' => 'Копія (CC) :',
	'facteur_copies' => 'Копії',
	'facteur_copies_descriptif' => 'Ємейли будуть додатково надсилатися на вказані адреси. В кожному полі можна вказати лише один ємейл.',
	'facteur_email_test' => 'Отримувач тестового ємейлу:',
	'facteur_filtre_accents' => 'Перетворювати спеціальні символи у HTML коди.',
	'facteur_filtre_css' => 'Перетворити css стилі, розміщені у секції <head></head>  у inline.',
	'facteur_filtre_images' => 'Вбудовувати код зображень у лист',
	'facteur_filtre_iso_8859' => 'Конвертувати у ISO-8859-1',
	'facteur_filtres' => 'Обробка',
	'facteur_filtres_descriptif' => 'Перетворення та операції з листом перед надсиланням.',
	'facteur_smtp_auth' => 'Необхідна аутентифікація:',
	'facteur_smtp_auth_non' => 'ні',
	'facteur_smtp_auth_oui' => 'так',
	'facteur_smtp_host' => 'Хост:',
	'facteur_smtp_password' => 'Пароль:',
	'facteur_smtp_port' => 'Порт:',
	'facteur_smtp_secure' => 'Безпека з’єднання :',
	'facteur_smtp_secure_non' => 'жодної',
	'facteur_smtp_secure_ssl' => 'SSL (застаріло)',
	'facteur_smtp_secure_tls' => 'TLS (рекомендовано)',
	'facteur_smtp_sender' => 'Ємейл для відповіді (необов’язково)',
	'facteur_smtp_sender_descriptif' => 'Ємейл, на який будуть надходити відповіді на ваш лист (Return-Path)',
	'facteur_smtp_tls_allow_self_signed_non' => 'SSL сертифікат SMTP сервера випущений центром сертифікації (рекомендовано).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'SSL сертифікат SMTP сервера випущено самостійно',
	'facteur_smtp_username' => 'Логін :',

	// I
	'info_envois_bloques_constante' => 'Всі відправлення заблоковані константою <tt>_TEST_EMAIL_DEST</tt>.',
	'info_envois_forces_vers_email' => 'Всі листи надсилаються на ємейл <b>@email@</b> через значення константи <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_email_test_from' => 'Відправник',
	'label_email_test_from_placeholder' => 'from@example.org (необов’язково)',
	'label_email_test_important' => 'Важливий ємейл',
	'label_facteur_forcer_from' => 'Відправляти з цієї адреси коли ємейл у полі <tt>From</tt> належить іншому домену',
	'label_message_envoye' => 'Надіслати ємейл:',

	// M
	'message_identite_email' => 'В <a href="@url@">налаштуваннях плагіну <i>Листоноша</i></a> для надсилання листів використовується  ємейл <b>@email@</b>.',

	// N
	'note_test_configuration' => 'На цю адресу буде надіслано тестовий емейл ',

	// P
	'personnaliser' => 'Змінити',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] ПОМИЛКА  - не вийшло відправити @dest@ (тема : @sujet@)',

	// T
	'tester' => 'Перевірити',
	'tester_la_configuration' => 'Перевірити налаштування',
	'titre_configurer_facteur' => 'Налаштування Листоноші',

	// U
	'utiliser_mail' => 'Використувувати функцію <tt>mail()</tt> PHP',
	'utiliser_reglages_site' => 'Використати налаштування сайту
',
	'utiliser_smtp' => 'Використовувати SMTP',

	// V
	'valider' => 'Зберегти',
	'version_html' => 'У форматі HTML',
	'version_texte' => 'У текстовому форматі',
];
