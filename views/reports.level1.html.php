<p>
	<a href="<?php echo $url; ?>&amp;flush=1"><?php _e( 'Refresh Data', 'benchmark-email-lite' ); ?></a>
</p>

<table class="widefat" cellspacing="0">

	<?php
	$errors = array();
	foreach( $data as $key => $emails ) {
		if( ! $emails ) { $errors[] = $key; continue; }
	?>
	<thead>
		<tr>
			<th width="*">
				<?php _e( 'Email Name', 'benchmark-email-lite' ); ?>
				<small>(<?php _e( 'Emails for API key', 'benchmark-email-lite' ); ?>: <?php echo $key; ?>)</small>
			</th>
			<th width="100"><?php _e( 'Date Modified', 'benchmark-email-lite' ); ?></th>
		</tr>
	</thead>
	<tbody>

		<?php foreach( $emails as $id ) { $email = get_transient( "benchmarkemaillite_{$id}" ); ?>
		<tr>
			<td>
				<a href="<?php echo benchmarkemaillite_reports::url( array( 'campaign' => $id ) ); ?>">
				<?php echo $email['emailName']; ?></a><br />
				<small>
					<?php _e( 'List', 'benchmark-email-lite' ); ?>:
					<?php echo $email['toListName'] ?>
				</small>
			</td>
			<td><?php echo $email['scheduleDate']; ?></td>
		</tr>
		<?php } ?>

	</tbody>
	<?php } ?>

</table>

<?php if( $errors ) { ?>
<h4><?php _e( 'No results found for the following API key(s)', 'benchmark-email-lite' ); ?>:</h4>
<ul>
	<?php foreach( $errors as $key ) { ?>
	<li><?php echo $key; ?></li>
	<?php } ?>
</ul>

<?php } else if( ! isset( $key ) ) { ?>
<p><?php _e( 'Data will start appearing only after your emails have been sent.', 'benchmark-email-lite' ); ?></p>
<?php } ?>