<?php

/**
 * This file is the main controller used by WordPress.
 *
 * @package	benchmarkemaillite
 * @license	GNU General Public License version 3; see LICENSE.txt
 *
 */

// No direct access to this file
defined( '_JEXEC' ) or die( 'Restricted access' );

class benchmarkemaillite_admin {

	// WP Admin Area
	static function admin_init() {

		// WP Widgets Admin JavaScript
		global $pagenow;
		if( in_array( $pagenow, array( 'customize.php', 'widgets.php' ) ) ) {
			$js_file = BMEL_DIR_URL . 'assets/js/widget.js';
			wp_enqueue_script( 'bmel_widgetadmin', $js_file, array( 'jquery' ), false, false );
		}

		// WP Posts Admin JavaScript
		wp_enqueue_script( 'jquery-ui-slider', '', array( 'jquery', 'jquery-ui' ), false, true );
		wp_enqueue_script( 'jquery-ui-datepicker', '', array( 'jquery', 'jquery-ui' ), false, true );
		//wp_enqueue_style( 'jquery-ui-theme', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css' );

		// WP Posts Meta Boxes
		$metabox_fn = array( 'benchmarkemaillite_posts', 'metabox' );
		add_meta_box( 'benchmark-email-lite', 'Benchmark Email Lite', $metabox_fn, 'post', 'side', 'default' );
		add_meta_box( 'benchmark-email-lite', 'Benchmark Email Lite', $metabox_fn, 'page', 'side', 'default' );
	}

	// Sister Product Function
	static function wp_dashboard_setup() {
		$message = '';

		// Handle Dismissal Request
		if( ! empty( $_REQUEST['bmel_dismiss_sister'] ) && check_admin_referer( 'bmel_dismiss_sister' ) ) {
			update_option( 'bmel_sister_dismissed', current_time( 'timestamp') );
		}

		// Check Sister Product
		$sister_dismissed = get_option( 'bmel_sister_dismissed' );
		if(
			$sister_dismissed < current_time( 'timestamp') - 86400 * 90
			&& is_plugin_inactive( 'woo-benchmark-email/woo-benchmark-email.php' )
			&& current_user_can( 'activate_plugins' )
		) {

			// Plugin Installed But Not Activated
			if( file_exists( WP_PLUGIN_DIR . '/woo-benchmark-email/woo-benchmark-email.php' ) ) {
				$message =
					__( 'Activate our sister product Woo Benchmark Email to connect carts and orders.', 'benchmark-email-lite' )
					. sprintf(
						' &nbsp; <strong style="font-size:1.25em;"><a href="%s">%s</a></strong>',
						benchmarkemaillite_admin::get_sister_activate_link(),
						__( 'Activate Now', 'benchmark-email-lite' )
					);

			// Plugin Not Installed
			} else {
				$message =
					__( 'Install our sister product Woo Benchmark Email to connect carts and orders.', 'benchmark-email-lite' )
					. sprintf(
						' &nbsp; <strong style="font-size:1.25em;"><a href="%s">%s</a></strong>',
						benchmarkemaillite_admin::get_sister_install_link(),
						__( 'Install Now', 'benchmark-email-lite' )
					);
			}

			// Dismiss Link
			$message .= sprintf(
				' <a style="float:right;" href="%s">%s</a>',
				benchmarkemaillite_admin::get_sister_dismiss_link(),
				__( 'dismiss for 90 days', 'benchmark-email-lite' )
			);
		}

		// Output Message
		if( $message ) {
			echo sprintf(
				'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
				print_r( $message, true )
			);
		}
	}

	// Sister Install Link
	static function get_sister_install_link() {
		$action = 'install-plugin';
		$slug = 'woo-benchmark-email';
		return wp_nonce_url(
			add_query_arg(
				array( 'action' => $action, 'plugin' => $slug ),
				admin_url( 'update.php' )
			),
			$action . '_' . $slug
		);
	}

	// Sister Activate Link
	static function get_sister_activate_link( $action='activate' ) {
		$plugin = 'woo-benchmark-email/woo-benchmark-email.php';
		$_REQUEST['plugin'] = $plugin;
		return wp_nonce_url(
			add_query_arg(
				array( 'action' => $action, 'plugin' => $plugin, 'plugin_status' => 'all', 'paged' => '1&s' ),
				admin_url( 'plugins.php' )
			),
			$action . '-plugin_' . $plugin
		);
	}

	// Sister Dismiss Notice Link
	static function get_sister_dismiss_link() {
		$url = wp_nonce_url( 'index.php?bmel_dismiss_sister=1', 'bmel_dismiss_sister' );
		return $url;
	}

}

class benchmarkemaillite_frontend {

	// WP Front End Shortcode
	static function shortcode( $atts ) {

		// Ensure Widget ID Is Specified
		if( ! isset( $atts['widget_id'] ) ) { return; }
		$atts = shortcode_atts(
			array(
				'widget_id' => '',
				'before_widget' => '',
				'after_widget' => '',
				'before_title' => '<h2 class="widgettitle">',
				'after_title' => '</h2>',
			),
			$atts
		);
		$widgets = get_option( 'widget_benchmarkemaillite_widget' );

		// Ensure Widget Id Is Found
		if( ! isset( $widgets[$atts['widget_id']] ) ) { return; }
		$instance = $widgets[$atts['widget_id']];
		$instance['widgetid'] = $atts['widget_id'];

		// Temporarily Disable Page Filtering And Return Widget Output
		benchmarkemaillite_widget::$is_shortcode = true;
		benchmarkemaillite_widget::$pagefilter = false;
		ob_start();
		the_widget( 'benchmarkemaillite_widget', $instance );
		$result = ob_get_contents();
		ob_end_clean();
		benchmarkemaillite_widget::$pagefilter = true;
		benchmarkemaillite_widget::$is_shortcode = false;
		return $result;
	}

}
