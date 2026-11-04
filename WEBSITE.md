# Provenix public website

The marketing website runs on the existing SPIP installation. The Python/React product in `E:\Documents\GitHub\engine` remains a separate application.

## Pages

- Home: `/provenix/`
- About: `/provenix/spip.php?page=about`
- Services: `/provenix/spip.php?page=services`
- Contact: `/provenix/spip.php?page=contact`

Custom templates are in `squelettes/`. Shared navigation, metadata, footer, and call-to-action are `inc-*.html`. Styles, navigation behavior, contact form behavior, and the PX favicon are in `squelettes/assets/`. Page copy is edited in these templates; it is not currently stored as SPIP articles. SPIP core and bundled templates are unchanged.

The contact form validates inputs then opens the visitor's mail client with a draft addressed to `info@getprovenix.com`. It does not send or store submissions on the server. Actual server delivery would require a configured mail service and a protected SPIP form handler.

Fonts use Google Fonts with local system-font fallbacks. No application credentials, customer claims, or invented testimonials are included. The homepage lineage diagram is explicitly illustrative.

If SPIP serves an older cached version, use its cache maintenance controls or reload with `var_mode=recalcul` while logged in as an administrator.

Git excludes SPIP runtime files, uploads, local credentials, and installed core directories. A deployment needs a matching SPIP installation and separately managed database/uploads; this repository is not a complete site backup.
