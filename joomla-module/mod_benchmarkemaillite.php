<?php

/*******************************************************
 This file is the main widget controller used by Joomla.
********************************************************/

// Joomla WP Bootup, Also Sets API Key
require_once( JPATH_ADMINISTRATOR . '/components/com_benchmarkemaillite/controllers/joomla-wp-bootup.php' );

// Instantiate Widget Model
$widget = new benchmarkemaillite_widget;

// Process Submissions
$widget->widgets_init();

// Load Module Translations
$language = JFactory::getLanguage();
$language_tag = $language->getTag();
$language->load( 'mod_benchmarkemaillite.sys', dirname( __FILE__ ), $language_tag, true );

// Get Active Module And Parameters
$module = JModuleHelper::getModule( 'mod_benchmarkemaillite' );
$params = new JRegistry( $module->params );

// Build Widget Fields List
$fields = array();
$fields_required = array();
for( $i = 1; $i <= 10; $i ++ ) {
	$field = $params->get( 'field' . $i );
	if( ! $field ) { continue; }
	$fields[] = $field;
	$fields_required[] = 1;
}

// Construct Widget
$args = array( 'before_widget' => '', 'before_title' => '', 'after_title' => '', 'after_widget' => '' );
$listdata = array( benchmarkemaillite_api::$token, 'Sample Contact List', 175166 );
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
$widget->widget( $args, $instance );
