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
    foreach (provenix_article_plan()['articles'] as $article) {
        $article_id = (int) ($article_ids[$article['role']] ?? 0);
        $matches = $object === 'article'
            ? $article_id === $id
            : (int) ($section_ids[$article['section']] ?? 0) === $id;
        if (!$matches || !$article_id || !autoriser('voir', 'article', $article_id)) {
            continue;
        }
        $label = htmlspecialchars($article['label'], ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars(
            generer_url_ecrire('article', 'id_article=' . $article_id),
            ENT_QUOTES,
            'UTF-8'
        );
        $items[] = '<li><a href="' . $url . '"><strong>' . $label
            . '</strong></a> <small>(article ' . $article_id . ')</small></li>';
    }
    if ($items) {
        $heading = $object === 'article' ? 'Where this article appears' : 'Website content: where to edit';
        $guide = '<div class="provenix-editor-guide" style="margin:20px 0;padding:16px;background:#f5f0ff;border-left:4px solid #7650cf">'
            . '<h2>' . $heading . '</h2>'
            . '<p>Internal editing guide: these labels are not displayed on the public website. '
            . 'The article title remains the public heading.</p><ul>' . implode('', $items) . '</ul>'
            . '<p>To change the content, open the article and choose <strong>Edit this article</strong>.</p></div>';
        $marker = '<!--affiche_milieu-->';
        $flux['data'] = strpos($flux['data'], $marker) !== false
            ? str_replace($marker, $guide . $marker, $flux['data'])
            : $guide . $flux['data'];
    }
    return $flux;
}
