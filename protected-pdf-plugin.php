<?php

/**
 * @link              https://rcode.pl
 * @since             1.0.0
 * @package           Protected_Pdf_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Protected PDF File
 * Plugin URI:        https://pdf.rcode.pl
 * Description:       Plugin for a secured PDF file in exchange for contact details collected by the form. The selected file remains restricted to protect against unauthorized access.
 * Version:           1.0.0
 * Author:            Rafał Rojek
 * Author URI:        https://rcode.pl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       protected-pdf
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    die;
}

function activate_protected_pdf_plugin()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-protected-pdf-activator.php';
    Protected_PDF_Plugin_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_protected_pdf_plugin');
