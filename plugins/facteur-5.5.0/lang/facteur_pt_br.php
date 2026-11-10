<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/facteur?lang_cible=pt_br
// ** ne pas modifier le fichier **

return [

	// C
	'config_info_enregistree' => 'A configuração do Carteiro foi gravada corretamente',
	'configuration_adresse_envoi' => 'Endereço de envio padrão',
	'configuration_facteur' => 'Carteiro',
	'configuration_facteur_smtp_tls_allow_self_signed' => 'Validação do certificado SSL',
	'configuration_mailer' => 'Método de envio',
	'configuration_smtp' => 'Seleção do método de envio de e-mail',
	'configuration_smtp_descriptif' => 'Se tiver dúvida, escolha a função mail do PHP.',
	'corps_email_de_test' => 'Este é um e-mail de teste acentuado',

	// E
	'email_envoye_par' => 'Enviado por @site@',
	'email_test_envoye' => 'O e-mail de teste foi enviado corretamente. Se você não o receber, verifique a configuração do seu servidor ou contate o administrador do servidor.',
	'erreur' => 'Erro',
	'erreur_confirm_ip_sans_hostname' => 'Você quer realmente usar este endereço IP como host de SMTP?',
	'erreur_dans_log' => ' : consulte o arquivo de log para obter mais detalhes',
	'erreur_envoi_bloque_constante' => 'Envio bloqueado pela constante <tt>_TEST_EMAIL_DEST</tt>.
Verifique o seu arquivo <tt>mes_options.php</tt>',
	'erreur_generale' => 'Há um ou mais erros de configuração. Por favor, verifique o conteúdo do formulário.',
	'erreur_invalid_host' => 'Este nome de host não está correto',
	'erreur_invalid_port' => 'Este número de porta não está correto',
	'erreur_ip_sans_hostname' => 'Este endereço IP não corresponde a nenhum nome de domínio.',

	// F
	'facteur_adresse_envoi_email' => 'E-mail:',
	'facteur_adresse_envoi_nom' => 'Nome:',
	'facteur_bcc' => 'Cópia Oculta (BCC):',
	'facteur_cc' => 'Cópia (CC):',
	'facteur_copies' => 'Cópias',
	'facteur_copies_descriptif' => 'Um e-mail será enviado em cópia para os endereços especificados. Um único endereço em cópia e/ou um único endereço em cópia oculta.',
	'facteur_email_test' => 'Enviar um e-mail de teste para:',
	'facteur_filtre_accents' => 'Transformar os acentos em entidades HTML (útil especialmente para o Hotmail).',
	'facteur_filtre_css' => 'Transformaros estílos contidos entre <head> e </head> em estilos "em linha", útil para os webmails, já que os estilos em linha têm prioridade sobre os estilos externos.',
	'facteur_filtre_images' => 'Embutir as imagens referenciadas no próprio e-mail',
	'facteur_filtre_iso_8859' => 'Converter em ISO-8859-1',
	'facteur_filtres' => 'Filtros',
	'facteur_filtres_descriptif' => 'Filtros podem ser aplicados aos e-mails, no momento do envio.',
	'facteur_smtp_auth' => 'Requer autenticação:',
	'facteur_smtp_auth_non' => 'não',
	'facteur_smtp_auth_oui' => 'sim',
	'facteur_smtp_host' => 'Host:',
	'facteur_smtp_password' => 'Senha:',
	'facteur_smtp_port' => 'Porta:',
	'facteur_smtp_secure' => 'Conexão segura:',
	'facteur_smtp_secure_non' => 'náo',
	'facteur_smtp_secure_ssl' => 'SSL (obsoleto)',
	'facteur_smtp_secure_tls' => 'TLS (recomendado)',
	'facteur_smtp_sender' => 'Endereço para o retorno dos erros (opcional)',
	'facteur_smtp_sender_descriptif' => 'Informa, no cabeçalho da mensagem, o endereço de e-mail de retorno dos erros (ou Return-Path)',
	'facteur_smtp_tls_allow_self_signed_non' => 'o certificado SSL do servidor SMTP foi emitido por uma Autoridade Certificadora (recomendado).',
	'facteur_smtp_tls_allow_self_signed_oui' => 'o certificado SSL do servidor SMTP é auto-assinado.',
	'facteur_smtp_username' => 'Nome do usuário:',

	// I
	'info_envois_bloques_constante' => 'Todos os envios estão bloqueados pela contante <tt>_TEST_EMAIL_DEST</tt>.',
	'info_envois_forces_vers_email' => 'Todos os envios são forçados para o endereço <b>@email@</b> pela constante <tt>_TEST_EMAIL_DEST</tt>',

	// L
	'label_email_test_avec_piece_jointe' => 'Com um anexo',
	'label_email_test_from' => 'Remetente',
	'label_email_test_from_placeholder' => 'from@example.org (opcional)',
	'label_email_test_important' => 'Este e-mail é importante',
	'label_facteur_forcer_from' => 'Forçar o endereço de envio quando o <tt>From</tt> não é no mesmo domínio.',
	'label_message_envoye' => 'Mensagem enviada:',

	// M
	'message_identite_email' => 'A <a href="@url@">configuração do plugin "Carteiro"</a> sobrecarrega este endereço de e-mail com <b>@email@</b> para o envio das mensagens.',

	// N
	'note_test_configuration' => 'Um e-mail será enviado a este endereço.',

	// P
	'personnaliser' => 'Personalizar essas configurações',

	// S
	'sujet_alerte_mail_fail' => '[MAIL] FALHA no envio para @dest@ (assunto: @sujet@)',

	// T
	'tester' => 'Testar',
	'tester_la_configuration' => 'Testar a configuração',
	'titre_configurer_facteur' => 'Configuração do Carteiro',

	// U
	'utiliser_mail' => 'Usar a função <tt>mail()</tt> do PHP',
	'utiliser_reglages_site' => 'Usar as configurações do site SPIP',
	'utiliser_smtp' => 'Usar SMTP',

	// V
	'valider' => 'Validar',
	'version_html' => 'Versão HTML.',
	'version_texte' => 'Versão texto.',
];
