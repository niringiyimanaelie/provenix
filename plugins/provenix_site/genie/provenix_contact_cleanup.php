<?php
if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}
function genie_provenix_contact_cleanup_dist($last_run)
{
    foreach (glob(_DIR_TMP . 'provenix-contact/*.json') ?: [] as $file) {
        if (filemtime($file) >= time() - 86400) {
            continue;
        }
        $handle = @fopen($file, 'r+');
        if ($handle) {
            if (flock($handle, LOCK_EX | LOCK_NB)) {
                // Truncate rather than unlink an open file on Windows.
                ftruncate($handle, 0);
                flock($handle, LOCK_UN);
                fclose($handle);
                @unlink($file);
            } else {
                fclose($handle);
            }
        }
    }
    return 1;
}
