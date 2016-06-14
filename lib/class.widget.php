<?php

class benchmarkemaillite_widget extends WP_Widget {
	static $response = array(), $pagefilter = true, $is_shortcode=false;

	// Load JavaScript Into Header On Widgets Page
	static function admin_init() {
		global $pagenow;
		if ( $pagenow == 'widgets.php' ) {
			wp_enqueue_script( 'bmel_widgetadmin', plugins_url( 'js/widget.admin.js', dirname( __FILE__ ) ), array(), false, false );
		}
	}

	// Upgrade 1.x Widgets
	static function upgrade_widgets_1() {
		$tokens = array();
		$widgets = get_option( 'widget_benchmarkemaillite_widget' );
		if( is_array( $widgets ) ) {
			foreach( $widgets as $instance => $widget ) {
				if( isset( $widget['token'] ) && $widget['token'] != '' ) {
					$tokens[] = $widget['token'];

					// Update List Selection In Widget
					benchmarkemaillite_api::$token = $widget['token'];
					$lists = benchmarkemaillite_api::lists();
					if( ! is_array( $lists ) ) { continue; }
					foreach( $lists as $list ) {
						if( $list['listname'] == $widget['list'] ) {
							$widgets[$instance]['list'] = "{$widget['token']}|{$widget['list']}|{$list['id']}";
						}
					}
				}
			}
			update_option( 'widget_benchmarkemaillite_widget', $widgets );
		}
		return $tokens;
	}

	// Upgrade 2.0.x widgets
	static function upgrade_widgets_2() {
		$widgets = get_option( 'widget_benchmarkemaillite_widget' );
		if( ! is_array( $widgets ) ) { return; }
		$changed = false;
		foreach ( $widgets as $instance => $widget ) {
			if ( ! is_array( $widget ) || isset( $widget['fields'] ) ) { continue; }
			$changed = true;
			if ( isset( $widget['showname'] ) && $widget['showname'] != '1' ) {
				$widgets[$instance]['fields'] = array( 'Email' );
				$widgets[$instance]['fields_labels'] = array( 'Email' );
				$widgets[$instance]['fields_required'] = array( 1 );
			} else {
				$widgets[$instance]['fields'] = array( 'First Name', 'Last Name', 'Email' );
				$widgets[$instance]['fields_labels'] = array( 'First Name', 'Last Name', 'Email' );
				$widgets[$instance]['fields_required'] = array( 0, 0, 1 );
			}
		}
		if ( $changed ) {
			update_option( 'widget_benchmarkemaillite_widget', $widgets );
		}
	}

	// Deactivate Widgets of Deleted API Keys
	static function cleanup_widgets( $api_keys ) {
		$delete = array();

		// Get All Widgets Of This Plugin
		$my_widgets = get_option( 'widget_benchmarkemaillite_widget' );

		// Only Proceed If There Are Widgets Of This Plugin
		if( ! is_array( $my_widgets ) ) { return; }

		// Get All Widget Sidebars
		$all_widget_sidebars = get_option( 'sidebars_widgets' );

		// Loop Widgets Of This Plugin
		foreach( $my_widgets as $instance => $widget ) {
			$widget_id = "benchmarkemaillite_widget-{$instance}";

			// Widget Must Have 'list' Property
			if( ! is_array( $widget ) || ! isset( $widget['list'] ) ) { continue; }
			$list = explode( '|', $widget['list'] );

			// Widget API Key Isn't Current
			if( ! in_array( $list[0], $api_keys ) ) {

				// Loop All Widget Sidebars
				foreach( $all_widget_sidebars as $sidebar_id => $sidebar_widgets ) {

					// Must Not Be The Inactive Sidebar
					if( $sidebar_id == 'wp_inactive_widgets' ) { continue; }

					// Sidebar Must Have Widgets
					if( ! is_array( $sidebar_widgets ) ) { continue; }

					// Search For This Widget
					$sidebar_widget_id = array_search( $widget_id, $sidebar_widgets );

					// If Found, Add To Delete List
					if( $sidebar_widget_id !== false ) {
						$delete[] = array( $sidebar_id, $sidebar_widget_id, $widget_id );
					}
				}
			}
		}

		// Continue Only If There Are Widgets To Delete
		if( ! $delete ) { return; }

		// Process Delete List
		foreach( $delete as $todo ) {
			list( $sidebar_id, $sidebar_widget_id, $widget_id ) = $todo;
			unset( $all_widget_sidebars[$sidebar_id][$sidebar_widget_id] );
			$all_widget_sidebars['wp_inactive_widgets'][] = $widget_id;
		}

		// Save Changes
		update_option( 'sidebars_widgets', $all_widget_sidebars );

		// Inform About Deactivation
		set_transient(
			'benchmark-email-lite_updated',
			sprintf(
				__( 'We moved %d widget(s) of no longer existing API keys to your Inactive Widgets sidebar.', 'benchmark-email-lite' ),
				sizeof( $delete )
			)
		);
	}


