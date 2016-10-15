<?php

/******************************************************
 This file is the main admin controller used by Joomla.
*******************************************************/

// Load Component Translations
$language = JFactory::getLanguage();
$language_tag = $language->getTag();
$language->load( 'com_benchmarkemaillite.sys', dirname(__FILE__), $language_tag, true );

// Admin Tool Bar
JToolBarHelper::title( 'Benchmark Email Lite', 'envelope' );
JToolBarHelper::custom( 'add-module', 'new', 'new', 'COM_BENCHMARKEMAILLITE_MODULE_ADD' );
JToolBarHelper::custom( 'edit-module', 'edit', 'edit', 'COM_BENCHMARKEMAILLITE_MODULE_EDIT' );
JToolBarHelper::preferences( 'com_benchmarkemaillite' );

// Joomla WP Bootup
require_once( 'controllers/joomla-bootup.php' );

// Vendor Handshake
benchmarkemaillite_api::handshake( array( benchmarkemaillite_api::$token ) );

// Report Missing API Key
if( ! benchmarkemaillite_api::$token ) {
?>

<div class="alert alert-info">
	<span class="icon-info"></span>
	<a href='index.php?option=com_config&view=component&component=com_benchmarkemaillite'>
		<?php _e( 'COM_BENCHMARKEMAILLITE_CONFIG1_DESC2' ); ?>
	</a>
</div>

<?php
}

// Has API Key
else {

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

	// Look Up Their Lists And Forms
	$contact_lists = benchmarkemaillite_api::lists();
	$signup_forms = benchmarkemaillite_api::signup_forms();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_benchmarkemaillite" />
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="benchmarkemaillite" />
</form>

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
