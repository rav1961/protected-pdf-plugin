<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://rcode.pl
 * @since      1.0.0
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/includes
 * @author     Rafał Rojek <r.rojek87@gmail.com>
 */
class Protected_Pdf_Deactivator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		self::clear_db_tables();
		self::remove_htaccess_rule();
	}

	private static function remove_htaccess_rule()
	{
		$htaccess_file = '.htaccess';
		$current_htaccess = file_get_contents($htaccess_file);

		$old_plugin_rule = "RewriteRule ^wp-content/uploads/(.*)\.pdf$ /wp-content/plugins/protected-pdf-plugin/includes/proxy-protected-files.php?file=$1 [QSA,L]\n";
		$current_htaccess = str_replace($old_plugin_rule, '', $current_htaccess);

		file_put_contents($htaccess_file, $current_htaccess);
	}

	private static function clear_db_tables()
	{
		global $wpdb;

		$tables = [
			$wpdb->prefix . PROTECTED_PDF_FILE_TABLE,
			$wpdb->prefix . PROTECTED_PDF_MEMBERS_TABLE,
		];

		foreach ($tables as $table) {
			$wpdb->query("DROP TABLE IF EXISTS $table");
		}
	}
}
