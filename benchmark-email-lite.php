<?php
/*
Plugin Name: Benchmark Email Lite
Plugin URI: https://codedcommerce.com/product/benchmark-email-newsletters/
Description: Benchmark Email Lite lets you build an email list right from your WordPress site, and easily send your subscribers email versions of your blog posts.
Version: 2.11.1
Author: Coded Commerce, LLC
Author URI: https://codedcommerce.com/
License: GPLv3
Text Domain: benchmark-email-lite
Domain Path: /languages/
*/

// Define Constants
define( 'BMEL_DIR_PATH', plugin_dir_path( __FILE__ ) . 'component/' );
define( 'BMEL_DIR_URL', plugin_dir_url( __FILE__ ) . 'component/' );
define( '_JEXEC', true );

// Include Plugin Object Files
require_once( ABSPATH . WPINC . '/class-IXR.php' );
require_once( 'component/controllers/wordpress.php' );
require_once( 'component/models/class.api.php' );
require_once( 'component/models/class.posts.php' );
require_once( 'component/models/class.reports.php' );
require_once( 'component/models/class.settings.php' );
require_once( 'component/models/class.widget.php' );

// Controller Hooks
add_action( 'admin_init', array( 'benchmarkemaillite_admin', 'admin_init' ) );
add_action( 'customize_controls_enqueue_scripts', array( 'benchmarkemaillite_admin', 'admin_init' ) );
add_action( 'wp_dashboard_setup', array( 'benchmarkemaillite_admin', 'wp_dashboard_setup' ) );

// Posts Hooks
add_action( 'save_post', array( 'benchmarkemaillite_posts', 'save_post' ) );
add_action( 'wp_ajax_bmel_metabox', array( 'benchmarkemaillite_posts', 'save_post_ajax' ) );

// Widget Hooks
add_action( 'widgets_init', array( 'benchmarkemaillite_widget', 'widgets_init' ) );
add_action( 'benchmarkemaillite_queue', array( 'benchmarkemaillite_widget', 'queue_upload' ) );
function benchmarkemaillite_register_widget() { register_widget( 'benchmarkemaillite_widget' ); }
add_action( 'widgets_init', 'benchmarkemaillite_register_widget' );

// Shortcode Hooks
add_shortcode( 'benchmark-email-lite', array( 'benchmarkemaillite_frontend', 'shortcode' ) );

// Settings Hooks
add_action( 'admin_init', array( 'benchmarkemaillite_settings', 'admin_init' ) );
add_action( 'admin_menu', array( 'benchmarkemaillite_settings', 'admin_menu' ) );
add_action( 'admin_notices', array( 'benchmarkemaillite_settings', 'admin_notices' ) );
add_action( 'init', array( 'benchmarkemaillite_settings', 'init' ) );

// Plugins Page Links
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'benchmarkemaillite_settings', 'plugin_action_links' ) );

// Internationalization
function benchmarkemaillite_i18n() {
	load_plugin_textdomain( 'benchmark-email-lite', false, BMEL_DIR_PATH . '/languages/' );        
}
add_action( 'plugins_loaded', 'benchmarkemaillite_i18n' );
