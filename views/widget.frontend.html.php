<?php echo $before_widget; ?>

<?php echo $title; ?>

<?php echo $description; ?>

<form class="benchmarkemaillite-subscribe" method="post" action="#benchmark-email-lite-<?php echo $uniqid; ?>" onsubmit="return benchmarkemaillite_<?php echo $uniqid; ?>(this);">
	<input type="hidden" name="formid" value="benchmark-email-lite-<?php echo $uniqid; ?>" />
	<input type="hidden" name="widgetid" value="<?php echo $widgetid; ?>" />
	<input type="hidden" name="uniqid" value="<?php echo $uniqid; ?>" />

	<?php foreach( $fields as $field ) { extract( $field ); ?>
	<div>
		<label for="<?php echo $id; ?>" style="display:block;"><?php echo $label; ?></label>
		<input type="text" maxlength="200" id="<?php echo $id; ?>" name="<?php echo $id; ?>" value="<?php echo $value; ?>" />
	</div>
	<?php } ?>

	<div><input type="submit" value="<?php echo $instance['button']; ?>" /></div>
	<div><?php echo $printresponse; ?></div>
</form>

<p id="subscribe_spinner-<?php echo $uniqid; ?>" style="display: none; text-align: center;">
	<br /><img alt="Loading" src="<?php echo includes_url( 'images/wpspin.gif' ); ?>" />
	<br /><?php _e( 'Loading - Please wait', 'benchmark-email-lite' ); ?>
</p>

<script type="text/javascript">
function benchmarkemaillite_<?php echo $uniqid; ?>(theForm) {
	var errors = new Array();
	<?php
	foreach( $instance['fields'] as $key => $field ) {
		$label = isset( $instance['fields_labels'][$key] ) ? $instance['fields_labels'][$key] : $field;
		$field = sanitize_title( $field );
		$id = "{$field}-{$key}-{$widgetid}-{$uniqid}";
		if( isset( $instance['fields_required'][$key] ) && $instance['fields_required'][$key] == '1' ) {
	?>
	var elem = document.getElementById( '<?php echo $id; ?>' );
	if (elem.value == '') { errors.push( '<?php echo $label; ?>' ); }
	<?php } } ?>
	if (errors.length > 0) {
		alert( '<?php _e( 'Please complete the field(s):', 'benchmark-email-lite' ); ?>\n' + errors.join( '\n' ) );
		return false;
	}
	document.getElementById( 'subscribe_spinner-<?php echo $uniqid; ?>' ).style.display = 'block';
	theForm.style.display = 'none';
	return true;
}
</script>

<?php echo $after_widget; ?>