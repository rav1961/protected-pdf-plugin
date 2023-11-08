<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://rcode.pl
 * @since      1.0.0
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="pdf-plugin-wrapper">
    <object data="<?php echo $file_url; ?>#toolbar=0" type="application/pdf" width="100%" height="500px" class="pdf-file">
        <p class="pdf-file__alert"><?php _e('Unable to display PDF file.', 'protected-pdf'); ?></p>
    </object>
</div>