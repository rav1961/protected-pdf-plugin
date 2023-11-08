<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://rcode.pl
 * @since             1.0.0
 * @package           Protected_Pdf
 *
 * @wordpress-plugin
 * Plugin Name:       Protected PDF File
 * Plugin URI:        https://rcode.pl
 * Description:       Plugin for a secured PDF file in exchange for contact details collected by the form. The selected file remains restricted to protect against unauthorized access.
 * Version:           1.0.0
 * Author:            Rafał Rojek
 * Author URI:        https://rcode.pl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       protected-pdf
 * Domain Path:       /languages
 */

if (!defined('WPINC')) {
    die;
}

define('PROTECTED_PDF_VERSION', '1.0.0');
define('PROTECTED_PDF_FILE_TABLE', 'protected_pdf_files');
define('PROTECTED_PDF_MEMBERS_TABLE', 'protected_pdf_members');
define('PROTECTED_PDF_ACCESS_TIME', '+6 months');
define('PROTECTED_PDF_COOKIE_NAME', 'pp');

function activate_protected_pdf()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-protected-pdf-activator.php';
    Protected_PDF_Activator::activate();
}
register_activation_hook(__FILE__, 'activate_protected_pdf');

function deactivate_protected_pdf()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-protected-pdf-deactivator.php';
    Protected_Pdf_Deactivator::deactivate();
}
register_deactivation_hook(__FILE__, 'deactivate_protected_pdf');

require plugin_dir_path(__FILE__) . 'includes/class-protected-pdf.php';
require plugin_dir_path(__FILE__) . 'includes/proxy-protected-files.php';

function run_protected_pdf()
{
    $plugin = new Protected_Pdf();
    $plugin->run();
}
run_protected_pdf();
