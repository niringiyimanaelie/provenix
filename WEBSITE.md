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

The editor has **Home**, **About us**, **Services**, **Contact**, and **Shared content** tabs. Edit the text fields, then click **Save and publish changes**. Fields are grouped by section; separate fields for heading lines preserve the original line breaks and purple emphasis. No HTML is needed.

**Shared content** contains navigation, footer, the shared contact banner, search description, and the public contact email. Changing that email updates visible addresses, email links, and the contact form recipient together. Contact contains the form labels, topics, placeholders, and email-draft messages. The `{email}` token in the status message is replaced with the current contact address.

The content is stored in the database's `spip_meta` table under `provenix_contenu`, through SPIP's native configuration API. These are structured CMS fields in the dedicated editor, not ordinary articles. They are included in database backups. The editor requires an unrestricted SPIP administrator and uses SPIP's signed CVT forms. Saves invalidate the public cache automatically.

Custom templates in `squelettes/` contain layout and CMS field references. Shared navigation, metadata, footer, and call-to-action use `inc-*.html`. Styles, navigation behavior, contact form behavior, and the PX favicon are in `squelettes/assets/`. The original CSS and layout are preserved. Decorative icons, diagram connectors, and page routing remain theme code.

## CMS plugin

The local `plugins/provenix_site/` plugin supplies the editor, schema, escaped public-content filter, and installation migration. Keep this plugin active. `initial-content.json` is an installation seed only: public pages read the database, and never fall back to this file. Reinstalling preserves existing editorial values. Uninstalling also preserves them.

For another SPIP installation, copy the theme and plugin, then enable **Provenix website content** under plugin management. Alternatively, run `C:\xampp\php\php.exe tools\install_cms.php` from the project root. This installer is CLI-only and preserves the other active plugins.

`tools/extract_cms_content.py` is the one-time migration from the original static templates; it refuses to rerun on migrated templates. Do not run it for normal content editing. All `tools/` HTTP access is denied.

## Verification

- `php tools/test_cms.php anonymous`: verifies anonymous access is rejected.
- `php tools/test_cms.php`: renders each editor tab, checks signed forms and validation, then makes and restores one temporary homepage edit to verify database persistence and cache invalidation. Run only on a local development site.
- `python tools/verify_cms.py`: compares public markup, text, and attributes to the pre-migration snapshots in ignored `tmp/`. These snapshots are local verification artifacts and are not included in Git.
- `node --check squelettes/assets/site.js`: JavaScript syntax check.

The contact form validates inputs then opens the visitor's mail client with a draft addressed to `info@getprovenix.com`. It does not send or store submissions on the server. Actual server delivery would require a configured mail service and a protected SPIP form handler.

Fonts use Google Fonts with local system-font fallbacks. No application credentials, customer claims, or invented testimonials are included. The homepage lineage diagram is explicitly illustrative.

CMS saves invalidate the cache. After changing theme code, use SPIP's cache maintenance controls or reload with `var_mode=recalcul` while logged in as an administrator if needed.

Git excludes SPIP runtime files, uploads, local credentials, and installed core directories. A deployment needs a matching SPIP installation and separately managed database/uploads; this repository is not a complete site backup.
