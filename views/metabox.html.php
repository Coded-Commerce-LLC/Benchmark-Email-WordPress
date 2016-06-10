<style type="text/css">
.ui-datepicker {
	z-index: 3!important;
}
</style>

<p>
	<label for="bmelist"><?php _e( 'Select a list for this campaign', 'benchmark-email-lite' ); ?></label><br />
	<select style="width:100%;" name="bmelist" id="bmelist"><?php echo $dropdown; ?></select>
</p>
<p>
	<label for="bmetitle"><?php _e( 'Email name', 'benchmark-email-lite' ); ?></label><br />
	<input style="width:100%;" maxlength="100" type="text" id="bmetitle" name="bmetitle" value="<?php echo $title; ?>" /><br />
	<small><?php _e( 'For your personal use (not displayed in your emails).', 'benchmark-email-lite' ); ?></small>
</p>
<p>
	<label for="bmefrom"><?php _e( 'From name', 'benchmark-email-lite' ); ?></label><br />
	<input style="width:100%;" maxlength="100" type="text" id="bmefrom" name="bmefrom" value="<?php echo $from; ?>" /><br />
	<small><?php _e('Use something they\'ll instantly recognize, like your company name.', 'benchmark-email-lite' ); ?></small>
</p>
<p>
	<label for="bmesubject"><?php _e( 'Subject', 'benchmark-email-lite' ); ?></label><br />
	<input style="width:100%;" maxlength="500" type="text" id="bmesubject" name="bmesubject" value="<?php echo $subject; ?>" /><br />
	<small><?php _e( 'Interesting, non-spammy subject lines help your open rates.', 'benchmark-email-lite' ); ?></small>
</p>
<p><strong>Email Delivery Options</strong></p>
<p>
	<input type="radio" name="bmeaction" value="1" id="bmeaction_1" checked="checked" />
	<label for="bmeaction_1"><?php _e( 'Send a test email', 'benchmark-email-lite' ); ?></label>
</p>
<p>
	<textarea style="width:100%;" id="bmetestto" name="bmetestto" cols="30" rows="2"><?php echo $email; ?></textarea><br />
	<small><?php _e( 'Send a test version of your email. Enter up to 5 email addresses separated by a comma.', 'benchmark-email-lite' ); ?></small>
</p>
<p>
	<input type="radio" name="bmeaction" value="2" id="bmeaction_2" />
	<label for="bmeaction_2"><?php _e( 'Send immediately', 'benchmark-email-lite' ); ?></label><br />
	<small><?php _e( 'Your email will be sent within the next 15 mins.', 'benchmark-email-lite' ); ?></small>
</p>
<p>
	<input type="radio" name="bmeaction" value="3" id="bmeaction_3" />
	<label for="bmeaction_3"><?php _e( 'Schedule delivery', 'benchmark-email-lite' ); ?></label><br />
	<input type="text" class="datepicker" size="10" maxlength="10" id="bmedate" name="bmedate" value="<?php echo date( 'm/d/Y', $localtime_quarterhour ); ?>" />
	<label for="bmetime">@</label>
	<input type="text" size="5" maxlength="5" id="bmetime" name="bmetime" value="<?php echo date( 'H:i', $localtime_quarterhour ); ?>" />
	<?php echo $localtime_zone; ?>
	<div id="bmetime-slider"></div>
</p>
<p>
	<small>
		<?php _e( 'To schedule in a different timezone, set your', 'benchmark-email-lite' ); ?>
		<a target="_blank" href="options-general.php"><?php _e( 'WordPress', 'benchmark-email-lite' ); ?></a>
		<?php _e( 'and', 'benchmark-email-lite' ); ?>
		<a target="_blank" href="http://ui.benchmarkemail.com/EditSetting#ContentPlaceHolder1_UC_ClientSettings1_lblTimeZone">
		<?php _e( 'Benchmark Email', 'benchmark-email-lite' ); ?></a>
		<?php _e( 'timezones', 'benchmark-email-lite' ); ?>.
	</small>
</p>
<p><?php _e( 'Need help? Please call Benchmark Email at 800.430.4095.', 'benchmark-email-lite' ); ?></p>
<input type="hidden" name="bmesubmit" id="bmesubmit" value="" />
<input style="float:right;min-width:80px;font-weight:bold;" id="bmesubmitbtn" type="submit" class="button-primary" value="Send" /><br />
<div class="clear"> </div>

<script type="text/javascript">
jQuery( document ).ready( function() {
	jQuery( '.datepicker' ).datepicker();
	jQuery( '#bmetime-slider' ).slider( {
		value: <?php
				$minutes = explode( ':', date( 'H:i', $localtime_quarterhour ) );
				echo $minutes[0] * 60 + $minutes[1];
				?>,
		min: 0,
		max: 1425,
		step: 15,
		slide: function( event, ui ) {
			var hours = Math.floor( ui.value / 60 );
			var minutes = ui.value - ( hours * 60 );
			hours = ( hours < 10 ) ? '0' + hours : hours;
			minutes = ( minutes < 10 ) ? '0' + minutes : minutes;
			jQuery( '#bmetime' ).val( hours + ':' + minutes );
		}
	} );
} );
jQuery('#bmetestto').click(function(){
	jQuery('#bmeaction_1').attr('checked','checked');
});
jQuery('#bmedate, #bmetime').click(function(){
	jQuery('#bmeaction_3').attr('checked','checked');
});
jQuery('#bmesubmitbtn').click(function(e) {
	e.preventDefault();
	if (jQuery('#bmetitle').val() == '') {
		jQuery('#bmetitle').focus();
		jQuery('#bmetitle').css('border', '2px solid red');
		alert('Please enter an email name.');
	} else if (jQuery('#bmefrom').val() == '') {
		jQuery('#bmetitle').css('border', '0');
		jQuery('#bmefrom').focus();
		jQuery('#bmefrom').css('border', '2px solid red');
		alert('Please enter a from name.');
	} else if (jQuery('#bmesubject').val() == '') {
		jQuery('#bmetitle').css('border', '0');
		jQuery('#bmefrom').css('border', '0');
		jQuery('#bmesubject').focus();
		jQuery('#bmesubject').css('border', '2px solid red');
		alert('Please enter a subject.');
	} else if (jQuery('#bmetestto').val() == '' && jQuery('#bmeaction_1').is(':checked')) {
		jQuery('#bmetitle').css('border', '0');
		jQuery('#bmefrom').css('border', '0');
		jQuery('#bmesubject').css('border', '0');
		jQuery('#bmetestto').focus();
		jQuery('#bmetestto').css('border', '2px solid red');
		alert('Please enter email address(es) to send the test email to.');
	} else if (jQuery('#bmetestto').val().split(/@/g).length - 1 > 5 && jQuery('#bmeaction_1').is(':checked')) {
		jQuery('#bmetitle').css('border', '0');
		jQuery('#bmefrom').css('border', '0');
		jQuery('#bmesubject').css('border', '0');
		jQuery('#bmetestto').focus();
		jQuery('#bmetestto').css('border', '2px solid red');
		alert('Please do not exceed the limit of 5 email addresses.');
	} else {
		jQuery('#bmetitle').css('border', '0');
		jQuery('#bmefrom').css('border', '0');
		jQuery('#bmesubject').css('border', '0');
		jQuery('#bmetestto').css('border', '0');
		jQuery('#bmesubmit').val('yes');
		jQuery('#bmesubmitbtn').attr('disabled', 'disabled');
		if (document.getElementById('save-post')) { jQuery('#save-post').click(); }
		else { jQuery('#publish').click(); }
	}
});
</script>