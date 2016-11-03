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
		if ( $pagenow == 'widgets.php' ) {
			$js_file = BMEL_DIR_URL . 'assets/js/widget.js';
			wp_enqueue_script( 'bmel_widgetadmin', $js_file, array(), false, false );
		}

		// WP Posts Admin JavaScript
		wp_enqueue_script( 'jquery-ui-slider', '', array( 'jquery', 'jquery-ui' ), false, true );
		wp_enqueue_script( 'jquery-ui-datepicker', '', array( 'jquery', 'jquery-ui' ), false, true );
		wp_enqueue_style( 'jquery-ui-theme', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css' );
		$metabox_fn = array( 'benchmarkemaillite_posts', 'metabox' );
		add_meta_box( 'benchmark-email-lite', 'Benchmark Email Lite', $metabox_fn, 'post', 'side', 'default' );
		add_meta_box( 'benchmark-email-lite', 'Benchmark Email Lite', $metabox_fn, 'page', 'side', 'default' );
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
