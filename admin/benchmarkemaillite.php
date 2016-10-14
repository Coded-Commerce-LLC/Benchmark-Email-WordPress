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

// Joomla WP Bootup
require_once( 'controllers/joomla-bootup.php' );

// Vendor Handshake
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
