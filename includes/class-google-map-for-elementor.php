<?php
namespace ElementorGoogleMapExtended;

use \Elementor\Settings;
use \Elementor\Plugin;
/**
 * Class EB_Map_Plugin
 *
 * @since 1.2
 */
class EB_Map_Plugin {
	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.2.0
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '1.8';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.2
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '5.6';
	/**
	 * Instance
	 *
	 * @since 1.2
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;
	/**
	 * Get Plugin Option
	 *
	 * @since 1.2
	 *
	 */
	private $google_map_api;
	private $google_map_languages;
	private $dequeue_scripts;
	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2
	 * @access public
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin
	 *
	 * @since 1.2
	 * @access public
	 */
	public function init() {
		add_action( 'plugins_loaded', [ $this, 'init_hooks' ] );
	}
	/**
	 * Get the value of a settings field
	 *
	 * @param string $option settings field name
	 * @param string $section the section name this field belongs to
	 * @param string $default default text if it's not found
	 *
	 * @return mixed
	 */
	public function get_option( $option, $section, $default = '' ) {

		$options = get_option( $section );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}

		return $default;
	}
	/**
	 * Load the required dependencies for this plugin.
	 * @since    1.2
	 * @access   private
	 */
	public function load_dependencies() {
		require_once GOOGLE_MAP_EXTENDED__DIR__ . '/includes/class-google-map-for-elementor-settings-api.php';
		require_once GOOGLE_MAP_EXTENDED__DIR__ . '/includes/class-google-map-for-elementor-page-settings.php';
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.2
	 * @access   private
	 */
	public function init_hooks() {
		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		if ( defined( 'GOOGLE_map_pro_version' ) ) {
			return;
		}

		$this->google_map_api = $this->get_option( 'GOOGLE_map_api_key','eb_map_general_settings' );
		$this->google_map_languages = $this->get_option( 'GOOGLE_map_lang','eb_map_general_settings' );
		$this->dequeue_scripts = $this->get_option( 'eb_dequeue_scripts','eb_map_general_settings' );

		$this->load_dependencies();

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_filter( 'plugin_action_links', [ $this, 'add_action_plugin' ], 10, 5 );
		add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ], 11 );

		if ( $this->google_map_api === '' ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_google_map_api' ] );
		}
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_scripts' ) );
		add_action( 'elementor/frontend/after_register_scripts', [ $this, 'widget_scripts' ] );
		add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );
	}
	/**
	 * The public-facing functionality of the plugin.
	 *
	 * @since    1.2
	 */
	public function enqueue_scripts() {
		$dequeue_scripts = preg_split('/[\ \n\,]+/', $this->dequeue_scripts);
		
		if ( $dequeue_scripts !== '' ) {
			foreach ($dequeue_scripts as $handle ) {
				wp_dequeue_script( $handle );
			}           
		} 

	}
	/**
	 * admin_widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function editor_scripts() {
		wp_enqueue_style( 'eb-google-map-admin', plugins_url( '/assets/css/eb-google-map-admin.css', GOOGLE_MAP_EXTENDED__FILE__ ) );
		wp_enqueue_script( 'eb-google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $this->google_map_api . '&language=' . $this->google_map_languages, ['elementor-editor'], GOOGLE_MAP_EXTENDED_VERSION, true  );
		wp_enqueue_script( 'eb-google-map-admin', plugins_url( '/assets/js/eb-google-map-admin.js', GOOGLE_MAP_EXTENDED__FILE__ ), ['eb-google-maps-api'], GOOGLE_MAP_EXTENDED_VERSION, true );
	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2
	 * @access public
	 */
	public function widget_scripts() {
		wp_register_script( 'eb-google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=' . $this->google_map_api . '&language=' . $this->google_map_languages, array(), GOOGLE_MAP_EXTENDED_VERSION, true  );
		wp_localize_script( 'eb-google-maps-api', 'EB_WP_URL', array( 'plugin_url' => plugin_dir_url( __DIR__ ) ) );
		wp_register_script( 'eb-google-map', plugins_url( '/assets/js/eb-google-map.js', GOOGLE_MAP_EXTENDED__FILE__ ), [ 'eb-google-maps-api' ], GOOGLE_MAP_EXTENDED_VERSION, true );
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.2
	 * @access private
	 */
	private function include_widgets_files() {
		require_once( GOOGLE_MAP_EXTENDED__DIR__ . '/widgets/eb-google-map-extended-widget.php' );
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();
		// Register Widgets
		Plugin::instance()->elements_manager->add_category( 
			'eb-elementor-extended',
			[
				'title'  => 'Multiple Map Marker',
				'icon' => 'font'
			],
			1
		);

		Plugin::instance()->widgets_manager->register_widget_type( new Widgets\GOOGLE_Map_Extended() );
	}

	/**
	* Get activation or deactivation link of a plugin
	*
	* @author Nazmul Ahsan <mail@nazmulahsan.me>
	* @param string $plugin plugin file name
	* @param string $action action to perform. activate or deactivate
	* @return string $url action url
	*/	
	public function plugin_action_link( $plugin, $action = 'activate' ) {
		if ( strpos( $plugin, '/' ) ) {
			$plugin = str_replace( '\/', '%2F', $plugin );
		}

		$url = sprintf( admin_url( 'plugins.php?action=' . $action . '&plugin=%s&plugin_status=all&paged=1&s' ), $plugin );
		$_REQUEST['plugin'] = $plugin;
		$url = wp_nonce_url( $url, $action . '-plugin_' . $plugin );

		return $url;
	}

	public function is_plugin_installed( $slug ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all_plugins = get_plugins();

		if ( !empty( $all_plugins[$slug] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
     * Creates the plugin action links.
     *
     * @since 1.2
     */
	public function add_action_plugin( $actions, $plugin_file ) {
		static $plugin;

		if (!isset($plugin)) 
			$plugin = 'google-map-for-elementor/elementor-google-map-extended.php';
		if ($plugin == $plugin_file) {
			$settings = array( 'settings' => '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=GOOGLE_map_setting') ) .'">' . __('Settings', 'google-map-for-elementor') . '</a>' );
			$go_pro = array( 'go_pro' => '<a href="'. esc_url( 'https://internetcss.com/elementor-google-map-extended-pro-demo/' ) .'" target="_blank">' . __('Go Pro', 'google-map-for-elementor') . '</a>');

			$actions = array_merge($go_pro, $actions);
			$actions = array_merge($settings, $actions);
		}
		return $actions;
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.2
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		
		$elementor_plugin_slug = 'elementor';
		
		$eb_button_elementor_url = '';
		$elementor_url = '';

		if ( $this->is_plugin_installed( $elementor_plugin_slug . '/elementor.php' ) ) {
			$eb_button_elementor_url = esc_html__( 'Activate Elementor', 'google-map-for-elementor' );
			$elementor_url = $this->plugin_action_link( $elementor_plugin_slug . '/elementor.php', 'activate' );
		} else {
			$eb_button_elementor_url = esc_html__( 'Install Elementor', 'google-map-for-elementor' );
			$elementor_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $elementor_plugin_slug ), 'install-plugin_' . $elementor_plugin_slug );
		}
				
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated. %3$s', 'google-map-for-elementor' ),
			'<strong>' . esc_html__( 'Multiple Map Marker For Elementor Page Builder', 'google-map-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'google-map-for-elementor' ) . '</strong>',
			'<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>',
				$elementor_url,
				$eb_button_elementor_url
			) . '</p>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.2
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'google-map-for-elementor' ),
			'<strong>' . esc_html__( 'Multiple Map Marker For Elementor Page Builder', 'google-map-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'google-map-for-elementor' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}
	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.2
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'google-map-for-elementor' ),
			'<strong>' . esc_html__( 'Multiple Map Marker For Elementor Page Builder', 'google-map-for-elementor' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'google-map-for-elementor' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.2
	 * @access public
	 */
	public function admin_notice_google_map_api() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		$message = sprintf(
			__( 'Please enter your Google API Key <a href="%2$s">here</a>.<br>"%1$s" requires Google Map API Key to work. If not you can <a href="%3$s" target="_blank">click here</a> to generate one.', 'google-map-for-elementor' ),
			'<strong>' . esc_html__( 'Multiple Map Marker For Elementor Page Builder', 'google-map-for-elementor' ) . '</strong>',
			admin_url( 'admin.php?page=GOOGLE_map_setting' ),
			esc_url( 'https://developers.google.com/maps/documentation/javascript/get-api-key' )
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();
		$eb_setting_page = ( $current_screen->id == 'elementor_page_GOOGLE_map_setting' );

		if ($eb_setting_page) {
        	$footer_text = __( 'Enjoy Multiple Map Marker For Elementor Page Builder? Please leave us a <a href="https://wordpress.org/support/plugin/google-map-for-elementor/reviews/?filter=5#new-post" target="_blank">★★★★★</a> rating. Thank you!', 'google-map-for-elementor' );
		}
		return $footer_text;
    }
}

// Instantiate EB_Map_Plugin Class
EB_Map_Plugin::instance();
