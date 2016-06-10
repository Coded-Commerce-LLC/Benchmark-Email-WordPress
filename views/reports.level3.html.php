<p>
	<a href="<?php echo $url; ?>" title="<?php _e( 'Back to Email Summary', 'benchmark-email-lite' ); ?>">
	<?php _e( 'Back to Email Summary', 'benchmark-email-lite' ); ?></a>
</p>
<h3><?php echo $title; ?></h3>
<p><?php echo $instructions; ?></p>
<p>
	<strong><?php _e( 'Email name', 'benchmark-email-lite' ); ?>:</strong>
	<?php echo $response['emailName']; ?><br />
	<strong><?php _e( 'Subject', 'benchmark-email-lite' ); ?>:</strong>
	<?php echo $response['subject']; ?>
</p>
<?php benchmarkemaillite_display::maketable( $data ); ?>