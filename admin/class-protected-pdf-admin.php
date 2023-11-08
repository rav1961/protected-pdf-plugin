<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://rcode.pl
 * @since      1.0.0
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/admin
 * @author     Rafał Rojek <r.rojek87@gmail.com>
 */
class Protected_Pdf_Admin
{
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Protected_Pdf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Protected_Pdf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/protected-pdf-admin.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Protected_Pdf_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Protected_Pdf_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/protected-pdf-admin.js', ['jquery'], $this->version, false);
	}

	public function manage_protected_pdf_file($post_id, $post)
	{
		try {
			$post_content = get_post_field('post_content', $post_id);
			$post_type = $post->post_type;
			$current_file_in_table = $this->get_file_from_table($post_id, $post_type);
			$shortcode_file = $this->get_shortcode_file($post_content, 'protected-pdf');

			if (has_shortcode($post_content, 'protected-pdf') && $current_file_in_table === null && $post_type !== 'revision') {
				$this->save_protected_file($post_id, $post_type, $shortcode_file);
			}

			if (has_shortcode($post_content, 'protected-pdf') && $shortcode_file !== null && $post_type !== 'revision') {
				if ($current_file_in_table !== $shortcode_file) {
					$this->update_protected_file($post_id, $post_type, $shortcode_file);
				}
			}

			if (!has_shortcode($post_content, 'protected-pdf') && $current_file_in_table !== null && $post_type !== 'revision') {
				$this->remove_protected_file($post_id, $post_type);
			}
		} catch (\Exception $e) {
			wp_die($e->getMessage());
		}
	}

	private function get_file_from_table($post_id, $post_type)
	{
		global $wpdb;

		$id_to_check = $post_id;
		$type_to_check = $post_type;

		$table = $wpdb->prefix . PROTECTED_PDF_FILE_TABLE;

		$sql = "SELECT file_url FROM {$table} WHERE post_type=%s AND post_id=%d";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql, $type_to_check, $id_to_check)
		);

		if (is_array($results) && isset($results[0]->file_url)) {
			return $results[0]->file_url;
		}

		return null;
	}

	private function get_shortcode_file($content, $shortcode_tag)
	{
		$regex_pattern = get_shortcode_regex();
		preg_match('/' . $regex_pattern . '/s', $content, $regex_matches);

		if (is_array($regex_matches) && isset($regex_matches[3])) {
			$atts_string = $regex_matches[3];
			$atts = [];
			if (preg_match_all('/(\w+)\s*=\s*"([^"]*)"/', $atts_string, $matches, PREG_SET_ORDER)) {
				foreach ($matches as $match) {
					return $match[2];
				}
			}
		}

		return null;
	}

	private function save_protected_file($post_id, $post_type, $file)
	{
		global $wpdb;

		$table = $wpdb->prefix . PROTECTED_PDF_FILE_TABLE;

		$wpdb->insert($table, [
			'file_url' => $file,
			'post_type' => $post_type,
			'post_id' => $post_id,
			'author_id' => get_current_user_id()
		]);
	}

	private function remove_protected_file($post_id, $post_type)
	{
		global $wpdb;

		$table = $wpdb->prefix . PROTECTED_PDF_FILE_TABLE;

		$whereData = [
			'post_id' => $post_id,
			'post_type' => $post_type,
		];

		$wpdb->delete($table, $whereData);
	}

	private function update_protected_file($post_id, $post_type, $new_file)
	{
		global $wpdb;

		$table = $wpdb->prefix . PROTECTED_PDF_FILE_TABLE;

		$whereData = [
			'post_id' => $post_id,
			'post_type' => $post_type,
			'author_id' => get_current_user_id(),
		];

		$wpdb->update($table, ['file_url' => $new_file], $whereData);
	}
}
