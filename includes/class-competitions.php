<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.upwork.com/freelancers/~013cb8700c27145b4e
 * @since      1.0.0
 *
 * @package    Competitions
 * @subpackage Competitions/includes
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
 * @package    Competitions
 * @subpackage Competitions/includes
 * @author     Junayed <admin@easeare.com>
 */
class Competitions {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Competitions_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	public function __construct() {
		if ( defined( 'COMPETITIONS_VERSION' ) ) {
			$this->version = COMPETITIONS_VERSION;
		} else {
			$this->version = '1.0.3';
		}
		$this->plugin_name = 'competitions';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Competitions_Loader. Orchestrates the hooks of the plugin.
	 * - Competitions_i18n. Defines internationalization functionality.
	 * - Competitions_Admin. Defines all hooks for the admin area.
	 * - Competitions_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-competitions-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-competitions-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-competitions-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-competitions-public.php';

		$this->loader = new Competitions_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Competitions_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Competitions_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Competitions_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $plugin_admin, 'competitions_post_types' );
		
		$this->loader->add_action( 'init', $plugin_admin, 'competition_roles' );

		$this->loader->add_action( 'manage_competitions_posts_columns', $plugin_admin, 'manage_competitions_columns', 10, 4 );
		$this->loader->add_action( 'manage_competitions_posts_custom_column', $plugin_admin, 'manage_competitions_columns_views', 10, 2 ); 

		$this->loader->add_action( 'init', $plugin_admin, 'project_taxonomies', 0 );
		
		$this->loader->add_filter( 'get_avatar',  $plugin_admin, 'comp_get_avatar', 10, 5 );

		$this->loader->add_filter( 'save_post_competitions',  $plugin_admin, 'save_competition_data' );
		$this->loader->add_filter( 'save_post_project',  $plugin_admin, 'save_project_data' );
		$this->loader->add_filter( 'admin_menu',  $plugin_admin, 'competition_admin_menupage' );

		$this->loader->add_action( 'wp_ajax_update_user_remark', $plugin_admin, 'update_user_remark' );
		$this->loader->add_action( 'wp_ajax_nopriv_update_user_remark', $plugin_admin, 'update_user_remark' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Competitions_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_filter( 'wp_head',  $plugin_public, 'competition_wp_head' );
		
		$this->loader->add_filter( 'display_post_states', $plugin_public, 'competition_profile_page_states', 10, 2 ); // Add post state
		// Page attribute
		$this->loader->add_filter('theme_page_templates', $plugin_public, 'competition_pages' );
		// Pages includes
		$this->loader->add_filter('template_include', $plugin_public, 'competitions_templates');
		
		
		$this->loader->add_action( 'wp_ajax_get_competition_results', $plugin_public, 'get_competition_results' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_competition_results', $plugin_public, 'get_competition_results' );

		$this->loader->add_action( 'wp_ajax_get_projects_results', $plugin_public, 'get_projects_results' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_projects_results', $plugin_public, 'get_projects_results' );

		$this->loader->add_action( 'wp_ajax_get_profile_informations', $plugin_public, 'get_profile_informations' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_profile_informations', $plugin_public, 'get_profile_informations' );

		$this->loader->add_action( 'wp_ajax_save_profile_information', $plugin_public, 'save_profile_information' );
		$this->loader->add_action( 'wp_ajax_nopriv_save_profile_information', $plugin_public, 'save_profile_information' );

		$this->loader->add_action( 'wp_ajax_delete_project', $plugin_public, 'delete_project' );
		$this->loader->add_action( 'wp_ajax_nopriv_delete_project', $plugin_public, 'delete_project' );

		// Submit project
		$this->loader->add_action( 'init', $plugin_public, 'submit_project' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Competitions_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
