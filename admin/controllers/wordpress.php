<?php

// Plugin Display Class
class benchmarkemaillite_admin {

	// WP Admin Area
	static function admin_init() {

		// WP Widgets Admin JavaScript
		global $pagenow;
		if ( $pagenow == 'widgets.php' ) {
			$js_file = BMEL_DIR_URL . 'admin/assets/js/widget.js';
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