	/************************
	 WORDPRESS WIDGET METHODS
	 ************************/

	// Constructor - Cannot Be Static
	function __construct() {
		$widget_ops = array(
			'classname' => 'benchmarkemaillite_widget',
			'description' => __( 'Create a Benchmark Email newsletter widget in WordPress.', 'benchmark-email-lite' )
		);
		$control_ops = array( 'width' => 400 );
		parent::__construct( 'benchmarkemaillite_widget', 'Benchmark Email Lite', $widget_ops, $control_ops );
	}

	// Build the Widget Settings Form - Cannot Be Static
	function form( $instance ) {

		// Optional Fields List
		$fields = array(
			'First Name' => __( 'First Name', 'benchmark-email-lite' ),
			'Last Name' => __( 'Last Name', 'benchmark-email-lite' ),
			'Middle Name' => __( 'Middle Name', 'benchmark-email-lite' ),
			'Address' => __( 'Address', 'benchmark-email-lite' ),
			'City' => __( 'City', 'benchmark-email-lite' ),
			'State' => __( 'State', 'benchmark-email-lite' ),
			'Zip' => __( 'Zip', 'benchmark-email-lite' ),
			'Country' => __( 'Country', 'benchmark-email-lite' ),
			'Phone' => __( 'Phone', 'benchmark-email-lite' ),
			'Fax' => __( 'Fax', 'benchmark-email-lite' ),
			'Cell Phone' => __( 'Cell Phone', 'benchmark-email-lite' ),
			'Company Name' => __( 'Company Name', 'benchmark-email-lite' ),
			'Job Title' => __( 'Job Title', 'benchmark-email-lite' ),
			'Business Phone' => __( 'Business Phone', 'benchmark-email-lite' ),
			'Business Fax' => __( 'Business Fax', 'benchmark-email-lite' ),
			'Business Address' => __( 'Business Address', 'benchmark-email-lite' ),
			'Business City' => __( 'Business City', 'benchmark-email-lite' ),
			'Business State' => __( 'Business State', 'benchmark-email-lite' ),
			'Business Zip' => __( 'Business Zip', 'benchmark-email-lite' ),
			'Business Country' => __( 'Business Country', 'benchmark-email-lite' ),
			'Notes' => __( 'Notes', 'benchmark-email-lite' ),
			'Extra 3' => __( 'Extra 3', 'benchmark-email-lite' ),
			'Extra 4' => __( 'Extra 4', 'benchmark-email-lite' ),
			'Extra 5' => __( 'Extra 5', 'benchmark-email-lite' ),
			'Extra 6' => __( 'Extra 6', 'benchmark-email-lite' ),
		);

		// Prepare Default Values
		$defaults = array(
			'button' => __( 'Subscribe', 'benchmark-email-lite' ),
			'description' => __( 'Get the latest news and information direct from us to you!', 'benchmark-email-lite' ),
			'fields_labels' => array( $fields['First Name'], $fields['Last Name'], __( 'Email', 'benchmark-email-lite' ) ),
			'fields_required' => array( 0, 0, 1 ),
			'fields' => array( 'First Name', 'Last Name', 'Email' ),
			'filter' => 0,
			'list' => '',
			'page' => '',
			'title' => __( 'Subscribe to Newsletter', 'benchmark-email-lite' ),
		);

		// Get Widget ID And Saved Values
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		$instance['id'] = $this->id;
		$instance['widget_id'] = $this->number;

		// Get Drop Down Values
		$options = get_option( 'benchmark-email-lite_group' );
		if( ! isset( $options[1][0] ) || !$options[1][0] ) {
			$val = benchmarkemaillite_settings::badconfig_message();
			echo "<strong style='color:red;'>{$val}</strong>";
		}
		$signup_forms = benchmarkemaillite_display::print_lists( $options[1], $instance['list'], 'signup_forms' );

		// Insert "Add New" Hidden Row
		array_unshift( $instance['fields'], '' );
		array_unshift( $instance['fields_labels'], '' );
		array_unshift( $instance['fields_required'], 0 );

		// Print Widget
		require( dirname( __FILE__ ) . '/../views/widget.admin.html.php' );
	}

