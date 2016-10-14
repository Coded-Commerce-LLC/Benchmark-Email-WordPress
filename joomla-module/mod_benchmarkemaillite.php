<?php

/*******************************************************
 This file is the main widget controller used by Joomla.
********************************************************/

// Load Module Translations
$language = JFactory::getLanguage();
$language_tag = $language->getTag();
$language->load( 'mod_benchmarkemaillite.sys', dirname(__FILE__), $language_tag, true );

// Joomla WP Bootup
require_once( 'administrator/components/com_benchmarkemaillite/controllers/joomla-bootup.php' );

// Get Active Module And Parameters
$module = JModuleHelper::getModule( 'mod_benchmarkemaillite' );
$params = new JRegistry( $module->params );

// Build Widget Fields List
$fields = array();
$fields_required = array();
for( $i=1; $i <= 10; $i++ ) {
	$field = $params->get( 'field' . $i );
	if( ! $field ) { continue; }
	$fields[] = $field;
	$fields_required[] = 1;
}

// Construct Widget
$args = array( 'before_widget' => '', 'before_title' => '', 'after_title' => '', 'after_widget' => '' );
$instance = array(
	'button' => 'Subscribe',
	'description' => $params->get( 'introduction' ),
	'fields' => $fields,
	'fields_labels' => $fields,
	'fields_required' => $fields_required,
	'filter' => 1,
	'list' => 'e01c3d95-bb28-4dd0-af3b-4aa9689cdd80|Sample Contact List|175166',
	'page' => 0,
	'title' => '',
	'widgetid' => $module->id,
);
$widget = new benchmarkemaillite_widget;

// Show Widget
$widget->widget( $args, $instance );
