<?php
/*
Plugin Name: Benchmark Email Lite
Plugin URI: http://www.beautomated.com/benchmark-email-lite/
Description: Benchmark Email Lite lets you build an email list right from your WordPress site, and easily send your subscribers email versions of your blog posts.
Version: 2.6
Author: beAutomated
Author URI: http://www.beautomated.com/
License: GPLv2
Text Domain: benchmark-email-lite
Domain Path: /languages/

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version, see <http://www.gnu.org/licenses/>.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

// Include Plugin Object Files
require_once( 'lib/class.api.php' );
require_once( 'lib/class.display.php' );
require_once( 'lib/class.posts.php' );
require_once( 'lib/class.reports.php' );
require_once( 'lib/class.settings.php' );
require_once( 'lib/class.widget.php' );

// Display API Hooks
add_action( 'wp_init', array( 'benchmarkemaillite_display', 'wp_init' ) );

// Posts API Hooks
add_action( 'admin_init', array( 'benchmarkemaillite_posts', 'admin_init' ) );
add_action( 'save_post', array( 'benchmarkemaillite_posts', 'save_post' ) );

// Widget API Hooks
add_action( 'admin_init', array( 'benchmarkemaillite_widget', 'admin_init' ) );
add_action( 'widgets_init', array( 'benchmarkemaillite_widget', 'widgets_init' ) );
add_action( 'benchmarkemaillite_queue', array( 'benchmarkemaillite_widget', 'queue_upload' ) );
function benchmarkemaillite_register_widget() {
	register_widget( 'benchmarkemaillite_widget' );
}
add_action( 'widgets_init', 'benchmarkemaillite_register_widget' );

// Shortcode API Hooks
add_shortcode( 'benchmark-email-lite', array( 'benchmarkemaillite_display', 'shortcode' ) );

// Settings API Hooks
add_action( 'admin_init', array( 'benchmarkemaillite_settings', 'admin_init' ) );
add_action( 'admin_menu', array( 'benchmarkemaillite_settings', 'admin_menu' ) );
add_action( 'admin_notices', array( 'benchmarkemaillite_settings', 'admin_notices' ) );
add_action( 'init', array( 'benchmarkemaillite_settings', 'init' ) );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( 'benchmarkemaillite_settings', 'plugin_action_links' ) );

// Internationalization
function benchmarkemaillite_i18n() {
	load_plugin_textdomain( 'benchmark-email-lite', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );        
}
add_action( 'plugins_loaded', 'benchmarkemaillite_i18n' );

?>
