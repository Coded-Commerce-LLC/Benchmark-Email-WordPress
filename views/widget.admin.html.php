<p>
	<label for="<?php echo $this->get_field_id( 'list' ); ?>">
		<?php _e( 'Contact list or signup form relationship', 'benchmark-email-lite' ); ?>:
	</label>
	<select style="width:100%;" name="<?php echo $this->get_field_name( 'list' ); ?>" id="<?php echo $this->get_field_id( 'list' ); ?>">
	<?php echo $signup_forms; ?>
	</select><br />
	<small><?php _e( 'Choose a single list or a sign-up form from your Benchmark Email account. Choosing a signup form means we use the Opt In confirmation email and subscription list(s) defined within the selected signup form.', 'benchmark-email-lite' ); ?></small>
</p>
<p>
	<label for="<?php echo $this->get_field_name( 'title' ); ?>-title">
		<?php _e( 'Signup form title', 'benchmark-email-lite' ); ?>:
	</label>
	<input class="widefat" id="<?php echo $this->get_field_name( 'title' ); ?>-title" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'description' ); ?>">
		<?php _e( 'Signup form introduction', 'benchmark-email-lite' ); ?>:
	</label>
	<textarea class="widefat" cols="20" rows="3" name="<?php echo $this->get_field_name( 'description' ); ?>" id="<?php echo $this->get_field_id( 'description' ); ?>"><?php echo esc_html( $instance['description'] ); ?></textarea><br />
	<input type="checkbox" value="1" <?php checked($instance['filter'], 1); ?> name="<?php echo $this->get_field_name( 'filter' ); ?>" id="<?php echo $this->get_field_id( 'filter' ); ?>" />
	<label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php _e( 'Automatically add paragraphs', 'benchmark-email-lite' ); ?></label>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'page' ); ?>">
		<?php _e( 'Limit to page', 'benchmark-email-lite' ); ?>:
	</label>
	<?php
	wp_dropdown_pages(
		array(
			'depth' => 0,
			'child_of' => 0,
			'selected' => esc_attr( $instance['page'] ),
			'echo' => 1,
			'name' => $this->get_field_name( 'page' ),
			'id' => $this->get_field_id( 'page' ),
			'show_option_none' => __( 'Show Everywhere', 'benchmark-email-lite' ),
		)
	);
	?>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'button' ); ?>">
		<?php _e( 'Submit button text', 'benchmark-email-lite' ); ?>:
	</label>
	<input class="widefat" name="<?php echo $this->get_field_name( 'button' ); ?>" id="<?php echo $this->get_field_id( 'button' ); ?>" type="text" value="<?php echo esc_attr( $instance['button'] ); ?>" />
</p>
<table>
	<thead>
		<tr>
			<th><?php _e( 'Field Name', 'benchmark-email-lite' ); ?></th>
			<th><?php _e( 'Label', 'benchmark-email-lite' ); ?></th>
			<th><?php _e( 'Required', 'benchmark-email-lite' ); ?></th>
			<th colspan="2"><?php _e( 'Tools', 'benchmark-email-lite' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$i = 0;
		foreach( $instance['fields'] as $key => $selected ) {
			$i++;
			if( ! $key ) { $key = 'INSERT-KEY'; }
			$label = isset( $instance['fields_labels'][$key] ) ? $instance['fields_labels'][$key] : $selected;
			$required = isset( $instance['fields_required'][$key] ) ? $instance['fields_required'][$key] : 0;
		?>
		<tr<?php if ($i === 1) { echo ' style="display:none;" class="bmebase"'; } ?>>

			<?php if ( $selected == 'Email' ) { ?>
				<td>
					<span style="padding:0 0 0 8px;">
						<input type="hidden" value="Email" name="<?php echo $this->get_field_name('fields'); ?>[<?php echo $key; ?>]" />
						[<?php _e( 'Email address', 'benchmark-email-lite' ); ?>]
					</span>
				</td>
				<td>
					<input type="text" size="15" maxlength="50" value="<?php echo $label; ?>" name="<?php echo $this->get_field_name('fields_labels'); ?>[<?php echo $key; ?>]" />
				</td>
				<td style="text-align:center;">
					<input type="hidden" value="1" name="<?php echo $this->get_field_name('fields_required'); ?>[<?php echo $key; ?>]" />
					<img src="images/yes.png" width="16" height="16" />
				</td>
			<?php } else { ?>
				<td>
					<select class="bmefields" name="<?php echo $this->get_field_name('fields'); ?>[<?php echo $key; ?>]">
					<?php foreach( $fields as $i => $field ) { ?>
					<option value="<?php echo $field; ?>"<?php if( $selected == $field ) { echo ' selected="selected"'; } ?>><?php echo $fields_labels[$i]; ?></option>
					<?php } ?>
					</select>
				</td>
				<td>
					<input type="text" size="15" maxlength="50" class="bmelabels" value="<?php echo $label; ?>" name="<?php echo $this->get_field_name('fields_labels'); ?>[<?php echo $key; ?>]" />
				</td>
				<td style="text-align:center;">
					<input type="checkbox" value="1" <?php checked( $required, '1' ); ?> name="<?php echo $this->get_field_name( 'fields_required' ); ?>[<?php echo $key; ?>]" />
				</td>
			<?php } ?>

			<td>
				<a href="#" class="bmemoveup" title="Move up" style="text-decoration:none;">
					<div style="float:left;background:transparent url(images/arrows.png) no-repeat 0 -36px;width:15px;height:15px;"> </div>
				</a>
				<a href="#" class="bmemovedown" title="Move down" style="text-decoration:none;">
					<div style="float:left;background:transparent url(images/arrows.png) no-repeat 0 -2px;width:15px;height:15px;"> </div>
				</a>
			</td>
			<td>
				<?php if ( $selected != 'Email' ) { ?>
				<a href="#" class="bmedelete" title="Delete" style="text-decoration:none;">
					<img alt="Delete" src="images/no.png" width="16" height="16" />
				</a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<p style="padding:5px 0 0 10px;">
	<a href="#" class="bmeadd">[<?php _e( 'Add new field', 'benchmark-email-lite' ); ?>]</a>
</p>
<p>
	<?php _e( 'Optional Shortcode', 'benchmark-email-lite' ); ?>:
	<?php if( is_numeric( $instance['widget_id'] ) ) { ?>
	<strong>[benchmark-email-lite widget_id="<?php echo $instance['widget_id']; ?>"]</strong><br />
	<small>
		<?php _e( 'To optionally use this widget inside of any post or page content, copy and paste this shortcode where you would like the signup form to be placed. You may also drag this widget into the Inactive Widgets section to prevent sidebar placement.', 'benchmark-email-lite' ); ?>
	</small>
	<?php } else { ?>
	<small><?php _e( 'Click the Save button to generate a shortcode for this widget.', 'benchmark-email-lite' ); ?></small>
	<?php } ?>
</p>
<p><?php _e( 'Need help? Please call Benchmark Email at 800.430.4095.', 'benchmark-email-lite' ); ?></p>
<script type="text/javascript">
bmebinding();
</script>
