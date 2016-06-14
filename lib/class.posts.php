<?php

class benchmarkemaillite_posts {

	// Create Pages+Posts Metaboxes
	static function admin_init() {
		wp_enqueue_script( 'jquery-ui-slider', '', array( 'jquery', 'jquery-ui' ), false, true );
		wp_enqueue_script( 'jquery-ui-datepicker', '', array( 'jquery', 'jquery-ui' ), false, true );
		wp_enqueue_style( 'jquery-ui-theme', '//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.min.css' );
		$metabox_fn = array( 'benchmarkemaillite_posts', 'metabox' );
		add_meta_box( 'benchmark-email-lite', 'Benchmark Email Lite', $metabox_fn, 'post', 'side', 'default' );
		add_meta_box( 'benchmark-email-lite', 'Benchmark Email Lite', $metabox_fn, 'page', 'side', 'default' );
	}

	// Page+Post Metabox Contents
	static function metabox() {
		global $post;
		$localtime = current_time('timestamp');

		// Get Values For Form Prepopulations
		$user = wp_get_current_user();
		$email = isset( $user->user_email ) ? $user->user_email : get_bloginfo( 'admin_email' );
		$bmelist = ( $val = get_transient( 'bmelist' ) ) ? esc_attr( $val ) : '';
		$title = ( $val = get_transient( 'bmetitle' ) ) ? esc_attr( $val ) : date( 'M d Y', $localtime ) . ' Email';
		$from = ( $val = get_transient( 'bmefrom' ) ) ? esc_attr( $val ) : get_the_author_meta( 'display_name', get_current_user_id() );
		$subject = ( $val = get_transient( 'bmesubject' ) ) ? esc_attr( $val ) : '';
		$email = ( $val = get_transient( 'bmetestto' ) ) ? implode( ', ', $val ) : $email;

		// Open Benchmark Email Connection and Locate List
		$options = get_option('benchmark-email-lite_group');
		if( ! isset( $options[1][0] ) || ! $options[1][0] ) {
			$val = benchmarkemaillite_settings::badconfig_message();
			echo "<strong style='color:red;'>{$val}</strong>";
		} else {
			$dropdown = benchmarkemaillite_display::print_lists( $options[1], $bmelist );
		}

		// Round Time To Nearest Quarter Hours
		$minutes = date( 'i', $localtime );
		$newminutes = ceil( $minutes / 15 ) * 15;
		$localtime_quarterhour = $localtime + 60 * ( $newminutes - $minutes );

		// Get Timezone String
		$tz = ( $val = get_option( 'timezone_string' ) ) ? $val : 'UTC';
		$dateTime = new DateTime();
		$dateTime->setTimeZone( new DateTimeZone( $tz ) );
		$localtime_zone = $dateTime->format( 'T' );

		// Output Form
		require( dirname( __FILE__ ) . '/../views/metabox.html.php' );
	}

