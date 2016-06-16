<div class="wrap">

	<?php echo get_screen_icon( 'plugins' ); ?>

	<h2>Benchmark Email Lite</h2>

	<h2 class="nav-tab-wrapper">&nbsp;

	<?php
	foreach( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab{$class}' href='admin.php?page={$tab}'>{$name}</a>";
	}
	?>

	</h2>

	<?php if( $val = get_transient( 'benchmark-email-lite_serverdown' ) ) { ?>
	<br />
	<div class="error">

		<h3><?php _e( 'Connection Timeout', 'benchmark-email-lite' ); ?></h3>

		<p><?php
		echo sprintf(
			__(
				'Due to sluggish communications, the Benchmark Email connection is automatically suspended for up to 5 minutes. '
				. 'If you encounter this error often, you may set the Connection Timeout setting to a higher value. %s',
				'benchmark-email-lite'
			), sprintf(
				'
					<br /><br />
					<form method="post" action="">
					<input type="submit" class="button-primary" name="force_reconnect" value="%s" />
					</form>
				',
				__( 'Attempt to Reconnect', 'benchmark-email-lite' )
			)
		);
		?></p>

	</div>

	<?php
	}

	// Show Selected Tab Content
	switch( $current ) {

		case 'benchmark-email-lite':
			benchmarkemaillite_reports::show();
			break;

		case 'benchmark-email-lite-settings':
			benchmarkemaillite_settings::print_settings( 'bmel-pg1', 'benchmark-email-lite_group' );
			break;

		case 'benchmark-email-lite-template':
			benchmarkemaillite_settings::print_settings( 'bmel-pg2', 'benchmark-email-lite_group_template' );
			break;

		case 'benchmark-email-lite-log':

			// Heading
			echo sprintf(
				'<h3>%s</h3>',
				sprintf(
					__( 'Displaying %d recent communication logs', 'benchmark-email-lite' ),
					sizeof( $communications )
				)
			);
			?>

			<table class="widefat">
				<thead>
					<tr>
						<th><?php _e( 'Started', 'benchmark-email-lite' ); ?></th>
						<th><?php _e( 'Lapsed', 'benchmark-email-lite' ); ?></th>
						<th><?php _e( 'Method', 'benchmark-email-lite' ); ?></th>
						<th><?php _e( 'Show/Hide', 'benchmark-email-lite' ); ?></th>
					</tr>
				</thead>
				<tbody>

					<?php foreach( $communications as $i => $log ) { ?>
					<tr>
						<td><?php echo $log['Time']; ?></td>
						<td><?php echo $log['Lapsed']; ?></td>
						<td><?php echo $log['Request'][0]; ?></td>
						<td>
							<a href="#" title="Show/Hide" onclick="jQuery( '#log-<?php echo $i; ?>' ).toggle();return false;">
								<div class="dashicons dashicons-sort"></div>
							</a>
						</td>
					</tr>
					<tr id="log-<?php echo $i; ?>" style="display: none;">
						<td colspan="4">
							<p><strong><?php _e( 'Request', 'benchmark-email-lite' ); ?></strong></p>
							<pre><?php print_r( $log['Request'] ); ?></pre>
							<p><strong><?php _e( 'Response', 'benchmark-email-lite' ); ?></strong></p>
							<pre><?php print_r( $log['Response'] ); ?></pre>
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>

			<?php if( $crons ) { ?>

			<h3><?php _e( 'Queue schedule in cron', 'benchmark-email-lite' ); ?></h3>

			<table class="widefat">
				<thead>
					<tr>
						<th><?php _e( 'Starts', 'benchmark-email-lite' ); ?></th>
						<th><?php _e( 'API Key', 'benchmark-email-lite' ); ?></th>
						<th><?php _e( 'List or Form', 'benchmark-email-lite' ); ?></th>
						<th><?php _e( 'Show/Hide', 'benchmark-email-lite' ); ?></th>
					</tr>
				</thead>
				<tbody>

					<?php foreach( $crons as $i => $log ) { ?>
					<tr>
						<td><?php echo $log['starts']; ?></td>
						<td><?php echo $log['key']; ?></td>
						<td><?php echo $log['list']; ?> (<?php echo $log['list_id']; ?>)</td>
						<td>
							<a href="#" title="Show/Hide" onclick="jQuery( '#cron-<?php echo $i; ?>' ).toggle();return false;">
								<div class="dashicons dashicons-sort"></div>
							</a>
						</td>
					</tr>
					<tr id="cron-<?php echo $i; ?>" style="display: none;">
						<td colspan="4">
							<p><strong><?php _e( 'Fields', 'benchmark-email-lite' ); ?></strong></p>
							<pre><?php print_r( $log['fields'] ); ?></pre>
						</td>
					</tr>
					<?php } ?>

				</tbody>
			</table>

			<?php
			}
			break;
	}

	?>
	<br />
	<hr />

	<p><?php _e( 'Need help? Please call Benchmark Email at 800.430.4095.', 'benchmark-email-lite' ); ?></p>

</div>