	// Save the Widget Settings - Cannot Be Static
	function update( $submitted, $instance ) {

		// Sanitize Admin Submitted Fields
		$instance['fields'] = array();
		$instance['fields_labels'] = array();
		$instance['fields_required'] = array();
		foreach( $submitted['fields'] as $key => $val ) {
			if( $key == 'INSERT-KEY' ) { continue; }
			$instance['fields'][$key] = esc_attr( $submitted['fields'][$key] );
			$instance['fields_labels'][$key] = esc_attr( $submitted['fields_labels'][$key] );
			$instance['fields_required'][$key] = isset( $submitted['fields_required'][$key] ) ? 1 : 0;
		}

		// Sanitize Other Admin Submissions
		$instance['button'] = esc_attr( $submitted['button'] );
		$instance['description'] = wp_kses_post( $submitted['description'] );
		$instance['filter'] = ( $submitted['filter'] ) ? 1 : 0;
		$instance['list'] = esc_attr( $submitted['list'] );
		$instance['page'] = absint( $submitted['page'] );
		$instance['title'] = esc_attr( $submitted['title'] );
		return $instance;
	}

	// Display the Widget - Cannot Be Static
	function widget( $args, $instance ) {

		// Widget Variables
		global $post;
		extract( $args );

		// Get Widget ID
		if( isset( $widget_id ) ) {
			$widgetid = explode( '-', $widget_id );
			$widgetid = isset( $widgetid[1] ) ? $widgetid[1] : $widgetid[0];
		} else { $widgetid = $instance['widgetid']; }

		// If Shortcode Mode, Remove Widget Wrapper Code
		if( self::$is_shortcode ) { $before_widget = ''; $after_widget = ''; }

		// Exclude from Pages/Posts Per Setting
		if(
			self::$pagefilter
			&& $instance['page']
			&& ( ! isset( $post->ID ) || $instance['page'] != $post->ID )
		) { return; }

		// Skip Output If Widget Is Not Yet Setup
		if( empty( $instance['list'] ) ) { return; }

		// Prepopulate Standard Fields If Logged In
		$user = wp_get_current_user();
		$first = ( $user->ID ) ? $user->user_firstname : '';
		$last = ( $user->ID ) ? $user->user_lastname : '';
		$email = ( $user->ID ) ? $user->user_email : '';

		// Build Widget Title And Intro Text
		$title = apply_filters( 'widget_title', $instance['title'] );
		$title = ( $title ) ? $before_title . $title . $after_title : '';
		$description = ( $instance['filter'] == 1 )
			? wpautop( $instance['description'] ) : $instance['description'];

		// Display Any Submission Response
		$printresponse = '';
		if( isset( self::$response[$widgetid][0] ) ) {
			$printresponse = ( self::$response[$widgetid][0] )
				? '<p style="font-weight:bold;color:green;">' . self::$response[$widgetid][1] . '</p>'
				: '<p style="font-weight:bold;color:red;">' . self::$response[$widgetid][1] . '</p>';

			// If Submission Without Errors, Output Response Without Form
			if( self::$response[$widgetid][0] ) {
				echo "{$before_widget}{$title}{$printresponse}{$after_widget}";
				return;
			}
		}

		// Compile Fields
		$fields = array();
		$uniqid = ( self::$is_shortcode ) ? "{$widgetid}_shortcode" : "{$widgetid}_sidebar";
		foreach( $instance['fields'] as $key => $field ) {

			// Build Unique Identifier For Field
			$id = sanitize_title( $field ) . "-{$key}-{$widgetid}-{$uniqid}";

			// Build Field Label
			$label = isset( $instance['fields_labels'][$key] ) ? $instance['fields_labels'][$key] : $field;

			// Prepopulate WP Field Values
			$value = '';
			switch( $field ) {
				case 'Email': $value = $email; break;
				case 'First Name': $value = $first; break;
				case 'Last Name': $value = $last; break;
			}

			// Repopulate Submitted Field Values
			$value = isset( $_REQUEST[$id] ) ? esc_attr( $_REQUEST[$id] ) : $value;

			// Store Field Data
			$fields[] = array( 'id' => $id, 'label' => $label, 'value' => $value );
		}

		// Output Widget
		require( dirname( __FILE__ ) . '/../views/widget.frontend.html.php' );
	}


