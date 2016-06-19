<?php

class benchmarkemaillite_settings {

	// Good Key Or Connection Message
	static function goodconnection_message() {
		return __( 'Valid API key and API server connection.', 'benchmark-email-lite' );
	}

	// Bad Key Or Connection Message
	static function badconnection_message() {
		return __( 'Invalid API key or API server connection problem.', 'benchmark-email-lite' );
	}

	/***************
	 WP Hook Methods
	 ***************/

	// Admin Area Notices
	static function admin_notices() {

		// Print Errors
		if ( $val = get_transient( 'benchmark-email-lite_error' ) ) {
			$val = "<p>Benchmark Email Lite</p><p>{$val}</p>";
			add_settings_error( 'bmel-notice', esc_attr( 'settings_updated' ), $val, 'error' );
			delete_transient( 'benchmark-email-lite_error' );
		}

		// Print Updates
		if ( $val = get_transient( 'benchmark-email-lite_updated' ) ) {
			$val = "<p>Benchmark Email Lite</p><p>{$val}</p>";
			add_settings_error( 'bmel-notice', esc_attr( 'settings_updated' ), $val, 'updated' );
			delete_transient( 'benchmark-email-lite_updated' );
		}

		// Settings API Notices
		settings_errors( 'bmel-notice' );
	}

	// Bad Configuration Message
	static function badconfig_message() {
		return sprintf(
			__(
				'Please configure your API key(s) on the %sBenchmark Email Lite settings page.%s',
				'benchmark-email-lite'
			),
			'<a href="admin.php?page=benchmark-email-lite-settings">', '</a>'
		);
	}

	// Triggered By Front And Back Ends - Try To Upgrade Plugin and Widget Settings
	// This Exists Because WordPress Doesn't Fire Activation Hook Upon Upgrades
	static function init() {

		// Check And Set Default Settings
		$options = get_option( 'benchmark-email-lite_group' );
		if( ! isset( $options[1] ) ) { $options[1] = array(); }
		if( ! isset( $options[2] ) ) { $options[2] = 'yes'; }
		if( ! isset( $options[3] ) ) { $options[3] = 'simple'; }
		if( ! isset( $options[4] ) ) { $options[4] = ''; }
		if( ! isset( $options[5] ) ) { $options[5] = 20; }
		update_option( 'benchmark-email-lite_group', $options );

		// Check And Set Defaults For Template Settings
		$options_template = get_option( 'benchmark-email-lite_group_template' );
		if( ! isset( $options_template['html'] ) || ! strstr( $options_template['html'], 'BODY_HERE' ) ) {
			$options_template['html'] = implode( '', file( dirname( __FILE__ ) . '/../templates/simple.html.php' ) );
			update_option( 'benchmark-email-lite_group_template', $options_template );
		}

		// Check For Compatible Vendor Handshake
		$handshake = get_transient( 'benchmark-email-lite_handshake' );
		if( $handshake != benchmarkemaillite_api::$handshake_version ) {
			benchmarkemaillite_api::handshake( $options[1] );
		}

		// Exit If Already Configured
		if( isset( $options[1][0] ) && $options[1][0] ) { return; }

		// Search For v1.x Widgets, Gather API Keys For Plugin Settings
		$tokens = benchmarkemaillite_widget::upgrade_widgets_1();

		// Gather Any Configured API Keys
		if( isset( $options[1][0] ) ) { $tokens = array_merge( $tokens, $options[1] ); }

		// Actions When Tokens Are Found
		if( $tokens ) {

			// Remove Duplicate API Keys
			$tokens = array_unique( $tokens );

			// Vendor Handshake With Benchmark Email
			benchmarkemaillite_api::handshake( $tokens );
		}

		// Save Initialized Settings
		$args = array( 1 => $tokens, 2 => $options[2], 3 => $options[3], 4 => $options[4], 5 => $options[5] );
		update_option( 'benchmark-email-lite_group', $args );

		// Search For v2.0.x Widgets And Upgrade To 2.1
		benchmarkemaillite_widget::upgrade_widgets_2();
	}

