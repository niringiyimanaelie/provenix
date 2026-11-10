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

You can also go directly to **Edit → Sections** or **Edit → Articles**. There are four main sections (Home, About Us, Services, Contact), plus **Our services** inside Services and **SQL platforms** inside Home. The 22 original content blocks are published articles associated with the existing webmaster. Article revision history is enabled.

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

**Shared content** contains navigation, footer, the shared contact banner, search description, and the public contact email. Changing that email updates visible addresses, email links, and the contact form recipient together. Contact contains the form labels, topics, placeholders, and server-side submission feedback.

Main content is stored in **`spip_articles`**, organized by **`spip_rubriques`**, with authors in `spip_auteurs_liens` and history in SPIP revision tables. Shared settings remain in `spip_meta` under `provenix_contenu`. The `provenix_articles` and `provenix_sections` settings map design slots to native object IDs. The pre-migration configuration is retained as `provenix_contenu_avant_articles` for recovery, but is never used as a public fallback. Unpublished articles do not fall back to old settings.

The shared-settings editor requires an unrestricted administrator and uses SPIP's signed CVT forms. Native article permissions apply to the article editor. Native article edits and settings saves invalidate public caches automatically. Database backups preserve all content.

Custom templates in `squelettes/` contain layout and CMS field references. Shared navigation, metadata, footer, and call-to-action use `inc-*.html`. Styles, navigation behavior, contact form behavior, and the PX favicon are in `squelettes/assets/`. The original CSS and layout are preserved. Decorative icons, diagram connectors, and page routing remain theme code.

## CMS plugin

The local `plugins/provenix_site/` plugin supplies the content directory, settings editor, native-article bindings, and installation migrations. Keep this plugin active. `article-plan.json` defines the initial content-to-article migration. `initial-content.json` is an installation seed only: public pages read the database, and never fall back to these seed values. Reinstalling preserves existing editorial values and articles. Uninstalling also preserves them.

For another SPIP installation, copy the theme and plugin, then enable **Provenix website content** under plugin management. Alternatively, run `C:\xampp\php\php.exe tools\install_cms.php` from the project root. This installer is CLI-only and preserves the other active plugins.

The original one-time template conversion scripts have been retired after the CMS migration. They remain recoverable in Git history. The plugin retains the migrations needed to install or upgrade the site. All `tools/` HTTP access is denied.

## Verification

- `php tools/test_article_guides.php`: read-only check that all mapped article links in the private section guides resolve to valid article IDs after HTML decoding.

- `php tools/test_cms.php anonymous`: verifies anonymous access is rejected.
- `php tools/test_cms.php`: renders each settings tab, checks signed forms and validation, then makes and restores one temporary navigation-label edit. Run only on a local development site.
- `php tools/test_articles.php`: checks native articles, idempotency, a reversible article edit, draft visibility on both pages, republishing, and revision records. Run only locally; temporary test edits are restored, and revision history records those test changes.
- `python tools/verify_cms.py articles`: compares public markup, text, and attributes to the snapshots captured immediately before the native-article migration in ignored `tmp/`. These snapshots are local verification artifacts and are not included in Git.
- `node --check squelettes/assets/site.js`: JavaScript syntax check.

The contact form uses SPIP's signed CVT submission and server-side mail API. Local messages are captured in Mailpit; hosted delivery must be configured and tested separately. See the server-side contact form section below.

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

## What belongs in the project

| Folder or files | Purpose |
| --- | --- |
| `squelettes/` | Custom website templates, styles, JavaScript, and icons |
| `plugins/provenix_site/` | CMS integration, settings forms, article mappings, and installation data |
| `tools/` | Local installer and verification scripts |
| `ecrire/`, `prive/`, `plugins-dist/`, `squelettes-dist/`, `vendor/` | SPIP and its dependencies; maintain through SPIP's upgrade process |
| `config/`, `.env` | Local configuration and credentials; keep private |
| `IMG/` | Uploaded media; include in site backups |
| `tmp/`, `local/` | SPIP working files and generated assets; let SPIP manage them |
| `.git/` | Version history and recovery |
| `.vscode/`, `.editorconfig` | Editor readability and formatting conventions |
| Root PHP, Composer, license, security, and SPIP metadata files | CMS entry points, dependency records, and maintenance information |

