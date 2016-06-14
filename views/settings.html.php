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

		<p><?php echo sprintf(
			__( 'Due to sluggish communications, the Benchmark Email connection is automatically suspended for up to 5 minutes. If you encounter this error often, you may set the Connection Timeout setting to a higher value. %s', 'benchmark-email-lite' )
		, '
			<br /><br />
			<form method="post" action="">
			<input type="submit" class="button-primary" name="force_reconnect" value="' . __( 'Attempt to Reconnect', 'benchmark-email-lite' ) . '" />
			</form>
		' ); ?></p>

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
			$logs = get_transient( 'benchmark-email-lite_log' );
			$logs = is_array( $logs ) ? $logs : array();
			echo sprintf(
				__( '<h3>Displaying %d recent communication logs</h3>', 'benchmark-email-lite' ),
				sizeof( $logs )
			);
			?>

			<table class="widefat fixed">
				<thead>
					<tr>
						<th>Began</th>
						<th>Lapsed</th>
						<th>Method</th>
						<th>Show/Hide</th>
					</tr>
				</thead>
				<tbody>

					<?php foreach( $logs as $i => $log ) { ?>
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

			<h3><?php _e( 'Queue schedule in cron', 'benchmark-email-lite' ); ?></h3>

			<?php
			$schedule = get_option( 'cron' );
			foreach( $schedule as $timestamp => $jobs ) {
				if( ! is_array( $jobs ) ) { continue; }
				foreach( $jobs as $slug => $job ) {
					if( $slug != 'benchmarkemaillite_queue' ) { continue; }
					$logs = get_option( 'benchmarkemaillite_queue' );
					$logs = explode( "\n", $logs );
					foreach( $logs as $log ) {
						if( ! $log ) { continue; }
						$output = array();
						$log = explode( '||', $log );
						list( $output['API Key'], $output['List or Form Name'], $output['List or Form ID'] ) = explode( '|', $log[0] );
						$output['Fields'] = unserialize( $log[1] );
						echo sprintf(
							'<div>%s</div><pre>%s</pre>',
							date( 'r', $timestamp ),
							print_r( $output, true )
						);
					}
				}
			}
			break;
	}

	?>
	<br />
	<hr />

	<p><?php _e( 'Need help? Please call Benchmark Email at 800.430.4095.', 'benchmark-email-lite' ); ?></p>

</div>