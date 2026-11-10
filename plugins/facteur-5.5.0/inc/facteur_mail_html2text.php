<?php

/**
 * Plugin Facteur 4
 * (c) 2009-2019 Collectif SPIP
 * Distribue sous licence GPL
 *
 * @package SPIP\Facteur\Inc\Facteur_mail_html2text
 */

use Spip\Texte\Collecteur\HtmlTag as CollecteurHtmlTag;

/**
 * Transformer un mail HTML en mail Texte proprement :
 * - les tableaux de mise en page sont utilisés pour structurer le mail texte
 * - le reste du HTML est markdownifie car c'est un format texte lisible et conventionnel
 *
 * @param string $html
 * @return string
 */
function inc_facteur_mail_html2text_dist($html, $options = []) {
	include_spip('inc/filtres');
	include_spip('inc/charsets');
	include_spip('lib/markdownify/src/Parser');
	include_spip('lib/markdownify/src/Converter');
	include_spip('lib/markdownify/src/ConverterExtra');
	$converter = new Markdownify\ConverterExtra(Markdownify\ConverterExtra::LINK_IN_PARAGRAPH, false, false);
	$converter->setAddCssClass(false);

	// nettoyer les balises de mise en page html
	$html = preg_replace(',</(td|th)>,Uims', '<br/>', $html);
	$html = preg_replace(',</(table)>,Uims', '@@@hr@@@', $html);
	$html = preg_replace(',</?(!doctype|html|body|table|td|th|tbody|thead|center|article|section|span)[^>]*>,Uims', "\n\n", $html);

	// commentaires html et conditionnels
	$html = preg_replace(',<!--.*-->,Uims', "\n", $html);
	$html = preg_replace(',<!\[.*\]>,Uims', "\n", $html);

	// les balises que l'on traite spécifiquement pour aider markdownify
	$html = facteur_figure_prepare($html);
	// découpage des lignes de blockquotes cf #51
	$html = facteur_blockquote_prepare($html, ($options['wordwrap_width'] ?? 75) -2);

	// preparation au paragraphage
	$html = preg_replace(',<(/?)(div|tr|caption)([^>]*>),Uims', "<\\1p>", $html);
	$html = preg_replace(',(<p>\s*)+,ims', '<p>', $html);
	$html = preg_replace(',<br/?>\s*</p>,ims', '</p>', $html);
	$html = preg_replace(',</p>\s*<br/?>,ims', '</p>', $html);
	$html = preg_replace(',(</p>\s*(@@@hr@@@)?\s*)+,ims', "</p>\\2", $html);
	$html = preg_replace(',(<p>\s*</p>),ims', '', $html);

	// succession @@@hr@@@<hr> et <hr>@@@hr@@@
	$html = preg_replace(',@@@hr@@@\s*(<[^>]*>\s*)?<hr[^>]*>,ims', "@@@hr@@@\n", $html);
	$html = preg_replace(',<hr[^>]*>\s*(<[^>]*>\s*)?@@@hr@@@,ims', "\n@@@hr@@@", $html);

	$html = preg_replace(',<textarea[^>]*spip_cadre[^>]*>(.*)</textarea>,Uims', "<code>\n\\1\n</code>", $html);

	// vider le contenu de qqunes :
	$html = preg_replace(',<head[^>]*>.*</head>,Uims', "\n", $html);

	// les images par leur alt ?
	// au moins les puces
	$imgs = extraire_balises($html, 'img');
	foreach ($imgs as $img) {
		$alt = extraire_attribut($img, 'alt');
		if (!empty($alt)) {
			// on remplace l'image par son alt (y compris les img puce donc)
			$html = str_replace($img, $alt, $html);
		} else {
			// on vire celles sans alt
			$html = str_replace($img, '', $html);
		}
	}
	// etre sur de tout bien avoir nettoyé, y compris des balises mal écrites
	if (stripos($html, '<img') !== false|| stripos($html, '</img') !== false) {
		$html = preg_replace(',</?(img)[^>]*>,Uims', "\n", $html);
	}

	// Liens :
	$links = extraire_balises($html, 'a');
	$prelinks = $postlinks = [];
	foreach ($links as $k => $link) {
		$href = extraire_attribut($link, 'href');
		//$name = extraire_attribut($link, 'name');
		//$class = extraire_attribut($link, 'class');
		$texte_lien = trim(strip_tags($link));
		if (strpos($href, '#') === 0
		  || strpos($href, 'mailto:') === 0
		  || strpos($href, 'javascript:') === 0
		) {
			// tous les liens internes, y compris les notes de bas de page
			$html = str_replace($link, $texte_lien, $html);
		} else {
			$linkreplace = "@@@link$k@@@";
			$url = str_replace('&amp;', '&', $href);
			if ($texte_lien === $href || $texte_lien === $url) {
				// si le texte est l'url :
				$prelinks[$link] = $linkreplace;
			}
			else {
				// texte + url
				if (str_contains($texte_lien, '<')) {
					try {
						$texte_lien = $converter->parseString($texte_lien);
					} catch (Exception $e) {
						spip_log("inc_facteur_mail_html2text_dist: Echec conversion html->markdown dans un lien: " . $e->getMessage(), 'facteur' . _LOG_ERREUR);
						$texte_lien = strip_tags($texte_lien);
					}
				}
				$texte_lien = trim(unicode2charset(html2unicode($texte_lien)));
				$prelinks[$link] = $texte_lien . " ($linkreplace)";
			}
			// passer l'url en absolu dans le texte sinon elle n'est pas clicable ni utilisable
			$postlinks[$linkreplace] = url_absolue($url);
		}
	}
	$html = str_replace(array_keys($prelinks), array_values($prelinks), $html);

	// espaces
	$html = str_replace('&nbsp;', ' ', $html);
	$html = preg_replace(',<p>\s+,ims', '<p>', $html);

	#return $html;
	try {
		$texte = $converter->parseString($html);
	} catch (Exception $e) {
		spip_log("inc_facteur_mail_html2text_dist: Echec conversion html->markdown : " . $e->getMessage(), 'facteur' . _LOG_ERREUR);
	}

	// avant de réinsérer les link, corriger les entités html
	include_spip('inc/charsets');
	$texte = unicode2charset(html2unicode($texte));
	// entites restantes ? (dans du code...)
	$texte = str_replace(['&#039;', '&#034;'], ["'",'"'], $texte);

	$texte = str_replace(array_keys($postlinks), array_values($postlinks), $texte);


	// trim et sauts de ligne en trop ou pas assez
	$texte = trim($texte);
	$texte = str_replace("<br />\n", "\n", $texte);
	$texte = preg_replace(',(@@@hr@@@\s*)+$,ims', "@@@hr@@@\n", $texte);
	$texte = preg_replace(",(@@@hr@@@\s*\n)+,ims", "\n\n" . str_pad('-', 75, '-') . "\n\n", $texte);
	$texte = preg_replace(",(\n#+\s),ims", "\n\n\\1", $texte);
	$texte = preg_replace(",(\n\s*)(\n\s*)+(\n)+,ims", "\n\n\n", $texte);


	// <p> et </p> restants
	$texte = str_replace(['<p>','</p>'], ['',''], $texte);

	// Faire des lignes de 75 caracteres maximum
	if ($options['wordwrap'] ?? true) {
		$texte = facteur_mail_texte_wrap($texte, $options['wordwrap_width'] ?? 75);
	}
	return trim($texte) . "\n";
}

