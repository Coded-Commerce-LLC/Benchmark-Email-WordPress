<?php

/******************************************************
 This file is the main admin controller used by Joomla.
*******************************************************/

// Admin Tool Bar
JToolBarHelper::title( 'Benchmark Email Lite', 'envelope' );
JToolBarHelper::preferences( 'com_benchmarkemaillite' );

// Load Component Translations
$language = JFactory::getLanguage();
$language_tag = $language->getTag();
$language->load( 'com_benchmarkemaillite.sys', dirname(__FILE__), $language_tag, true );

// Stand-Ins For WP Core Functions
if( ! function_exists( 'get_option' ) ) {
	function get_option( $arg ) {
		return array(  );
	}
}
if( ! function_exists( 'get_transient' ) ) {
	function get_transient( $arg ) { }
}
if( ! function_exists( 'set_transient' ) ) {
	function set_transient( $arg1, $arg2, $arg3 ) { }
}
if( ! function_exists( 'current_time' ) ) {
	function current_time( $arg1, $arg2=0 ) {
		return time();
	}
}
if( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $arg1, $arg2 ) {
		return $arg2;
	}
}
if( ! function_exists( '__' ) ) {
	function __( $arg1, $arg2='benchmark-email-lite' ) {
		return JText::_( $arg1 );
	}
}
if( ! function_exists( '_e' ) ) {
	function _e( $arg1, $arg2='benchmark-email-lite' ) {
		echo JText::_( $arg1 );
	}
}

// Include XML-RPC Client
require_once( 'assets/class-IXR.php' );

// Include Models
require_once( 'components/com_benchmarkemaillite/models/class.api.php' );

// Get API Key From Settings
$benchmarkemaillite_params = JComponentHelper::getParams( 'com_benchmarkemaillite' );
benchmarkemaillite_api::$token = $benchmarkemaillite_params->get( 'api_keys' );
benchmarkemaillite_api::handshake( array( benchmarkemaillite_api::$token ) );

// Report Missing API Key
if( ! benchmarkemaillite_api::$token ) {
?>

<h2>Setup Instructions:</h2>

<ol>
	<li><?php _e( 'COM_BENCHMARKEMAILLITE_CONFIG1_DESC1' ); ?></li>
	<li><?php _e( 'COM_BENCHMARKEMAILLITE_CONFIG1_DESC2' ); ?></li>
</ol>

<?php
}

// Look Up Their Lists And Forms
else {
	$contact_lists = benchmarkemaillite_api::lists();
	$signup_forms = benchmarkemaillite_api::signup_forms();
?>

<div class="alert alert-info">
	<span class="icon-info"></span>
	<?php _e( 'COM_BENCHMARKEMAILLITE_CONFIG1_DESC4' ); ?>
</div>

<h2><?php _e( 'COM_BENCHMARKEMAILLITE_HEADING_LISTS' ); ?></h2>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS1' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS2' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS3' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_LISTS4' ); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php foreach( $contact_lists as $val ) { ?>
		<tr>
			<td><?php echo $val['id']; ?></td>
			<td><?php echo $val['listname']; ?></td>
			<td><?php echo $val['contactcount']; ?></td>
			<td><?php echo $val['createdDate']; ?></td>
		</tr>
		<?php } ?>

	</tbody>
</table>

<h2><?php _e( 'COM_BENCHMARKEMAILLITE_HEADING_FORMS' ); ?></h2>

<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_FORMS1' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_FORMS2' ); ?></th>
			<th><?php _e( 'COM_BENCHMARKEMAILLITE_TABLE_FORMS3' ); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php foreach( $signup_forms as $val ) { ?>
		<tr>
			<td><?php echo $val['id']; ?></td>
			<td><?php echo $val['listName']; ?></td>
			<td><?php echo $val['toListName']; ?></td>
		</tr>
		<?php } ?>

	</tbody>
</table>

<?php } ?>
