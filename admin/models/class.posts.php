<?php

class benchmarkemaillite_posts {

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
			$dropdown = benchmarkemaillite_api::print_lists( $options[1], $bmelist );
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
		require( BMEL_DIR_PATH . 'views/metabox.html.php' );
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
		$content = self::compile_email_theme( $data );
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

	/*
	Formats Email Body Into Email Template
	This Can Be Customized EXTERNALLY Using This Approach:
	add_filter( 'benchmarkemaillite_compile_email_theme', 'my_custom_function', 10, 1 );
	*/
	static function compile_email_theme( $data ) {
		$options = get_option( 'benchmark-email-lite_group_template' );

		// Priority 1: Uses Child Plugin Customizations
		if( has_filter( 'benchmarkemaillite_compile_email_theme' ) ) {
			return apply_filters( 'benchmarkemaillite_compile_email_theme', $data );
		}

		// Priority 2: Uses Stored HTML
		if ( isset( $options['html'] ) && $output = $options['html'] ) {
			$admin_email = md5( strtolower( get_option( 'admin_email' ) ) );
			$output = str_replace( 'BODY_HERE', $data['body'], $output );
			$output = str_replace( 'CATEGORIES', $data['categories'], $output );
			$output = str_replace( 'EMAIL_MD5_HERE', $admin_email, $output );
			$output = str_replace( 'EXCERPT', $data['excerpt'], $output );
			$output = str_replace( 'FEATURED_IMAGE_FULL', $data['featured_image']['full'], $output );
			$output = str_replace( 'FEATURED_IMAGE_LARGE', $data['featured_image']['large'], $output );
			$output = str_replace( 'FEATURED_IMAGE_MEDIUM', $data['featured_image']['medium'], $output );
			$output = str_replace( 'FEATURED_IMAGE_THUMBNAIL', $data['featured_image']['thumbnail'], $output );
			$output = str_replace( 'PERMALINK', $data['permalink'], $output );
			$output = str_replace( 'TAGS', $data['tags'], $output );
			$output = str_replace( 'TITLE_HERE', $data['title'], $output );
			return self::normalize_html( $output );
		}

		// Priority 3: Uses Template File
		ob_start();
		require( BMEL_DIR_URL . 'assets/email_templates/simple.html' );
		$output = ob_get_contents();
		ob_end_clean();
		return self::normalize_html( $output );
	}

	// Convert WP Core CSS To Embedded
	static function normalize_html( $html ) {

		// Proceed Only When Possible
		if( ! class_exists( 'DOMDocument' ) ) { return $html; }

		// Rules To Apply
		$rules = array(
			'alignnone' => 'margin: 5px 20px 20px 0; ',
			'aligncenter' => 'display: block; margin: 5px auto 5px auto; ',
			'alignright' => 'float: right; margin: 5px 0 20px 20px; ',
			'alignleft' => 'float: left; margin: 5px 20px 20px 0; ',
			'wp-caption' => 'background: #fff; border: 1px solid #f0f0f0; max-width: 96%; padding: 5px 3px 10px; text-align: center; ',
			'wp-caption-text' => 'font-size: 11px; line-height: 17px; margin:0; padding: 0 4px 5px; ',
			//'wp-caption img' => 'border: 0 none; height: auto; margin: 0; max-width: 98.5%; padding: 0; width: auto;',
		);

		// Tags To Process
		$searchtags = array( 'p', 'span', 'img', 'div', 'h1', 'h2', 'h3', 'h4' );

		// Suppress PHP Warnings
		libxml_use_internal_errors( true );

		// Open HTML
		$doc = @DOMDocument::loadHTML( $html );

		// Loop Tags
		foreach( $searchtags as $tag ) {

			// Search For Matches
			$foundtags = $doc->getElementsByTagName( $tag );
			if( ! $foundtags ) { continue; }

			// Loop Matching Tags
			foreach( $foundtags as $para ) {

				// Search For Classes
				$classes = array();
				if( $para->hasAttribute( 'class' ) ) {
					$classes = $para->getAttribute( 'class' );
					$para->removeAttribute( 'class' );
					$classes = explode( ' ', $classes );
				}

				// Preserve Any Existing Styles
				$style = '';
				if( $para->hasAttribute( 'style' ) ) {
					$style = trim( $para->getAttribute( 'style' ) );
					if( ! strchr( $style, ';' ) ) { $style .= ';'; }
					$style .= ' ';
				}

				// Loop Classes
				foreach( $classes as $class ) {

					// Skip Non Conversion Classes
					if( ! in_array( $class, array_keys( $rules ) ) ) { continue; }

					// Accumulate Styling Rules To Apply
					$style .= $rules[$class];
				}

				// Store Rules Into Tag
				if( $style ) { $para->setAttribute( 'style', $style ); }
			}
		}

		// Assemble HTML
		$newdoc = $doc->saveHTML();

		// Handle Errors
		$errors = libxml_get_errors();

		// Output
		return $newdoc;
	}
}