	// Called when Adding, Creating or Updating any Page+Post
	static function save_post( $postID ) {
		$options = get_option( 'benchmark-email-lite_group' );

		// Set Variables
		$bmelist = isset( $_POST['bmelist'] ) ? esc_attr( $_POST['bmelist'] ) : false;
		if( $bmelist ) {
			list(
				benchmarkemaillite_api::$token, $listname, benchmarkemaillite_api::$listid
			) = explode( '|', $bmelist );
		}
		$bmetitle = isset( $_POST['bmetitle'] ) ? stripslashes( strip_tags( $_POST['bmetitle'] ) ) : false;
		$bmefrom = isset( $_POST['bmefrom'] ) ? stripslashes( strip_tags( $_POST['bmefrom'] ) ) : false;
		$bmesubject = isset( $_POST['bmesubject'] ) ? stripslashes( strip_tags( $_POST['bmesubject'] ) ) : false;
		$bmeaction = isset( $_POST['bmeaction'] ) ? esc_attr( $_POST['bmeaction'] ) : false;
		$bmetestto = isset( $_POST['bmetestto'] ) ? explode( ',', $_POST['bmetestto'] ) : array();

		// Handle Prepopulation Loading
		set_transient( 'bmelist', $bmelist, 15 );
		set_transient( 'bmeaction', $bmeaction, 15 );
		set_transient( 'bmetitle', $bmetitle, 15 );
		set_transient( 'bmefrom', $bmefrom, 15 );
		set_transient( 'bmesubject', $bmesubject, 15 );
		set_transient( 'bmetestto', $bmetestto, 15 );

		// Don't Work With Post Revisions Or Other Post Actions
		if( wp_is_post_revision($postID) || !isset($_POST['bmesubmit']) || $_POST['bmesubmit'] != 'yes' ) { return; }

		// Get User Info
		if ( ! $user = wp_get_current_user() ) { return; }
		$user = get_userdata( $user->ID );
		$name = isset( $user->first_name ) ? $user->first_name : '';
		$name .= isset( $user->last_name ) ? ' ' . $user->last_name : '';
		$name = trim( $name );

		// Get Post Info
		if( ! $post = get_post( $postID ) ) { return; }

		// Prepare Campaign Data
		$tags = wp_get_post_tags( $postID );
		foreach( $tags as $i => $val ) {
			$tags[$i] = $val->slug;
		}
		$categories = wp_get_post_categories( $postID );
		foreach( $categories as $i => $val ) {
			$val = get_category( $val );
			$categories[$i] = $val->slug;
		}
		$data = array(
			'body' => apply_filters( 'the_content', $post->post_content ),
			'categories' => implode( ', ', $categories ),
			'excerpt' => $post->post_excerpt,
			'featured_image' => array(
				'full' => get_the_post_thumbnail( $postID, 'full' ),
				'large' => get_the_post_thumbnail( $postID, 'large' ),
				'medium' => get_the_post_thumbnail( $postID, 'medium' ),
				'thumbnail' => get_the_post_thumbnail( $postID, 'thumbnail' ),
			),
			'permalink' => get_permalink( $postID ),
			'tags' => implode( ', ', $tags ),
			'title' => $post->post_title,
		);
		$content = benchmarkemaillite_display::compile_email_theme( $data );
		$webpageVersion = ( $options[2] == 'yes' ) ? true : false;
		$permissionMessage = isset( $options[4] ) ? $options[4] : '';

		// Create Campaign
		$result = benchmarkemaillite_api::campaign(
			$bmetitle, $bmefrom, $bmesubject, $content, $webpageVersion, $permissionMessage
		);

		// Handle Error Condition: Preexists
		if( $result == __( 'preexists', 'benchmark-email-lite' ) ) {
			set_transient(
				'benchmark-email-lite_error',
				__( 'An email campaign by this name was previously sent and cannot be updated or sent again. Please choose another email name.', 'benchmark-email-lite' )
			);
			return;

		// Handle Error Condition: Other
		} else if( ! is_numeric( benchmarkemaillite_api::$campaignid ) ) {
			$error = isset( benchmarkemaillite_api::$campaignid['faultString'] ) ? benchmarkemaillite_api::$campaignid['faultCode'] : '';
			set_transient(
				'benchmark-email-lite_error',
				__( 'There was a problem creating or updating your email campaign. Please try again later.', 'benchmark-email-lite' )
				. ' ' . $error
			);
			return;
		}

		// Clear Fields After Successful Send
		if( in_array( $bmeaction, array( 2, 3 ) ) ) {
			delete_transient( 'bmelist' );
			delete_transient( 'bmeaction' );
			delete_transient( 'bmetitle' );
			delete_transient( 'bmefrom' );
			delete_transient( 'bmesubject' );
			delete_transient( 'bmetestto' );
			delete_transient( 'benchmarkemaillite_emails' );
		}

		// Schedule Campaign
		switch( $bmeaction ) {
			case '1':

				// Send Test Emails
				foreach( $bmetestto as $i => $bmetest ) {

					// Limit To 5 Recipients
					$overage = $i >= 5 ? true : false;
					if( $i >= 5 ) { continue; }

					// Send
					$bmetest = sanitize_email( trim( $bmetest ) );
					benchmarkemaillite_api::campaign_test( $bmetest );
				}

				// Report
				$overage = $overage ? __( 'Sending was capped at the first 5 test addresses.', 'benchmark-email-lite' ) : '';
				set_transient(
					'benchmark-email-lite_updated', sprintf(
						__( 'A test of your campaign %s was successfully sent.', 'benchmark-email-lite' ),
						"<em>{$bmetitle}</em>"
					) . $overage
				);
				break;

			case '2':

				// Send Campaign
				benchmarkemaillite_api::campaign_now();

				// Report
				set_transient(
					'benchmark-email-lite_updated', sprintf(
						__( 'Your campaign %s was successfully sent.', 'benchmark-email-lite' ),
						"<em>{$bmetitle}</em>"
					)
				);
				break;

			case '3':

				// Schedule Campaign For Sending
				$bmedate = isset( $_POST['bmedate'] ) ? esc_attr( $_POST['bmedate'] ) : date( 'd M Y', current_time( 'timestamp' ) );
				$bmetime = isset( $_POST['bmetime'] ) ? esc_attr( $_POST['bmetime'] ) : date( 'H:i', current_time( 'timestamp' ) );
				$when = "$bmedate $bmetime";
				benchmarkemaillite_api::campaign_later( $when );

				// Report
				set_transient(
					'benchmark-email-lite_updated', sprintf(
						__( 'Your campaign %s was successfully scheduled for %s.', 'benchmark-email-lite' ),
						"<em>{$bmetitle}</em>",
						"<em>{$when}</em>"
					)
				);
				break;
		}
	}
}

?>