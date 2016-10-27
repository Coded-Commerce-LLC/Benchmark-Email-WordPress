<?php

/*******************************************************
 This file is the main widget controller used by Joomla.
********************************************************/

// Joomla WP Bootup, Also Sets API Key
require_once( JPATH_ADMINISTRATOR . '/components/com_benchmarkemaillite/controllers/joomla-wp-bootup.php' );

// Load Module Translations
$language = JFactory::getLanguage();
$language_tag = $language->getTag();
$language->load( 'mod_benchmarkemaillite', dirname( __FILE__ ), $language_tag, true );

// Get Active Module And Parameters
$module = JModuleHelper::getModule( 'mod_benchmarkemaillite' );
$params = new JRegistry( $module->params );

// Build Widget Fields List
$fields = array();
$fields_required = array();
for( $i = 1; $i <= 26; $i ++ ) {
	$field = $params->get( 'field' . $i );
	if( ! $field ) { continue; }
	$fields[] = $field;
	$fields_required[] = 1;
}

// Process Submission
if( isset( $_POST['formid'] ) && strstr( $_POST['formid'], 'benchmark-email-lite' ) ) {

	// Sanitize Data
	$data = array();
	$uniqid = esc_attr( $_POST['uniqid'] );
	foreach( $fields as $val ) {
		$slug = sanitize_title( $val );
		$id = "{$slug}-{$uniqid}";
		$data[$val] = isset( $_POST[$id] ) ? esc_attr( $_POST[$id] ) : '';
	}

	// Handle Missing Email Address
	if( ! isset( $data['Email'] ) || ! is_email( $data['Email'] ) ) {
		return __( 'MOD_BENCHMARKEMAILLITE_ERROR_EMAIL' );
	}
	$data['email'] = $data['Email'];

	// Run Live Subscription
	$response = benchmarkemaillite_api::subscribe_simple( $params->get( 'list' ), $data );

	// Handle Response
	switch( $response ) {
		case 'added': echo __( 'MOD_BENCHMARKEMAILLITE_SUB_ADDED' ); break;
		case 'error': echo __( 'MOD_BENCHMARKEMAILLITE_SUB_ERROR' ); break;
		case 'updated': echo __( 'MOD_BENCHMARKEMAILLITE_SUB_UPDATED' ); break;
	}

// No Submission
} else {

	// Construct Widget For Front End Display
	$args = array( 'before_widget' => '', 'before_title' => '', 'after_title' => '', 'after_widget' => '' );
	$listdata = array( benchmarkemaillite_api::$token, '', $params->get( 'list' ) );
	$instance = array(
		'button' => 'Subscribe',
		'description' => $params->get( 'introduction' ),
		'fields' => $fields,
		'fields_labels' => $fields,
		'fields_required' => $fields_required,
		'filter' => 1,
		'list' => implode( '|', $listdata ),
		'page' => 0,
		'title' => '',
		'widgetid' => $module->id,
	);

	// Show Widget
	$widget = new benchmarkemaillite_widget;
	$widget->widget( $args, $instance );
}