/**
 * Wrapper un texte de mail sans wrapper les citations qui l'ont déjà été
 * @param string $texte
 * @param int $wordwrap_width
 * @return string
 */
function facteur_mail_texte_wrap(string $texte, int $wordwrap_width) {
	$texte = explode("\n", $texte);
	$texte = array_map(fn($line) => str_starts_with($line, '>') ? $line : wordwrap($line, $wordwrap_width), $texte);
	return implode("\n", $texte);
}

function facteur_figure_prepare(string $html) {
	if (stripos($html, '<figure') !== false
		&& class_exists('Spip\Texte\Collecteur\HtmlTag')) {
		$facteur_mail_html2text = charger_fonction('facteur_mail_html2text', 'inc');
		$htmlFigureCollecteur = new CollecteurHtmlTag('figure');
		$htmlFigcaptionCollecteur = new CollecteurHtmlTag('figcaption');
		$collection = $htmlFigureCollecteur->collecter($html);
		foreach (array_reverse($collection) as $occurence) {
			$figure = trim($occurence['innerHtml']);
			$captions = $htmlFigcaptionCollecteur->collecter($figure);
			foreach (array_reverse($captions) as $caption) {
				$legende = $facteur_mail_html2text($caption['innerHtml']);
				$legende = "– " . preg_replace(",\n+,", " | ", $legende);
				$figure = substr_replace($figure, $legende, $caption['pos'], $caption['length']);
			}
			$figure = preg_replace(',</?(source)[^>]*>,Uims', "\n\n", $figure);
			if ($img = extraire_balise($figure, 'img')) {
				$a = extraire_balise($figure, 'a');
				$href = (empty($a) ? '' : extraire_attribut($a, 'href'));
				$src = $href ?: extraire_attribut($img, 'src');
				$alt = extraire_attribut($img, 'alt');
				if ($a) {
					$figure = str_replace($a, "", $figure);
				}
				$figure = str_replace($img, "", $figure);
				$figure = "&lt;$src&gt;" . ($alt ? " ($alt)" : "") . "<br />\n" . $figure;
			}
			$html = substr_replace($html, $figure, $occurence['pos'], $occurence['length']);
		}
	}
	return $html;
}

/**
 * Wrapp le contenu de chaque blockquote
 * pour que le futur rendu markdown paraisse OK dans les lecteurs de mails
 */
function facteur_blockquote_prepare(string $html, int $wordwrap_width = 75): string {
	if (stripos($html, '<blockquote') !== false) {
		// ce traitement des blockqote n'est applicable qu'à partir de SPIP 4.3
		// pour les versions plus ancinnes de SPIP on garde le traitement par défaut de markdownify
		if (class_exists('Spip\Texte\Collecteur\HtmlTag')) {
			$htmlTagCollecteur = new CollecteurHtmlTag('blockquote');
			$collection = $htmlTagCollecteur->collecter($html);
			foreach (array_reverse($collection) as $blockquote) {
				$quote = trim($blockquote['innerHtml']);
				if (strlen($quote)) {
					if (str_contains($quote, '<')) {
						$facteur_mail_html2text = charger_fonction('facteur_mail_html2text', 'inc');
						$quote = $facteur_mail_html2text($quote, ['wordwrap' => false, 'wordwrap_width' => $wordwrap_width]);
					}
					$quote = facteur_mail_texte_wrap($quote, $wordwrap_width);
					$quote = explode("\n", $quote);
					$quote = implode("<br />\n", $quote);
					$quote = $blockquote['opening'] . "\n" . $quote . "\n" . $blockquote['closing'] . "\n";
				}
				$html = substr_replace($html, $quote, $blockquote['pos'], $blockquote['length']);
			}
		}
	}
	return $html;
}
