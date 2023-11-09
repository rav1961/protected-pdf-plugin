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
		$htaccess_file = ABSPATH . '.htaccess';

		$remove_plugin_rule = "\n# BEGIN PROTECTED-PDF-PLUGIN\n" .
			"# END PROTECTED-PDF-PLUGIN\n\n";

		$htaccess_updated = insert_with_markers($htaccess_file, 'prevent_pdf', $remove_plugin_rule);

		if (!$htaccess_updated) {
			echo __('Failed to remove the rule from the .htaccess file.', 'protected-pdf');
		}
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
