<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://rcode.pl
 * @since      1.0.0
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/public
 * @author     Rafał Rojek <r.rojek87@gmail.com>
 */
class Protected_Pdf_Public
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/styles.css', [], $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/scripts.js', ['jquery'], $this->version, false);
	}

	public function set_cookie($name, $value)
	{
		$value = base64_encode(stripslashes_deep($value));
		$expires = new \DateTime(PROTECTED_PDF_ACCESS_TIME);

		setcookie($name, $value, $expires->getTimestamp(), COOKIEPATH, COOKIE_DOMAIN, true, true);
	}

	public function get_cookie_decoded_hash($name)
	{
		if (isset($_COOKIE[$name])) {
			return base64_decode(stripslashes($_COOKIE[$name]));
		}

		return null;
	}

	public function register_protected_pdf_shortcode($atts)
	{
		$atts = shortcode_atts([
			'file' => ''
		], $atts);

		if (!$this->isPdf($atts['file'])) {
			wp_die(__('Required PDF format file', 'protected-pdf'));
		}

		if ($this->is_allowed_access()) {
			return $this->show_file_viewer($atts['file']);
		}

		return $this->show_signin_form();
	}

	public function saveMember($params)
	{
		global $wpdb;

		$hash = wp_generate_password(32, false);
		$table = $wpdb->prefix . PROTECTED_PDF_MEMBERS_TABLE;

		$wpdb->insert(
			$table,
			[
				'first_name' => $params['pdf_member_name'],
				'email' => $params['pdf_member_email'],
				'hash' => $hash,
			]
		);

		return $hash;
	}

	public function updateMember($params)
	{
		global $wpdb;

		$newHash = wp_generate_password(32, false);
		$table = $wpdb->prefix . PROTECTED_PDF_MEMBERS_TABLE;

		$whereData = [
			'first_name' => $params['pdf_member_name'],
			'email' => $params['pdf_member_email'],
		];

		$wpdb->update($table, ['hash' => $newHash], $whereData);

		return $newHash;
	}

	public function isMemberExists($first_name, $email)
	{
		global $wpdb;

		$table = $wpdb->prefix . PROTECTED_PDF_MEMBERS_TABLE;
		$first_name_to_check = $first_name;
		$email_to_check = $email;

		$sql = "SELECT count(id) as total FROM {$table} WHERE first_name=%s AND email=%s";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql, $first_name_to_check, $email_to_check)
		);

		return !(count($results) === 1 && intval($results[0]->total) === 0);
	}

	public function isMemberExistsByHash($hash)
	{
		global $wpdb;

		$hash_to_check = $hash;
		$table = $wpdb->prefix . PROTECTED_PDF_MEMBERS_TABLE;

		$sql = "SELECT count(id) as total FROM {$table} WHERE hash=%s";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql, $hash_to_check)
		);

		return !(count($results) === 1 && intval($results[0]->total) === 0);
	}

	private function isPdf($file)
	{
		$mime_type = wp_check_filetype(wp_basename($file));

		return strtolower($mime_type['type']) === 'application/pdf';
	}

	public function is_allowed_access()
	{
		if (!isset($_COOKIE[PROTECTED_PDF_COOKIE_NAME])) {
			return false;
		}

		$hash = $this->get_cookie_decoded_hash(PROTECTED_PDF_COOKIE_NAME);

		return $this->isMemberExistsByHash($hash);
	}

	private function show_file_viewer($file_url)
	{
		ob_start();

		$file_url = esc_url($file_url);

		include(plugin_dir_path(__FILE__) . 'partials/file-viewer.php');

		return ob_get_clean();
	}

	private function show_signin_form()
	{
		ob_start();

		include(plugin_dir_path(__FILE__) . 'partials/signin-form.php');

		return ob_get_clean();
	}
}
