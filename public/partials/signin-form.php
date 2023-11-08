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
    <form id="protected-pdf-signin" class="pdf-form" action="<?php echo get_rest_url(null, 'v2/' . $this->plugin_name . '/sign-in'); ?>">
        <?php wp_nonce_field('wp_rest'); ?>
        <div class="pdf-form__row">
            <label for="pdf-member-name" class="pdf-form__label"><?php _e('First name', 'protected-pdf'); ?></label>
            <input type="text" id="pdf-member-name" required name="pdf_member_name" placeholder="<?php _e('your name', 'protected-pdf'); ?>" class="pdf-form__input pdf-form__input--text" value="Rafał" />
        </div>
        <div class="pdf-form__row">
            <label for="pdf-member-email" class="pdf-form__label"><?php _e('E-mail', 'protected-pdf'); ?></label>
            <input type="email" id="pdf-member-email" required name="pdf_member_email" placeholder="<?php _e('e-mail@example.com', 'protected-pdf'); ?>" class="pdf-form__input pdf-form__input--text" value="r.rojek87@gmail.com" />
        </div>
        <div class="pdf-form__row">
            <input type="submit" value="<?php _e('Sign in', 'protected-pdf'); ?>" class="pdf-form__input pdf-form__input--submit" />
        </div>
    </form>
    <div id="protected-pdf-result" class="pdf-form__msg"></div>
    <div id="protected-pdf-viewer"></div>
</div>