Remove temporary development dependencies once their job is complete. Retire
one-time conversion scripts after migration, keeping their history in Git.
Keep installation seeds and article mappings: the plugin still needs these,
even though normal public content comes from the database. Keep the local HTML
comparison snapshots in `tmp/` while using `tools/verify_cms.py`.

A clean Git repository excludes generated files and secrets; the working site
still needs its installed dependencies and runtime folders. Deleting those
folders merely to make the file explorer shorter can break the site.

## Internal article labels

The Website content directory and each mapped section use short labels such
as "Main introduction", "Our approach", and "Step 1: Connect your SQL".
Section lists do not repeat the page name. Individual article viewing and
editing screens show "Appears on: Home", "Appears on: About Us", or the
appropriate page separately. Shared service articles say "Home and Services".

These labels are private editing guidance, separate from the public article
title. Labels are defined in the plugin's `article-plan.json`; they do not
require an additional database field. The guide covers the 26 mapped articles, including three SQL platforms and the ontology capability.
New service articles still appear in SPIP's normal article list.

## SQL platform logos on the homepage

Go to **Edit > Sections > Home > SQL platforms**. Each published platform article
supplies its name (Title) and image (Article logo in the sidebar). The Home tab
of **Website content** also links to the three initial platform articles.

The row currently contains Snowflake, Databricks, and MotherDuck. MotherDuck is
the cloud service built on DuckDB. Replace an image with SPIP's standard Article
logo controls. The template sizes logos consistently without recoloring or
stretching them. Without a logo, the article title appears instead. Draft or
unpublished platform articles are hidden; publishing another article in this
section adds another item. Sorting uses SPIP's numeric title prefix, then ID.

These are native SPIP articles and logo documents: database records store names,
publication status, and media links, while the actual files live under `IMG/`.
Back up and transfer both the database and `IMG/` when deploying. The plugin's
`media/` folder holds the initial official assets and source credits for a new
installation. Reinstalling does not overwrite logos that editors have changed
or deliberately removed after initial setup.

## Homepage illustrative lineage example

Under **Edit > Website content > Home**, find **Illustrative lineage demo**.
Its headings, source/table names, transformation steps, report labels, example
values, and instructions are editable CMS settings. The example uses orders
and refunds to explain net revenue by region; its amounts are illustrative,
not customer or company figures. If changing amounts, keep sales minus refunds
equal to net revenue for each region.

The three-stage layout is in `squelettes/sommaire.html`, with styling in
`assets/site.css`. The report is a static destination table, without selection
controls or trace highlighting. Source connectors use `assets/site.js` only
to follow card geometry. Without JavaScript the full example and calculation
remain readable. The row values are presentation content,
not a live query or connection to the separate product application.

The previous diagram settings were retained in the database under
`provenix_lineage_before` during migration for recovery.

## Ontology website content

**Services > Our services > Data ontology exploration** is a native published
article. Its subtitle, description, and link label populate the fourth Home
capability card; its title, introduction, and body populate the text-only
Services section at `/services#ontology`. Find it through the
Services tab of **Edit > Website content** as **Data ontology**.

The copy describes implemented search, column types, extracted formulas,
transformation categories, and upstream/downstream exploration. No ontology
illustration or diagram is embedded on the website. The About introduction
and current-product article also mention ontology; AI-assisted business
interpretation remains a future ambition. Normal edits use SPIP article fields.

## Public routes and shared icons

Marketing navigation uses `/about`, `/services`, and `/contact`, relative to
SPIP's configured public site address. The root `.htaccess` rewrites these
paths internally to their SPIP page templates; existing query-string URLs
remain valid. The administration area remains at `/ecrire/`.

Decorative arrows use the shared `inc-icon.html` include and the external
`assets/icons.svg` symbol file. Link labels remain editable native article
fields and contain words only; icons are part of the theme. Styles and
behaviour live in `assets/site.css` and `assets/site.js`. The head include
uses SPIP's `timestamp` filter to invalidate browser asset caches when those
files change.