	// Admin Load
	static function admin_init() {

		// Handle Force Reconnection
		if( isset( $_POST['force_reconnect'] ) ) {
			delete_transient( 'benchmark-email-lite_serverdown' );
		}

		// Admin Settings Notice
		$options = get_option( 'benchmark-email-lite_group' );
		if ( ! isset( $options[1][0] ) || ! $options[1][0] ) {
			set_transient( 'benchmark-email-lite_error', self::badconfig_message() );
		}

		// Load Settings API
		$validate_fn = array( 'benchmarkemaillite_settings', 'validate' );
		register_setting( 'benchmark-email-lite_group', 'benchmark-email-lite_group', $validate_fn );
		register_setting( 'benchmark-email-lite_group_template', 'benchmark-email-lite_group_template', $validate_fn );

		// Settings API Sections Follow
		add_settings_section( 'bmel-main', __( 'Benchmark Email Credentials', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'section_main' ), 'bmel-pg1' );
		add_settings_section( 'bmel-campaign', __( 'New Email Campaign Preferences', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'section_campaign' ), 'bmel-pg1' );
		add_settings_section( 'bmel-diagnostics', __( 'Diagnostics', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'section_diagnostics' ), 'bmel-pg1' );
		add_settings_section( 'bmel-template', __( 'Email Template', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'section_template' ), 'bmel-pg2' );

		// Settings API Fields Follow
		add_settings_field( 'benchmark-email-lite-api-keys', __( 'API Key(s) from your Benchmark Email account(s)', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'field_api_keys' ), 'bmel-pg1', 'bmel-main' );
		add_settings_field( 'benchmark-email-lite-webpage-flag', __( 'Webpage version', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'field_webpage_flag' ), 'bmel-pg1', 'bmel-campaign' );
		add_settings_field( 'benchmark-email-lite-connection-timeout', __( 'Connection Timeout (seconds)', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'field_connection_timeout' ), 'bmel-pg1', 'bmel-diagnostics' );
		add_settings_field( 'benchmark-email-lite-template-html', __( 'Email Template HTML', 'benchmark-email-lite' ), array( 'benchmarkemaillite_settings', 'field_template' ), 'bmel-pg2', 'bmel-template' );
	}

	// Admin Menu
	static function admin_menu() {
		$favicon = plugin_dir_url( __FILE__ ) . '../favicon.png';
		$page_fn = array( 'benchmarkemaillite_settings', 'page' );
		add_menu_page( 'Benchmark Email Lite', 'BenchmarkEmail', 'manage_options', 'benchmark-email-lite', '', $favicon );
		add_submenu_page( 'benchmark-email-lite', 'Benchmark Email Lite Emails', __( 'Emails', 'benchmark-email-lite' ), 'manage_options', 'benchmark-email-lite', $page_fn );
		add_submenu_page( 'benchmark-email-lite', 'Benchmark Email Lite Settings', __( 'Settings', 'benchmark-email-lite' ), 'manage_options', 'benchmark-email-lite-settings', $page_fn );
		add_submenu_page( 'benchmark-email-lite', 'Benchmark Email Lite Template', __( 'Email Template', 'benchmark-email-lite' ), 'manage_options', 'benchmark-email-lite-template', $page_fn );
		add_submenu_page( 'benchmark-email-lite', 'Benchmark Email Lite Log', __( 'Communication Log', 'benchmark-email-lite' ), 'manage_options', 'benchmark-email-lite-log', $page_fn );
	}

	// Plugins Page Settings Link
	static function plugin_action_links( $links ) {
		$links['settings'] = sprintf(
			'<a href="admin.php?page=benchmark-email-lite-settings">%s</a>',
			__( 'Settings', 'benchmark-email-lite' )
		);
		return $links;
	}


	/********************
	 Settings API Methods
	 ********************/