	/******************
	 SUBSCRIPTION LOGIC
	 ******************/

	// Process Form Submission
	static function widgets_init() {

		// Proceed Processing Upon Widget Form Submission
		if( isset( $_POST['formid'] ) && strstr( $_POST['formid'], 'benchmark-email-lite' ) ) {

			// Get Widget Options for this Instance
			$instance = get_option( 'widget_benchmarkemaillite_widget' );
			$widgetid = esc_attr( $_POST['widgetid'] );
			$uniqid = esc_attr( $_POST['uniqid'] );
			$instance = $instance[$widgetid];

			// Sanitize Submission
			$data = array();
			foreach( $instance['fields'] as $key => $field ) {
				$fieldslug = sanitize_title( $field );
				$id = "{$fieldslug}-{$key}-{$widgetid}-{$uniqid}";
				$data[$field] = isset( $_POST[$id] ) ? esc_attr( $_POST[$id] ) : '';
			}

			// Run Subscription
			self::$response[$widgetid] = self::process_subscription( $instance['list'], $data );
		}
	}

	// Main Subscription Logic
	static function process_subscription( $bmelist, $data ) {

		// Handle Missing Email Address
		if( ! isset( $data['Email'] ) || ! is_email( $data['Email'] ) ) {
			return __( 'Please enter a valid email address.', 'benchmark-email-lite' );
		}

		// Get List Info
		list( benchmarkemaillite_api::$token, $listname, benchmarkemaillite_api::$listid ) = explode( '|', $bmelist );

		// Try to Run Live Subscription
		$response = benchmarkemaillite_api::subscribe( $bmelist, $data );

		// Handle Responses
		switch( $response ) {
			case 'added': return array( true, __( 'A verification email has been sent.', 'benchmark-email-lite' ) );
			case 'updated': return array( true, __( 'Successfully updated subscription.', 'benchmark-email-lite' ) );
			case 'queued': return array( true, __( 'Successfully queued subscription.', 'benchmark-email-lite' ) );
		}
	}

	// Queue Subscription
	static function queue_subscription( $bmelist, $data ) {
		$queue = get_option( 'benchmarkemaillite_queue' );
		$data = serialize( $data );
		$queue .= "{$bmelist}||{$data}\n";
		update_option( 'benchmarkemaillite_queue', $queue );

		// Load Queue File into WP Cron
		if( ! wp_next_scheduled( 'benchmarkemaillite_queue' ) ) {
			wp_schedule_single_event( time() + 300, 'benchmarkemaillite_queue' );
		}
	}

	// Process Subscription Queue Cron Request
	static function queue_upload() {

		// Continue Only If Queue Exists
		if( ! $queue = get_option( 'benchmarkemaillite_queue' ) ) { return; }
		delete_option( 'benchmarkemaillite_queue' );

		// Attempt to Subscribe Each Queued Record, Or Fail Back Into Queue
		$queue = explode( "\n", $queue );
		foreach( $queue as $row ) {
			$row = explode( '||', $row );
			$row[1] = unserialize( $row[1] );
			self::process_subscription( $row[0], $row[1] );
		}
	}
}

?>