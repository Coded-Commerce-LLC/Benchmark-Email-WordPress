<?php

defined( '_JEXEC' ) or die( 'Restricted access' ); 

class BenchmarkEmailLiteControllersDashboard {

	function execute() {

		// Joomla WP Bootup
		require_once( 'joomla-wp-bootup.php' );

		// Load Component Translations
		$language = JFactory::getLanguage();
		$language_tag = $language->getTag();
		$language->load( 'com_benchmarkemaillite.sys', BMEL_DIR_PATH, $language_tag, true );

		// Admin Tool Bar
		JToolBarHelper::title( 'Benchmark Email Lite', 'envelope' );
		JToolBarHelper::custom( 'add-module', 'new', 'new', 'COM_BENCHMARKEMAILLITE_MODULE_ADD' );
		JToolBarHelper::custom( 'edit-module', 'edit', 'edit', 'COM_BENCHMARKEMAILLITE_MODULE_EDIT' );
		JToolBarHelper::preferences( 'com_benchmarkemaillite' );

		// Vendor Handshake
		benchmarkemaillite_api::handshake( array( benchmarkemaillite_api::$token ) );

		// Report Missing API Key
		if( ! benchmarkemaillite_api::$token ) {
			echo '<div class="alert alert-info">'
				. '<span class="icon-info"></span>'
				. '<a href="index.php?option=com_config&view=component&component=com_benchmarkemaillite">'
				. __( 'COM_BENCHMARKEMAILLITE_CONFIG1_DESC2' )
				. '</a></div>';
			return;
		}

		// Handle Requests
		$task = isset( $_REQUEST['task'] ) ? $_REQUEST['task'] : '';
		switch( $task ) {
			case 'add-module':
				header( 'Location: index.php?option=com_modules&view=select' );
				exit;
			case 'edit-module':
				header( 'Location: index.php?option=com_modules' );
				exit;
		}

		// Look Up Their Lists
		$contact_lists = benchmarkemaillite_api::lists();

		// Render Default View
		require( BMEL_DIR_PATH . 'views/dashboard.html.php' );
	}
}