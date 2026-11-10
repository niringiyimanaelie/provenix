<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=ru
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'Настройки плагина успешно сохранены',
	'configuration_adresse_envoi' => 'Информация об отправителе',
	'configuration_facteur' => 'Почтальйон (Facteur)',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Сертификат SSL',
	'configuration_mailer' => 'Отправлять при помощи:',
	'configuration_smtp' => 'Выберите способ отправки',
	'configuration_smtp_descriptif' => 'Если не знаете что выбрать - используете mail() PHP.',
	'corps_email_de_test' => 'Тестовое письмо',

	// E
	'email_envoye_par' => 'Отправлено с сайта @site@',
	'email_test_envoye' => 'Тестовый электронное письмо успешно отправлено. Если вы его получили и письмо отображается некорректно, проверьте настройки вашего сервера или обратитесь к администратору. ',
	'erreur' => 'Ошибка',
	'erreur_dans_log' => ' :  больше подробностей может быть в лог файле',
	'erreur_envoi_bloque_constante' => 'Отправка писем запрещена значением константы <tt>_TEST_EMAIL_DEST</tt>.
Проверьте файл <tt>mes_options.php</tt>',
	'erreur_generale' => 'Обнаружено одна или несколько ошибок в настройках плагина. Пожалуйста, проверьте содержимое формы. ',
	'erreur_invalid_host' => 'Название хоста указано неверно',
	'erreur_invalid_port' => 'Номер порта указан неверно',

	// F
	'facteur_adresse_envoi_email' => 'Емейл:',
	'facteur_adresse_envoi_nom' => 'Имя:',
	'facteur_bcc' => 'Скрытая копия (BCC) :',
	'facteur_cc' => 'Копия (CC) :',
	'facteur_copies' => 'Отправлять копии писем',
	'facteur_copies_descriptif' => 'Емейлы будут дополнительно направляться на указанные адреса. В каждом поле можно указать только один емейл. ',
	'facteur_email_test' => 'Получатель тестового емейла:',
	'facteur_filtre_accents' => 'Кодировать специальные символы в HTML коді.',
	'facteur_filtre_css' => 'Транформировать css стили из секции <head> </ head> в inline.',
	'facteur_filtre_images' => 'Встраивать код изображений в тело письма',
	'facteur_filtre_iso_8859' => 'Конвертировать текст письма в  ISO-8859-1',
	'facteur_filtres' => 'Обработка',
	'facteur_filtres_descriptif' => 'Преобразования письма перед отправкой.',
	'facteur_smtp_auth' => 'Необходима авторизация :',
	'facteur_smtp_auth_non' => 'нет',
	'facteur_smtp_auth_oui' => 'да',
	'facteur_smtp_host' => 'Сервер :',
	'facteur_smtp_password' => 'Пароль :',
	'facteur_smtp_port' => 'Порт :',
	'facteur_smtp_secure' => 'Безопасность подключения:',
	'facteur_smtp_secure_non' => 'нет',
	'facteur_smtp_secure_ssl' => 'SSL (устаревшее)',
	'facteur_smtp_secure_tls' => 'TLS (рекомендуется)',
	'facteur_smtp_sender' => 'Емейл для ответов (опционально)',
	'facteur_smtp_sender_descriptif' => 'Емейл, на который будут отправляться ответы на письмо (Return-Path)',
	'facteur_smtp_tls_allow_self_signed_non' => 'SSL сертификат SMTP сервера выпущен авторизированным центром ( рекомендуется) ',
	'facteur_smtp_tls_allow_self_signed_oui' => 'SSL сертификат SMTP сервера выпущен самостоятельно',
	'facteur_smtp_username' => 'Логин:',

	// I
	'info_envois_bloques_constante' => 'Все отправления писем заблокировані константой <tt>_TEST_EMAIL_DEST</tt>.',
	'info_envois_forces_vers_email' => 'В константе <tt>_TEST_EMAIL_DEST</tt>задано отправлять все письма на адрес <b>@email@</b>',

	// L
	'label_email_test_from' => 'Отправитель',
	'label_email_test_from_placeholder' => 'from@example.org (опционально)',
	'label_email_test_important' => 'Важный емейл',
	'label_facteur_forcer_from' => 'Отправлять письма с  адреса <tt>From</tt> когда емейл принадлежит другому домену',
	'label_message_envoye' => 'Отправлять емейлы :',

	// M
	'message_identite_email' => 'В <a href="@url@">настройках плагина <i>Почтальйон</i></a> емей л<b>@email@</b> используется для отправки писем.',

	// N
	'note_test_configuration' => 'Тестовое письмо будет отправлено на этот адрес.',

	// P
	'personnaliser' => 'Изменить',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] Сбой - не удалось отправить @dest@ (тема : @sujet@)',

	// T
	'tester' => 'Отправить тестовое сообщение',
	'tester_la_configuration' => 'Проверить отправку писем',
	'titre_configurer_facteur' => 'Настройки плагина Почтальон',

	// U
	'utiliser_mail' => 'Использовать фнукцию <tt>mail()</tt> PHP',
	'utiliser_reglages_site' => 'использовать настрйоки сайта SPIP',
	'utiliser_smtp' => 'Использовать SMTP сервер',

	// V
	'valider' => 'Сохранить',
	'version_html' => 'В формате HTML',
	'version_texte' => 'В текстовом формате',
];
