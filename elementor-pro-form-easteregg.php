<?php
/**
 * Plugin Name: Elementor Form Send Easteregg 
 * Description: Custom elementor extension for send form as easteregg.
 * Plugin URI:  https://tristandeboer.nl
 * Version:     1.0.1
 * Author:      Sesam
 * Author URI:  https://tristandeboer.nl
 * Text Domain: elementor-form-send-easteregg
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'ELEMENTOR_FORM_EASTEREGG_PATH', plugin_dir_path(__FILE__) );
define( 'ELEMENTOR_FORM_EASTEREGG_UPLOAD_PATH', trailingslashit( WP_CONTENT_DIR ) . '/uploads/elementor-form-easteregg' );

/**
 * Main Elementor Form Send Easteregg Class
 *
 * The main class that initiates and runs the plugin.
 *
 * @since 1.0.0
 */
final class Elementor_Form_Send_EASTEREGG {

	/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.1';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @static
	 *
	 * @var Elementor_Form_Send_EASTEREGG The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_Form_Send_EASTEREGG An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'elementor-form-send-easteregg' );

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}
		
		// Check for ELEMENTOR_FORM_EASTEREGG_PATH in uploads directory
		if ( ! file_exists( ELEMENTOR_FORM_EASTEREGG_UPLOAD_PATH ) ) {
			wp_mkdir_p( ELEMENTOR_FORM_EASTEREGG_UPLOAD_PATH );
		}
		

		// Add Plugin actions
		add_action( 'elementor_pro/init', [ $this, 'init_elemoentor_pro_form_action' ] );
		//add_action( "elementor/element/form/section_email/before_section_end", [$this,'add_easteregg_option_in_elementor_backend'],10,2);
	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-form-send-easteregg' ),
			'<strong>' . esc_html__( 'Elementor Form Send EASTEREGG', 'elementor-form-send-easteregg' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-form-send-easteregg' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-form-send-easteregg' ),
			'<strong>' . esc_html__( 'Elementor Form Send EASTEREGG', 'elementor-form-send-easteregg' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'elementor-form-send-easteregg' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'elementor-form-send-easteregg' ),
			'<strong>' . esc_html__( 'Elementor Form Send EASTEREGG', 'elementor-form-send-easteregg' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'elementor-form-send-easteregg' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}
	
	/**
	 * load email easteregg action only on elementor pro init
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init_elemoentor_pro_form_action(){
		
		// Require email action for elementor
		require_once(ELEMENTOR_FORM_EASTEREGG_PATH.'action/email-as-easteregg.php');
		
		// Instantiate the action class
		$emailaseasteregg = new EmailAsEASTEREGG();

		// Register the action with form widget
		\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $emailaseasteregg->get_name(), $emailaseasteregg );
	
	}

}

Elementor_Form_Send_EASTEREGG::instance();
