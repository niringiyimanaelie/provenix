# Provenix public website

The marketing website runs on the existing SPIP installation. The Python/React product in `E:\Documents\GitHub\engine` remains a separate application.

## Pages

- Home: `/provenix/`
- About: `/provenix/spip.php?page=about`
- Services: `/provenix/spip.php?page=services`
- Contact: `/provenix/spip.php?page=contact`

## Editing website content in SPIP

Log into `/provenix/ecrire/`, then choose **Edit → Website content**.
Direct link: `http://localhost:8080/provenix/ecrire/?exec=provenix_contenu`.

The editor has **Home**, **About us**, **Services**, **Contact**, and **Shared content** tabs. Each page tab now lists its **native SPIP articles**. Click an article to open its normal SPIP editor. The remaining fields below the article list are page settings for labels, illustration text, and form behavior.

You can also go directly to **Edit → Sections** or **Edit → Articles**. There are four main sections (Home, About Us, Services, Contact), plus **Our services** inside Services. The 22 original content blocks are published articles associated with the existing webmaster. Article revision history is enabled.

Native articles use SPIP's normal publication workflow. Changes to a published article appear publicly after saving. Drafts remain unpublished until their status is changed to published. Service articles appear on both the homepage cards and Services page only when published. Adding a service article to **Services → Our services** adds another entry automatically; article IDs are not hardcoded in the public templates.

## Article fields and the existing design

| SPIP field | Website use |
| --- | --- |
| Over-title / surtitre | Small label above a heading, or service number/label |
| Title | First heading line, or service title |
| Subtitle | Second heading line; for services, the shorter homepage card title |
| Description | Purple third line for the homepage introduction/capabilities block; homepage summary for service articles |
| Introduction / chapo | Introductory paragraph |
| Text / body | Paragraphs or service bullet points |
| Postscript | Homepage introduction's small closing note |
| Referenced site's name | Existing button/link label where used |

For service bullet points, put one item per line using SPIP's `-* ` list notation. About Us and the homepage story render the full article body, including new paragraphs and SPIP text formatting. Some short design blocks have separate title/subtitle fields to preserve the original line breaks. Contact information uses two introduction lines separated by a newline. The design remains theme-controlled.

**Shared content** contains navigation, footer, the shared contact banner, search description, and the public contact email. Changing that email updates visible addresses, email links, and the contact form recipient together. Contact contains the form labels, topics, placeholders, and email-draft messages. The `{email}` token in the status message is replaced with the current contact address.

Main content is stored in **`spip_articles`**, organized by **`spip_rubriques`**, with authors in `spip_auteurs_liens` and history in SPIP revision tables. Shared settings remain in `spip_meta` under `provenix_contenu`. The `provenix_articles` and `provenix_sections` settings map design slots to native object IDs. The pre-migration configuration is retained as `provenix_contenu_avant_articles` for recovery, but is never used as a public fallback. Unpublished articles do not fall back to old settings.

The shared-settings editor requires an unrestricted administrator and uses SPIP's signed CVT forms. Native article permissions apply to the article editor. Native article edits and settings saves invalidate public caches automatically. Database backups preserve all content.

Custom templates in `squelettes/` contain layout and CMS field references. Shared navigation, metadata, footer, and call-to-action use `inc-*.html`. Styles, navigation behavior, contact form behavior, and the PX favicon are in `squelettes/assets/`. The original CSS and layout are preserved. Decorative icons, diagram connectors, and page routing remain theme code.

## CMS plugin

The local `plugins/provenix_site/` plugin supplies the content directory, settings editor, native-article bindings, and installation migrations. Keep this plugin active. `article-plan.json` defines the initial content-to-article migration. `initial-content.json` is an installation seed only: public pages read the database, and never fall back to these seed values. Reinstalling preserves existing editorial values and articles. Uninstalling also preserves them.

For another SPIP installation, copy the theme and plugin, then enable **Provenix website content** under plugin management. Alternatively, run `C:\xampp\php\php.exe tools\install_cms.php` from the project root. This installer is CLI-only and preserves the other active plugins.

`tools/extract_cms_content.py` is the one-time migration from the original static templates; it refuses to rerun on migrated templates. Do not run it for normal content editing. All `tools/` HTTP access is denied.

## Verification

- `php tools/test_cms.php anonymous`: verifies anonymous access is rejected.
- `php tools/test_cms.php`: renders each settings tab, checks signed forms and validation, then makes and restores one temporary navigation-label edit. Run only on a local development site.
- `php tools/test_articles.php`: checks native articles, idempotency, a reversible article edit, draft visibility on both pages, republishing, and revision records. Run only locally; temporary test edits are restored, and revision history records those test changes.
- `python tools/verify_cms.py articles`: compares public markup, text, and attributes to the snapshots captured immediately before the native-article migration in ignored `tmp/`. These snapshots are local verification artifacts and are not included in Git.
- `node --check squelettes/assets/site.js`: JavaScript syntax check.

The contact form validates inputs then opens the visitor's mail client with a draft addressed to `info@getprovenix.com`. It does not send or store submissions on the server. Actual server delivery would require a configured mail service and a protected SPIP form handler.

Fonts use Google Fonts with local system-font fallbacks. No application credentials, customer claims, or invented testimonials are included. The homepage lineage diagram is explicitly illustrative.

CMS saves invalidate the cache. After changing theme code, use SPIP's cache maintenance controls or reload with `var_mode=recalcul` while logged in as an administrator if needed.

Git excludes SPIP runtime files, uploads, local credentials, and installed core directories. A deployment needs a matching SPIP installation and separately managed database/uploads; this repository is not a complete site backup.

## Reading and formatting the source

Start with `squelettes/sommaire.html` for the homepage layout, and
`squelettes/assets/site.css` for its appearance. The other page templates sit
beside `sommaire.html`; reusable page parts use the `inc-` prefix.

Templates use two-space indentation; PHP and Python use four spaces.
`.editorconfig` records these conventions. The workspace's VS Code settings
turn on word wrap so long CMS expressions and text values fit the editor.
Press **Alt+Z** to toggle word wrap. Wrapping in the editor does not change files.

Keep adjacent inline elements together when whitespace affects the displayed
text, such as the dot after the brand name. Keep SPIP expressions intact when
formatting templates. The formatting of source files is separate from the
content edited in `/ecrire/` and does not change database records.
