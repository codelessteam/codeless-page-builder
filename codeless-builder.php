<?php
/**
 * Plugin Name:       Codeless Page Builder
 * Plugin URI:        https://codeless.co/codeless-builder
 * Description:       Codeless Page Builder is a simple to use Visual (Front-end) Page Builder for WordPress based on Kirki Framework
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Author:            Codeless
 * Author URI:        https://codeless.co
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       codeless-builder
 * Domain Path:       /locate
 */
 
 // don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Current version
 */
if ( ! defined( 'CL_BUILDER_VERSION' ) ) {

	define( 'CL_BUILDER_VERSION', '1.0.0' );
}




class Cl_Builder_Manager{
    
    private $paths;
    
    private $factory = array();

	private $plugin_name;

	public $clactive = false;
	
	private $custom_user_templates_dir = false;
    
    private static $_instance;
    
    public function __construct(){
        
        $dir = dirname( __FILE__ );

        
		
		/**
		 * Define path settings for visual composer.
		 *
		 * APP_ROOT        - plugin directory.
		 * WP_ROOT         - WP application root directory.
		 * APP_DIR         - plugin directory name.
		 * CONFIG_DIR      - configuration directory.
		 * ASSETS_DIR      - asset directory full path.
		 * ASSETS_DIR_NAME - directory name for assets. Used from urls creating.
		 * CORE_DIR        - classes directory for core vc files.
		 * HELPERS_DIR     - directory with helpers functions files.
		 * SHORTCODES_DIR  - shortcodes classes.
		 * SETTINGS_DIR    - main dashboard settings classes.
		 * TEMPLATES_DIR   - directory where all html templates are hold.
		 * EDITORS_DIR     - editors for the post contents
		 * PARAMS_DIR      - complex params for shortcodes editor form.
		 * UPDATERS_DIR    - automatic notifications and updating classes.
		 */
		$this->setPaths( array(
			'APP_ROOT' => $dir,
			'WP_ROOT' => preg_replace( '/$\//', '', ABSPATH ),
			'APP_DIR' => basename( $dir ),
			'THEME_CODELESS_DIR' => get_template_directory().'/includes/codeless_builder',
			'THEME_CODELESS_CONFIG' => get_template_directory().'/includes/codeless_builder/config',
			'THEME_CODELESS_HEADER' => get_template_directory().'/includes/codeless_builder/header-elements',
			'THEME_CODELESS_SHORTCODES' => get_template_directory().'/includes/codeless_builder/shortcodes',
			'CONFIG_DIR' => $dir . '/config',
			'ASSETS_DIR' => $dir . '/assets',
			'ASSETS_DIR_NAME' => 'assets',
			'CORE_DIR' => $dir . '/include/core',
			'HELPERS_DIR' => $dir . '/include/helpers',
			'TEMPLATES_DIR' => $dir . '/include/templates',
			'SHORTCODES_DIR' => $dir . '/include/core/shortcodes',
			'KIRKI_SETUP' => $dir . '/include/core/kirki-setup'

		) );
		// Load API
		require_once $this->path( 'HELPERS_DIR', 'helpers.php' );
		require_once $this->path( 'CORE_DIR', 'cl-builder-mapper.php' );
		require_once $this->path( 'CORE_DIR', 'cl-register-post-type.php' );
		require_once $this->path( 'SHORTCODES_DIR', 'cl-shortcode.php' );
		require_once $this->path( 'SHORTCODES_DIR', 'cl-shortcode-manager.php' );
		require_once $this->path( 'SHORTCODES_DIR', 'cl-shortcode-container.php' );
		require_once $this->path( 'SHORTCODES_DIR', 'cl-shortcode-simple.php' );
		
		// Add hooks
		
		add_action( 'plugins_loaded', array( &$this, 'pluginsLoaded' ), 9 );
		
		add_action( 'init', array( &$this, 'init' ), 999 );

		remove_action('widgets_init', 'NEXForms_widget::register_this_widget');
		
		
		// Remove Widgets and Nav Menus on simple MODE
		//add_filter( 'customize_loaded_components', array( &$this,'remove_widgets_panel' ), 999, 1 );
		//add_filter( 'customize_loaded_components', array( &$this, 'remove_nav_menus_panel' ), 999, 1 );
		$this->setPluginName( $this->path( 'APP_DIR', 'codeless-builder.php' ) );
		//register_activation_hook( __FILE__, array( $this, 'activationHook' ) );
    }
    
    
    public static function getInstance() {
		if ( ! ( self::$_instance instanceof self ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}
	
    
    public function init() {


		if( isset($_REQUEST['clactive']) )
			$this->clactive = true;
		/**
		 * Set version if required.
		 */
		$this->setVersion();
		add_action( 'admin_notices', array( &$this, 'requirement_notice' ) );
		add_filter( 'query_vars', array( &$this, 'query_vars' ), 0 );
		add_action('parse_request', array(&$this, 'sniff_requests'), 0);


		if( ! class_exists( 'Kirki' ) )
    		return;

		/**
		 * Set necessary Kirki files
		 */
		$this->kirki_setup();


		/**
		 * Init default functions of plugin
		 */
		$this->cl_base()->init();
		
		
		
		
		$this->header_builder()->init();
		
		
		/**
		* Init Page Builder functionality
		*/	
		$this->page_builder()->init();

		
	}

	public function query_vars($vars){
		$vars[] = 'cl_ajax_handler';
		return $vars;
	}

	public function sniff_requests($wp_query){
		global $wp;

		if( isset($wp->query_vars[ 'cl_ajax_handler' ]) ) {
			if( isset($_REQUEST['action']) && !empty( $_REQUEST['action'] ) ){
			
				check_ajax_referer( 'cl_ajax_handler', 'nonce' );

				$action = esc_attr(trim($_REQUEST['action']));
				// Declare all actions that you will use this ajax handler for, as an added security measure.
				$allowed_actions = array(
					'cl_load_shortcode'
				);
	
	
				if(in_array($action, $allowed_actions)) {
					if(is_user_logged_in())
						do_action('cl_ajax_handler_'.$action);
				} else {
					die('-1');
				}
			}
		}
	}

	
	public function kirki_setup() {
		if ( ! isset( $this->factory['kirki_setup'] ) ) {
		
			require_once $this->path( 'CORE_DIR', 'cl-kirki-setup.php' );
			$kirki_setup = new Cl_Kirki_Setup();
			
			$this->factory['kirki_setup'] = $kirki_setup;
		}

		return $this->factory['kirki_setup'];
	}

	
    
    protected function setPaths( $paths ) {
		$this->paths = $paths;
	}
	
	
	public function path( $name, $file = '' ) {
		$path = $this->paths[ $name ] . ( strlen( $file ) > 0 ? '/' . preg_replace( '/^\//', '', $file ) : '' );
		return $path;
	}
	
	
	public function pluginsLoaded() {


		// Setup locale
		load_plugin_textdomain( 'codeless-builder', false, $this->path( 'APP_DIR', 'locale' ) );
		
		/**
		 * Init Post Meta functionality
		 */
		
		add_action( 'admin_bar_menu', array(
			$this,
			'add_edit_by',
		), 99 );
		
	}

	public function requirement_notice(){
		if ( ! class_exists('Kirki') ) {
					
			$class = 'notice notice-error';
			
			$text    = esc_html__( 'Kirki Framework', 'codeless-builder' );
			$link    = esc_url( add_query_arg( array(
												   'tab'       => 'plugin-information',
												   'plugin'    => 'kirki',
												   'TB_iframe' => 'true',
												   'width'     => '640',
												   'height'    => '500',
											   ), admin_url( 'plugin-install.php' ) ) );
			$message = wp_kses( __( "<strong>Codeless Page Builder</strong> plugin requires ", 'codeless-builder' ), array( 'strong' => array() ) );
			
			printf( '<div class="%1$s"><p>%2$s <a class="thickbox open-plugin-details-modal" href="%3$s"><strong>%4$s</strong></a> to work. Please Install!</p></div>', $class, $message, $link, $text );
		}
	}
	
	
	protected function setVersion() {
		$version = get_option( 'cl_builder_version' );
		if ( ! is_string( $version ) || version_compare( $version, CL_BUILDER_VERSION ) !== 0 ) {
			update_option( 'cl_builder_version', CL_BUILDER_VERSION );
		}
	}
	
	public function setPluginName( $name ) {
		$this->plugin_name = $name;
	}
	
	
	public function cl_base() {
		if ( ! isset( $this->factory['cl_base'] ) ) {
		
			require_once $this->path( 'CORE_DIR', 'cl-builder-base.php' );
			$cl_base = new Cl_Builder_Base();
			
			$this->factory['cl_base'] = $cl_base;
		}

		return $this->factory['cl_base'];
	}
	
	
	public function header_builder() {
		if ( ! isset( $this->factory['cl_header_builder'] ) ) {
		
			require_once $this->path( 'CORE_DIR', 'cl-header-builder.php' );
			$cl_header_builder = new Cl_Header_Builder();
			
			$this->factory['cl_header_builder'] = $cl_header_builder;
		}

		return $this->factory['cl_header_builder'];
	}
	
	
	
	public function page_builder() {
		if ( ! isset( $this->factory['cl_page_builder'] ) ) {
		
			require_once $this->path( 'CORE_DIR', 'cl-page-builder.php' );
			$cl_page_builder = new Cl_Page_Builder();
			
			$this->factory['cl_page_builder'] = $cl_page_builder;
		}

		return $this->factory['cl_page_builder'];
	}
	
	public function pluginUrl( $file ) {
		return preg_replace( '/\s/', '%20', plugins_url( $file , __FILE__ ) );
	}
	
	public function assetUrl( $file ) {
		return preg_replace( '/\s/', '%20', plugins_url( $this->path( 'ASSETS_DIR_NAME', $file ), __FILE__ ) );
	}
	
	public function getShortcodesTemplateDir( $template ) {
		return false !== $this->custom_user_templates_dir ? $this->custom_user_templates_dir . '/' . $template : locate_template( 'includes/codeless_builder/shortcodes' . '/' . $template );
	}
	
	public function getDefaultShortcodesTemplatesDir() {
		return cl_path_dir( 'TEMPLATES_DIR', 'shortcodes' );
	}

	public function getDefaultHeaderTemplatesDir() {
		return cl_path_dir( 'TEMPLATES_DIR', 'header-elements' );
	}

	public function remove_widgets_panel($components){

	}

	public function remove_nav_menus_panel($components){
		$components = array();
	    return $components;
	}

	public function is_page_builder_active(){
		if( isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'product' )
			return true;
		return $this->clactive;
	}

	public function add_edit_by($wp_admin_bar){
		
		if ( ! is_object( $wp_admin_bar ) ) {
			global $wp_admin_bar;
		}
		$post_id = get_queried_object_id();
		if ( is_singular() ) {
			if ( get_post_type() == 'page' || get_post_type() == 'portfolio' ) {
	
				$customize_href = add_query_arg( 'clactive', 1, get_permalink() );
		
				$customize_url = add_query_arg( 'clactive', 1, wp_customize_url());
		
				$customize_url = add_query_arg( 'url', urlencode($customize_href), $customize_url  );
				
		
				$wp_admin_bar->add_menu( array(
					'id' => 'vc_inline-admin-bar-link',
					'title' => esc_html__( 'Edit with Codeless Page Builder', 'codeless-builder' ),
					'href' => $customize_url,
					'meta' => array( 'class' => 'vc_inline-link' ),
				) );
			}
		}
	}
	
    
}


global $cl_builder;
if ( ! $cl_builder ) {
	$cl_builder = Cl_Builder_Manager::getInstance();
}


 
 
 ?>