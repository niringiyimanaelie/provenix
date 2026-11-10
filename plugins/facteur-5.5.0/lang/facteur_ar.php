<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=ar
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'تم حفظ إعدادات ساعي البريد',
	'configuration_adresse_envoi' => 'العنوان الافتراضي للإرسال',
	'configuration_facteur' => 'ساعي البريد',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'تصديق شهادة SSL',
	'configuration_mailer' => 'طريقة الإرسال',
	'configuration_smtp' => 'اختيار طريقة إرسال البريد',
	'configuration_smtp_descriptif' => 'اذا لم تكونون متأكيدين، اختاروا وظيفة بريد PHP.',
	'corps_email_de_test' => 'هذه رسالة تجربة محرّكة',

	// E
	'email_envoye_par' => 'إرسال من @site@',
	'email_test_envoye' => 'تم إرسال بريد التجربة بنجاح. اذا لم يصلكم بشكل سليم، تأكدوا من سلامة إعداد جهاز الخدمة او اتصلوا بالمسؤول عن الجهاز.',
	'erreur' => 'خطأ',
	'erreur_confirm_ip_sans_hostname' => 'هل انتم متأكدون من استخدام عنوان IP هذا ليكون مضيف SMTP؟',
	'erreur_dans_log' => 'مراجعة ملف السجل لمزيد من التفاصيل:',
	'erreur_envoi_bloque_constante' => 'تم صد الارسال بواسطة <tt>_TEST_EMAIL_DEST</tt>. دققوا في ملف <tt>mes_options.php</tt>',
	'erreur_generale' => 'حصلت عدة أخطاء في الإعداد. الرجاء التحقق من محتوى الاستمارة.',
	'erreur_invalid_host' => 'اسم المضيف غير سليم',
	'erreur_invalid_port' => 'رقم المنفذ هذا غير صحيح',
	'erreur_ip_sans_hostname' => 'لا يتوافق عنوان IP هذا مع اي اسم نطاق.',

	// F
	'facteur_adresse_envoi_email' => 'عنوان البريد :',
	'facteur_adresse_envoi_nom' => 'الاسم:',
	'facteur_bcc' => 'نسخة مخبأة (BCC):',
	'facteur_cc' => 'نسخة (CC):',
	'facteur_copies' => 'نسخات',
	'facteur_copies_descriptif' => 'سيتم إرسال نسخة من البريد الى العناوين المحددة. عنوان واحد للنسخة و/او عنوان واحد للنسخة المخبأة.',
	'facteur_email_test' => 'إرسال بريد تجربة الى:',
	'facteur_filtre_accents' => 'تحويل الحركات الى كائنات HTML (مناسب لبريد Hotmail مثلاً).',
	'facteur_filtre_css' => 'تحويل الأنماط الموجودة بين <head> و</head> الى أنماط «مدرجة»، وهو مناسب للبريد على الشبكة اذ ان الأنماط المدرجة لها الأفضلية على الأنماط الخارجية.',
	'facteur_filtre_images' => 'إدراج الصور المشار اليها في الرسائل',
	'facteur_filtre_iso_8859' => 'تحويل الى ISO-8859-1',
	'facteur_filtres' => 'مرشحات',
	'facteur_filtres_descriptif' => 'يمكن ـإدخال مرشحات على الرسائل لدى الإرسال.',
	'facteur_smtp_auth' => 'يتطلب مصادقة:',
	'facteur_smtp_auth_non' => 'كلا',
	'facteur_smtp_auth_oui' => 'نعم',
	'facteur_smtp_host' => 'المضيف:',
	'facteur_smtp_password' => 'كلمة السر:',
	'facteur_smtp_port' => 'المنفذ:',
	'facteur_smtp_secure' => 'إتصال مؤمّن:',
	'facteur_smtp_secure_non' => 'كلا',
	'facteur_smtp_secure_ssl' => 'SSL (بائد)',
	'facteur_smtp_secure_tls' => 'TLS (مفضل)',
	'facteur_smtp_sender' => 'عنوان استقبال رسائل الأخطاء (إختيازي)',
	'facteur_smtp_sender_descriptif' => 'يحدد عنوان استقبال رسائل الأخطاء في ترويسة الرسالة (ما يعرف بـReturn-Path) ',
	'facteur_smtp_tls_allow_self_signed_non' => 'تم إصدار شهادة SSL لخادم SMTP من قبل سلطة معتمدة (المفضل).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'شهادة SSL لخادم SMTP شخصية.',
	'facteur_smtp_username' => 'اسم المستخدم:',

	// I
	'info_envois_bloques_constante' => 'كل الإرسالات مصدودة بواسطة <tt>_TEST_EMAIL_DEST</tt>.',
	'info_envois_forces_vers_email' => 'كل الإرسالات موجهة قسرياً الى العنوان <b>@email@</b> بواسطة <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_email_test_avec_piece_jointe' => 'لا يوجد ملف مرفق',
	'label_email_test_from' => 'المرسِل',
	'label_email_test_from_placeholder' => 'from@example.org (إختياري)',
	'label_email_test_important' => 'هذه الرسالة مهمة',
	'label_facteur_forcer_from' => 'فرض عنوان الإرسال هذا عندما لا يكون <tt>From</tt> في النطاق نفسه',
	'label_message_envoye' => 'تم إرسال البريد:',

	// M
	'message_identite_email' => 'يقوم <a href="@url@">إعداد الملحق <b>ساعي البريد</b></a> باستبدال هذا العنوان بـ<b>@email@</b> لإرسال البريد.',

	// N
	'note_test_configuration' => 'سيتم إرسال بريد الى هذا العنوان.',

	// P
	'personnaliser' => 'تخصيص الاعدادات',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] فشل إرسال الى @dest@ (كان: @sujet@)',

	// T
	'tester' => 'تجربة',
	'tester_la_configuration' => 'إختبار هذا الإعداد',
	'titre_configurer_facteur' => 'إعداد ساعي البريد',

	// U
	'utiliser_mail' => 'استخدام وظيفة <tt>mail()</tt> في PHP',
	'utiliser_reglages_site' => 'استخدام إعدادا موقع SPIP',
	'utiliser_smtp' => 'استخدام SMTP',

	// V
	'valider' => 'تصديق',
	'version_html' => 'نسخة HTML.',
	'version_texte' => 'نسخة نصية.',
];
