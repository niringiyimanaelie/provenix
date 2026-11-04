<?php
/** Explain article placement only inside SPIP's private administration pages. */
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

function provenix_site_affiche_milieu($flux)
{
    $exec = $flux['args']['exec'] ?? '';
    if (!in_array($exec, ['article', 'article_edit', 'rubrique'], true)) {
        return $flux;
    }
    include_spip('inc/provenix_articles');
    include_spip('inc/config');
    include_spip('inc/autoriser');
    $object = $exec === 'article_edit' ? 'article' : $exec;
    $id = (int) ($flux['args']['id_' . $object] ?? 0);
    if (!$id || !autoriser('voir', $object, $id)) {
        return $flux;
    }
    $article_ids = lire_config('provenix_articles', []);
    $section_ids = lire_config('provenix_sections', []);
    $items = [];
    $location = "";
    foreach (provenix_article_plan()['articles'] as $article) {
        $article_id = (int) ($article_ids[$article['role']] ?? 0);
        $matches = $object === 'article'
            ? $article_id === $id
            : (int) ($section_ids[$article['section']] ?? 0) === $id;
        if (!$matches || !$article_id || !autoriser('voir', 'article', $article_id)) {
            continue;
        }
        $location = $article['section'] === 'service_catalogue'
            ? 'Home and Services'
            : provenix_article_plan()['sections'][$article['section']];
        $label = htmlspecialchars($article['label'], ENT_QUOTES, 'UTF-8');
        // SPIP already escapes ampersands; do not encode those entities twice.
        $url = htmlspecialchars(
            generer_url_ecrire('article', 'id_article=' . $article_id),
            ENT_QUOTES,
            'UTF-8',
            false
        );
        $items[] = '<li style="margin:8px 0"><a href="' . $url . '"><strong>' . $label
            . '</strong></a></li>';
    }
    if ($items) {
        $heading = $object === 'article' ? 'Website placement' : 'Edit page content';
        $guide = '<div class="provenix-editor-guide" style="margin:20px 0;padding:16px;background:#f5f0ff;border-left:4px solid #7650cf">'
            . '<h2>' . $heading . '</h2>'
            . ($object === 'article'
                ? '<p><strong>Appears on:</strong> ' . htmlspecialchars($location, ENT_QUOTES, 'UTF-8') . '</p>'
                : '<p>Choose the part of this page you want to edit.</p>')
            . '<ul>' . implode('', $items) . '</ul>'
            . '<p><small>These labels are only visible in the administration area.</small></p></div>';
        $marker = '<!--affiche_milieu-->';
        $flux['data'] = strpos($flux['data'], $marker) !== false
            ? str_replace($marker, $guide . $marker, $flux['data'])
            : $guide . $flux['data'];
    }
    return $flux;
}
