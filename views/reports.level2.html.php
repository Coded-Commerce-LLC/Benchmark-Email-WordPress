<p>
	<a href="<?php echo $url; ?>&amp;flush=1"><?php _e( 'Refresh Data', 'benchmark-email-lite' ); ?></a>
</p>

<div style="float: left; min-width: 500px; width: 50%;">
	<p>
		<strong><?php _e( 'Email name', 'benchmark-email-lite' ); ?>:</strong> <?php echo $response['emailName']; ?>
		<br /><strong><?php _e( 'Subject', 'benchmark-email-lite' ); ?>:</strong> <?php echo $response['subject']; ?>
	</p>
	<div style="height: 400px;">
		<div id="chart_div"></div>
	</div>
</div>

<div style="float: right; width: 50%;">
	<h3><?php _e( 'Email Statistics', 'benchmark-email-lite' ); ?></h3>
	<table class="widefat">
		<thead>
			<tr>
				<th width="*"><?php _e( 'Statistic', 'benchmark-email-lite' ); ?></th>
				<th width="50"><?php _e( 'Value', 'benchmark-email-lite' ); ?></th>
				<th width="50"><?php _e( 'Percent', 'benchmark-email-lite' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php _e( 'Total Emails Sent', 'benchmark-email-lite' ); ?></td>
				<td><?php echo $response['mailSent']; ?></td>
				<td></td>
			</tr>
			<tr>
				<td><?php _e( 'Opened Emails', 'benchmark-email-lite' ); ?></td>
				<td>
					<?php echo ( $response['opens'] ) ? "<a href='{$url}opens' title='" . __( 'Click to view report', 'benchmark-email-lite' ) . "'>{$response['opens']}</a>" : 0; ?>
				</td>
				<td>
					<?php echo number_format( 100 * $response['opens'] / $response['mailSent'], 1 ); ?>%
				</td>
			</tr>
			<tr>
				<td><?php _e('Links Clicked', 'benchmark-email-lite'); ?></td>
				<td>
					<?php echo ( $response['clicks'] ) ? "<a href='{$url}clicks_all' title='" . __( 'Click to view report', 'benchmark-email-lite') . "'>{$response['clicks']}</a>" : 0; ?>
				</td>
				<td>
					<?php echo number_format( $response['clicks_percent'] , 1 ); ?>%
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Emails Forwarded', 'benchmark-email-lite' ); ?></td>
				<td>
					<?php echo ( $response['forwards'] ) ? "<a href='{$url}forwards' title='" . __( 'Click to view report', 'benchmark-email-lite') . "'>{$response['forwards']}</a>" : 0; ?>
				</td>
				<td></td>
			</tr>
			<tr>
				<td><?php _e( 'Emails Bounced', 'benchmark-email-lite' ); ?></td>
				<td>
					<?php echo ( $response['bounces']) ? "<a href='{$url}bounces' title='" . __( 'Click to view report', 'benchmark-email-lite' ) . "'>{$response['bounces']}</a>" : 0; ?>
				</td>
				<td>
					<?php echo number_format( 100 * $response['bounces'] / $response['mailSent'], 1 ); ?>%
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Unsubscribes', 'benchmark-email-lite' ); ?></td>
				<td>
					<?php echo ( $response['unsubscribes']) ? "<a href='{$url}unsubscribes' title='" . __( 'Click to view report', 'benchmark-email-lite' ) . "'>{$response['unsubscribes']}</a>" : 0; ?>
				</td>
				<td>
					<?php echo number_format( 100 * $response['unsubscribes'] / $response['mailSent'], 1 ); ?>%
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Unopened', 'benchmark-email-lite' ); ?></td>
				<td>
					<?php echo ( $response['unopens']) ? "<a href='{$url}unopens' title='" . __( 'Click to view report', 'benchmark-email-lite' ) . "'>{$response['unopens']}</a>" : 0; ?>
				</td>
				<td>
					<?php echo number_format( 100 * $response['unopens'] / ($response['mailSent'] ), 1 ); ?>%
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Abuse Reports', 'benchmark-email-lite' ); ?></td>
				<td><?php echo $response['abuseReports']; ?></td>
				<td></td>
			</tr>
		</tbody>
	</table>

	<h3><?php _e( 'Email Reports', 'benchmark-email-lite' ); ?></h3>
	<p>
		<form method="get" action="">
			<input type="hidden" name="page" value="benchmark-email-lite" />
			<input type="hidden" name="campaign" value="<?php echo $meta->campaign; ?>" />
			<button class="button-primary" type="submit" name="show" value="opens"
				<?php if( ! $response['opens'] ) { echo 'disabled="disabled"'; } ?>><?php _e( 'Opens', 'benchmark-email-lite' ); ?></button>
			<button class="button-primary" type="submit" name="show" value="bounces"
				<?php if( ! $response['bounces'] ) { echo 'disabled="disabled"'; } ?>><?php _e( 'Bounces', 'benchmark-email-lite' ); ?></button>
			<button class="button-primary" type="submit" name="show" value="unsubscribes"
				<?php if( ! $response['unsubscribes'] ) { echo 'disabled="disabled"'; } ?>><?php _e( 'Unsubscribes', 'benchmark-email-lite' ); ?></button>
			<button class="button-primary" type="submit" name="show" value="forwards"
				<?php if( ! $response['forwards'] ) { echo 'disabled="disabled"'; } ?>><?php _e( 'Forwards', 'benchmark-email-lite' ); ?></button>
			<button class="button-primary" type="submit" name="show" value="unopens"
				<?php if( ! $response['unopens'] ) { echo 'disabled="disabled"'; } ?>><?php _e( 'Unopens', 'benchmark-email-lite' ); ?></button>
		</form>
	</p>
</div>
<div style="clear:both;"> </div>

<?php

// Show Opens By Location
if( $response['opens'] ) {
	echo '<h3>' . __( 'Opens by Location', 'benchmark-email-lite' ) . '</h3>';
	self::showDetail( 'locations' );
}

// Show Click Performance
if( $response['clicks'] ) {
	echo '<h3>' . __( 'Click Performance', 'benchmark-email-lite' ) . '</h3>';
	self::showDetail( 'clicks' );
}

?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
google.load( 'visualization', '1', { packages:['corechart'] } );
google.setOnLoadCallback( drawChart );
function drawChart() {
	var data = google.visualization.arrayToDataTable( [
		['Item', 'Quantity'],
		['<?php _e( 'Opened', 'benchmark-email-lite' ); ?>', <?php echo $response['opens']; ?>],
		['<?php _e( 'Bounced', 'benchmark-email-lite' ); ?>', <?php echo $response['bounces']; ?>],
		['<?php _e( 'Unopened', 'benchmark-email-lite' ); ?>', <?php echo $response['unopens']; ?>]
	]);
	var options = {
		backgroundColor: { fill: 'transparent' },
		chartArea: { width: 400, height: 365 },
		width: 500,
		height: 400,
		is3D: true,
		legend: { position: 'bottom' },
		colors: ['77D9A1', 'F2A81D', '1C8DDE']
	};
	var chart = new google.visualization.PieChart( document.getElementById( 'chart_div' ) );
	chart.draw( data, options );
}
</script>