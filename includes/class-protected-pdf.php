<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://boldway.eu
 * @since      1.0.0
 *
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Protected_Pdf
 * @subpackage Protected_Pdf/includes
 * @author     Rafał Rojek <r.rojek87@gmail.com>
 */
class Protected_Pdf
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Protected_Pdf_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('PROTECTED_PDF_VERSION')) {
			$this->version = PROTECTED_PDF_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'protected-pdf';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Protected_Pdf_i18n. Defines internationalization functionality.
	 * - Protected_Pdf_Admin. Defines all hooks for the admin area.
	 * - Protected_Pdf_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-protected-pdf-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-protected-pdf-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-protected-pdf-public.php';

		/**
		 * The class responsible for defining all custom routes REST Api
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-protected-pdf-public-route.php';

		$this->loader = new Protected_Pdf_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Protected_Pdf_Admin($this->get_plugin_name(), $this->get_version());

		$this->loader->add_action('save_post', $plugin_admin, 'manage_protected_pdf_file', 10, 2);

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{

		$plugin_public = new Protected_Pdf_Public($this->get_plugin_name(), $this->get_version());

		$plugin_router = new Protected_Pdf_Public_Route($this->get_plugin_name(), $this->get_version(), $plugin_public);

		// create custom rest endpoints
		$this->loader->add_action('rest_api_init', $plugin_router, 'sign_in');

		// add custom shortcode
		$this->loader->add_shortcode('protected-pdf', $plugin_public, 'register_protected_pdf_shortcode');

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Protected_Pdf_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}

	public static function isProtectedFile($file)
	{
		global $wpdb;

		$table = $wpdb->prefix . PROTECTED_PDF_FILE_TABLE;

		$file_to_check = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/wp-content/uploads/$file";

		$sql = "SELECT count(id) as total FROM {$table} WHERE file_url=%s";

		$results = $wpdb->get_results(
			$wpdb->prepare($sql, $file_to_check)
		);

		return !(count($results) === 1 && intval($results[0]->total) === 0);
	}
}