	// Page Loaders
	static function page() {
		$options = get_option( 'benchmark-email-lite_group' );
		$tabs = array(
			'benchmark-email-lite' => __( 'Emails', 'benchmark-email-lite' ),
			'benchmark-email-lite-settings' => __( 'Settings', 'benchmark-email-lite' ),
			'benchmark-email-lite-template' => __( 'Email Template', 'benchmark-email-lite' ),
			'benchmark-email-lite-log' => __( 'Communication Log', 'benchmark-email-lite' ),
		);
		$current = isset( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : 'benchmark-email-lite';

		// Get Communication Logs
		$communications = get_transient( 'benchmark-email-lite_log' );
		$communications = is_array( $communications ) ? $communications : array();

		// Get Scheduled Cron Jobs
		$crons = false;
		if( $current == 'benchmark-email-lite-log' ) {
			$schedule = get_option( 'cron' );
			foreach( $schedule as $timestamp => $jobs ) {
				if( ! is_array( $jobs ) ) { continue; }
				foreach( $jobs as $slug => $job ) {
					if( $slug != 'benchmarkemaillite_queue' ) { continue; }

					// Get Scheduled Jobs
					$logs = get_option( 'benchmarkemaillite_queue' );
					$logs = explode( "\n", $logs );
					foreach( $logs as $log ) {
						if( ! $log ) { continue; }

						// Print Scheduled Job Details
						$log = explode( '||', $log );
						$list_info = explode( '|', $log[0] );
						$crons[] = array(
							'fields' => unserialize( $log[1] ),
							'key' => $list_info[0],
							'list_id' => $list_info[2],
							'list' => $list_info[1],
							'starts' => date( 'm/d/Y h:i:s A', $timestamp + wp_timezone_override_offset() * 3600 ),
						);
					}
				}
			}
		}

		// Output
		require( dirname( __FILE__ ) . '/../views/settings.html.php');
	}

	// Renders WP Settings API Forms
	static function print_settings( $page, $group ) {
		echo '<form method="post" action="options.php">';
		settings_fields( $group );
		do_settings_sections( $page );
		submit_button( __( 'Save Changes', 'benchmark-email-lite' ), 'primary', 'submit', false );
		echo sprintf(
			'&nbsp; <input type="reset" class="button-secondary" value="%s" />',
			__( 'Reset Changes', 'benchmark-email-lite' )
		);

		// Email Template Footer
		switch( $page ) {
			case 'bmel-pg2':
				echo sprintf(
					'&nbsp; <input name="submit" type="submit" class="button-secondary" value="%s" onclick="return confirm( \'%s\' );" />',
					__( 'Reset to Defaults', 'benchmark-email-lite' ),
					__( 'Are you sure you wish to load the default values and lose your customizations?', 'benchmark-email-lite' )
				);
		}
		echo '</form>';
	}

	// Settings API Sections Follow
	static function section_campaign() { }
	static function section_main() {
		$links = array(
			'signup' => sprintf(
				'<a target="BenchmarkEmail" href="http://www.benchmarkemail.com/Register?p=68907" target="BenchmarkEmail">%s</a>',
				__( 'Sign up for a FREE lifetime account', 'benchmark-email-lite')
			),
			'getkey' => sprintf(
				'<a target="BenchmarkEmail" href="http://ui.benchmarkemail.com/EditSetting#ContentPlaceHolder1_UC_ClientSettings1_lnkGenerate" target="BenchmarkEmail">%s</a>',
				__( 'log in to Benchmark Email to get your API key', 'benchmark-email-lite' )
			),
		);
		echo '
			<p>
				' . __( 'The API Key(s) connect your WordPress site with your Benchmark Email account(s).', 'benchmark-email-lite' ) . '
				' . __( 'Only one key is required per Benchmark Email account.', 'benchmark-email-lite' ) . '
				' . __( 'API Key(s) may expire after one year.', 'benchmark-email-lite' ) . '
			</p>
		';
		echo sprintf(
			'<p>%s %s %s</p>',
			$links['signup'],
			__( 'or', 'benchmark-email-lite' ),
			$links['getkey']
		);
	}
	static function section_diagnostics() { }
	static function section_template() {
		echo sprintf(
			'<p>%s</p>',
			__( 'The following is for advanced users to customize the HTML template that wraps the output of the post-to-campaign feature.', 'benchmark-email-lite' )
		);
	}

	// Settings API Fields Follow
	static function field_api_keys() {

		$options = get_option( 'benchmark-email-lite_group' );
		$results = array();
		$key = $options[1];

		for( $i = 0; $i < 5; $i ++ ) {
			$key[$i] = isset( $key[$i] ) ? $key[$i] : '';

			// Token Not Set
			if( ! $key[$i] ) {
				$results[$i] = '<img style="vertical-align:middle;opacity:0;" src="images/yes.png" alt="" width="16" height="16" />';

			// Check Token
			} else {
				benchmarkemaillite_api::$token = $key[$i];
				$results[$i] = is_array( benchmarkemaillite_api::lists() )
					? sprintf(
						'<img style="vertical-align:middle;" src="images/yes.png" alt="Yes" title="%s" width="16" height="16" />',
						self::goodconnection_message()
					) : sprintf(
						'<img style="vertical-align:middle;" src="images/no.png" alt="No" title="%s" width="16" height="16" />',
						self::badconnection_message()
					);
			}
		}

		$primary = __( 'Primary', 'benchmark-email-lite' );
		$optional = __( 'Optional', 'benchmark-email-lite' );
		echo sprintf(
			'
				<div>%s <input type="text" size="36" maxlength="50" name="benchmark-email-lite_group[1][]" value="%s" /> %s</div>
				<div>%s <input type="text" size="36" maxlength="50" name="benchmark-email-lite_group[1][]" value="%s" /> %s</div>
				<div>%s <input type="text" size="36" maxlength="50" name="benchmark-email-lite_group[1][]" value="%s" /> %s</div>
				<div>%s <input type="text" size="36" maxlength="50" name="benchmark-email-lite_group[1][]" value="%s" /> %s</div>
				<div>%s <input type="text" size="36" maxlength="50" name="benchmark-email-lite_group[1][]" value="%s" /> %s</div>
			',
			$results[0], $key[0], $primary,
			$results[1], $key[1], $optional,
			$results[2], $key[2], $optional,
			$results[3], $key[3], $optional,
			$results[4], $key[4], $optional
		);
	}

	static function field_webpage_flag() {
		$options = get_option( 'benchmark-email-lite_group' );
		echo sprintf(
			'<input id="benchmark-email-lite_group_2" type="checkbox" name="benchmark-email-lite_group[2]" value="yes"%s /> %s ',
			checked( 'yes', $options[2], false ),
			__( 'Include the link to view a Webpage Version at the top of emails?', 'benchmark-email-lite' )
		);
	}

	static function field_connection_timeout() {
		$options = get_option( 'benchmark-email-lite_group' );
		echo sprintf(
			__(
				'If the connection with the Benchmark Email server takes %s seconds or longer, '
				. 'disable connections for 5 minutes to prevent site administration from becoming sluggish. (Default: 20)',
				'benchmark-email-lite'
			),
			"<input id='benchmark-email-lite_group_5' type='text' size='2' maxlength='2' name='benchmark-email-lite_group[5]' value='{$options[5]}' />"
		);
	}

	static function field_template() {
		$options = get_option( 'benchmark-email-lite_group_template' );
		?>

		<textarea id="benchmark-email-template" name="benchmark-email-lite_group_template[html]"
			style="width:100%;" cols="30" rows="20"><?php echo $options['html']; ?></textarea>

		<ul style="list-style-type:square;">
			<li>
				<a target="_blank" href="https://ui.benchmarkemail.com/help-support/help-FAQ-details?id=100">
					<?php _e( 'This article helps you with email template coding.', 'benchmark-email-lite' ); ?>
				</a>
			</li>
			<li>
				<a target="_blank" href="http://www.w3schools.com/tags/ref_colorpicker.asp">
					<?php _e( 'Look up color codes here.', 'benchmark-email-lite' ); ?>
				</a>
			</li>
			<li><code>BODY_HERE</code><?php _e( 'will be replaced with the WP post body.', 'benchmark-email-lite' ); ?></li>
			<li><code>CATEGORIES</code><?php _e( 'will be replaced with the WP post categories.', 'benchmark-email-lite' ); ?></li>
			<li><code>EMAIL_MD5_HERE</code><?php _e( 'will be replaced with the WP site admin email hash (for Gravatar).', 'benchmark-email-lite' ); ?></li>
			<li><code>EXCERPT</code><?php _e( 'will be replaced with the WP post excerpt.', 'benchmark-email-lite' ); ?></li>
			<li><code>FEATURED_IMAGE_FULL</code><?php _e( 'will be replaced with the WP post featured image in full size.', 'benchmark-email-lite' ); ?></li>
			<li><code>FEATURED_IMAGE_LARGE</code><?php _e( 'will be replaced with the WP post featured image in large size.', 'benchmark-email-lite' ); ?></li>
			<li><code>FEATURED_IMAGE_MEDIUM</code><?php _e( 'will be replaced with the WP post featured image in medium size.', 'benchmark-email-lite' ); ?></li>
			<li><code>FEATURED_IMAGE_THUMBNAIL</code><?php _e( 'will be replaced with the WP post featured image in thumbnail size.', 'benchmark-email-lite' ); ?></li>
			<li><code>PERMALINK</code><?php _e( 'will be replaced with the WP post permalink.', 'benchmark-email-lite' ); ?></li>
			<li><code>TAGS</code><?php _e( 'will be replaced with the WP post tags.', 'benchmark-email-lite' ); ?></li>
			<li><code>TITLE_HERE</code><?php _e( 'will be replaced with the WP post title.', 'benchmark-email-lite' ); ?></li>
		</ul>

		<h3><?php _e( 'If caching is enabled on your hosting service, you may need to refresh cache after saving changes.', 'benchmark-email-lite' ); ?></h3>

		<h3><?php _e( 'Be sure to send email tests after making changes to the email template!', 'benchmark-email-lite' ); ?></h3>

		<?php
	}

	// Settings API Field Validations
	static function validate( $values ) {
		$options = get_option( 'benchmark-email-lite_group' );

		// Handle Reset to Defaults
		if( isset( $_REQUEST['submit'] ) && $_REQUEST['submit'] == 'Reset to Defaults' ) {
			$values['html'] = implode( '', file( dirname( __FILE__ ) . '/../templates/simple.html.php' ) );
		}

		foreach( $options as $key => $val ) {
			$val = isset( $values[$key] ) ? $values[$key] : '';

			// Process Saving Of API Keys
			if( $key == '1' ) {

				// Unserialize API Keys
				$values[1] = maybe_unserialize( $val );

				// Ensure This Is The Expected Field
				if( ! is_array( $values[1] ) ) { continue; }

				// Remove Empties
				$values[1] = array_filter( $values[1] );

				// Remove Duplicates
				$values[1] = array_unique( $values[1] );

				// Reset Keys
				$values[1] = array_values( $values[1] );

				// Remove Any Previous Errors
				delete_transient( 'benchmark-email-lite_error' );

				// Vendor Handshake With Benchmark Email
				benchmarkemaillite_api::handshake( $values[1] );

				// Deactivate Widgets of Deleted API Keys
				benchmarkemaillite_widget::cleanup_widgets( $values[1] );
			}

			// Sanitize Non Array Settings
			else { $values[$key] = esc_attr( $val ); }
		}
		add_settings_error( 'bmel-notice', esc_attr( 'settings_updated' ), __( 'Settings saved.', 'benchmark-email-lite' ), 'updated' );
		return $values;
	}
}

?>