The lineage connectors measure card bounds and update through ResizeObserver
and font-load notifications, so text wrapping does not detach their endpoints.
Without JavaScript the source, transformation, and report content remains
readable. Diagram amounts and labels continue to come from CMS content.

Temporary previews, upload archives, browser profiles, and repair scripts are
not project source. Database safety copies belong outside the web project.

Plugin schema 1.4.1 removes the retired trace instruction and migrates the
three existing link labels to text without decorative arrow suffixes. The
migration preserves custom wording and is safe to rerun. Apply the normal
plugin database upgrade when deploying this version; copying theme files
alone does not migrate existing hosted content. Private editor styles use
SPIP's `prive/style_prive_plugin_provenix_site.css` discovery mechanism.


## Server-side contact form (plugin 1.5.0)

The Contact page renders the native `#FORMULAIRE_PROVENIX_CONTACT` CVT form.
It posts to SPIP and calls its `envoyer_mail` API; submitting never opens the
visitor's email app. The recipient is the existing `contact_email` CMS setting.
The sender uses SPIP's configured `email_envoi`, falling back to the public
contact address; the visitor's validated address is Reply-To, never the sender
or recipient. Use an address on the site's domain as the configured sender.

Labels, feedback, and subject/body labels remain editable in Website content.
The 1.5.0 migration updates only the former default mail-app instructions,
adds missing feedback settings, and removes the retired browser-only status.
Custom wording and native article content are preserved. No message table is
created: messages are passed to mail delivery, not stored in the website DB.

Validation runs server-side as well as in HTML. Native signed CVT arguments,
a signed two-hour ticket, a honeypot, and a
five-attempts-per-ten-minutes IP limit reduce abuse. Atomic file locks prevent
concurrent duplicate submissions; ticket and content hashes suppress repeats
for two hours. Transport failure leaves the form editable and does not mark
it as sent. A successful result means the mail transport accepted the message,
not a guarantee that a remote inbox has delivered it. Email credentials, if
SMTP is configured later, belong in the host's private configuration, never
in browser code or source defaults.

Rate metadata lives under SPIP's protected `tmp/provenix-contact/` directory;
it contains hashes and times, not visitor messages or raw addresses. SPIP's
daily cron task removes expired files. If using a trusted reverse proxy,
configure SPIP's client-IP handling correctly before relying on IP limits.

For local testing, a portable Mailpit inbox is installed outside the project
under XAMPP. It listens only on `127.0.0.1:25` (SMTP) and
`http://127.0.0.1:8025` (inbox), with external relay disabled. Existing local
PHP SMTP settings use localhost port 25; no hosting credentials are required.
Messages stay in this local capture inbox. Start with `mailpit --smtp=127.0.0.1:25 --listen=127.0.0.1:8025 --smtp-relay-all=false` before testing; its
stopped state correctly causes the form to show a delivery error.

Local checks: `php tools/test_contact.php` covers validation, failed delivery,
retry, duplicates, and rate limits. `python tools/test_contact_http.py` exercises
the real signed CVT POST and captured envelope/headers. Both require Mailpit
and refuse to target a hosted site. Do not deploy the tools or local Mailpit.
Hosted delivery must be tested separately after configuring server mail.

### SMTP transport (Facteur 5.5.0)

The Provenix plugin requires the official Facteur plugin (unmodified release from
https://plugins.spip.net/facteur.html). Contact messages use its structured
`from`, `nom_envoyeur`, `repondre_a`, and `nom_repondre_a` API fields.

Local Facteur settings in the local database use SMTP 127.0.0.1, port 25,
no authentication and no encryption, exclusively for loopback Mailpit. These
settings are not part of deployment archives.

On hosting, activate Facteur and Provenix, then open
`ecrire/?exec=configurer_facteur`. Select SMTP and enter the secure outgoing
server and port shown in cPanel > Email Accounts > Connect Devices. Enable
authentication with the full mailbox username and enter its password privately
in the SPIP configuration form. Use matching TLS settings, and set the sender
to the business mailbox and site name. Do not copy localhost SMTP settings to
hosting, disable TLS verification, commit credentials, or import the local DB.
Test real delivery and confirm Reply addresses only the visitor before declaring
the hosted configuration complete. Previously received malformed messages will
not be repaired by changing the sender configuration.
