<div class="wrap">
	<?php echo get_screen_icon( 'plugins' ); ?>
	<h2>Benchmark Email Lite</h2>
	<h2 class="nav-tab-wrapper">&nbsp;
	<?php

	// Show Tabs
	foreach( $tabs as $tab => $name ) {
		$class = ( $tab == $current ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab{$class}' href='admin.php?page={$tab}'>{$name}</a>";
	}

	?>
	</h2>
	<?php

	// Handle Server Down On Either Tab
	if( $val = get_transient( 'benchmark-email-lite_serverdown' ) ) {

	?>
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

	// Server Up
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
			//krsort( $logs );
			echo sprintf(
				__( '<h3>Displaying %d recent communication logs</h3>', 'benchmark-email-lite' ),
				sizeof( $logs )
			);
			echo '
				<table class="widefat fixed">
					<thead>
						<tr>
							<th>Time</th>
							<th>Method</th>
							<th>Show/Hide</th>
						</tr>
					</thead>
					<tbody>
			';
			foreach( $logs as $i => $log ) {
				echo '
					<tr>
						<th scope="row">' . $log['Time'] . '</th>
						<th scope="row">' . $log['Request'][0] . '</th>
						<th scope="row"><a href="#" title="Show/Hide" onclick="jQuery( \'#log-' . $i . '\' ).toggle();return false;"><div class="dashicons dashicons-sort"></div></a></th>
					</tr>
					<tr>
						<td colspan="3"><pre style="display:none;" id="log-' . $i . '">' . print_r( $log, true ) . '</pre></td>
					</tr>
				';
			}
			echo '</tbody></table>';
			break;
	}

	?>
	<br />
	<hr />
	<p><?php _e( 'Need help? Please call Benchmark Email at 800.430.4095.', 'benchmark-email-lite' ); ?></p>
</div>