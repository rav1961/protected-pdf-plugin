<?php

/**
 * Fired during plugin activation
 *
 * @link       https://rcode.pl
 * @since      1.0.0
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/includes
 * @author     Rafał Rojek <r.rojek87@gmail.com>
 */
class Protected_Pdf_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		self::create_members_table($wpdb->prefix, $wpdb->get_charset_collate());
		self::create_protected_files_table($wpdb->prefix, $wpdb->get_charset_collate());
		self::add_htaccess_rule();
	}

	private static function create_members_table($prefix = '', $charset_collate = '')
	{
		$sql = "CREATE TABLE " . $prefix . PROTECTED_PDF_MEMBERS_TABLE . " (
			id INT NOT NULL AUTO_INCREMENT,
			first_name VARCHAR(255),
			email VARCHAR(255),
			hash VARCHAR(255),
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY hash (hash)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	private static function create_protected_files_table($prefix = '', $charset_collate = '')
	{
		$sql = "CREATE TABLE " . $prefix . PROTECTED_PDF_FILE_TABLE . " (
			id INT NOT NULL AUTO_INCREMENT,
			file_url VARCHAR(255),
			post_type VARCHAR(255),
			post_id INT,
			author_id INT,
			created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id),
			KEY file_url (file_url)
		) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	private static function add_htaccess_rule()
	{
		$htaccess_file = ABSPATH . '.htaccess';

		$plugin_rule = "\n# BEGIN PROTECTED-PDF-PLUGIN\n" .
			"RewriteRule ^wp-content/uploads/(.*)\.pdf$ /wp-content/plugins/protected-pdf-plugin/includes/proxy-protected-files.php?file=$1 [QSA,L]\n" .
			"# END PROTECTED-PDF-PLUGIN\n\n";

		$htaccess_updated = insert_with_markers($htaccess_file, 'prevent_pdf', $plugin_rule);

		if (!$htaccess_updated) {
			echo __('Failed to add the rule to the .htaccess file.', 'protected-pdf');
		}
	}
}
