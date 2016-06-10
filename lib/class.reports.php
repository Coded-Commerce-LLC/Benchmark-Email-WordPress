<?php

class benchmarkemaillite_reports {
	static $base_url = 'admin.php?page=benchmark-email-lite&amp;';

	// Generates Internal Data/URLs
	static function meta() {
		return (object) array(
			'campaign' => isset( $_GET['campaign'] ) ? intval( $_GET['campaign'] ) : '',
			'show' => isset( $_GET['show'] ) ? strtolower( esc_attr( $_GET['show'] ) ) : '',
		);
	}

	// Generates Internal URLs	
	static function url( $args=array() ) {
		$meta = self::meta();
		foreach( $args as $key => $val ) { $meta->$key = $val; }
		return self::$base_url . http_build_query( $meta );
	}

	// Page Controller
	static function show() {
		$options = get_option( 'benchmark-email-lite_group' );
		$meta = self::meta();

		// Showing Campaign Listings
		if( ! $meta->campaign ) { self::showListings(); return; }

		// Lookup Campaign Cache
		$data = get_transient( 'benchmarkemaillite_emails' );

		// Cache Expired
		if( ! $data ) { self::showListings(); return; }

		// Showing Campaign Specific Report
		foreach( $data as $key => $emails ) {
			if( in_array( $meta->campaign, $emails ) ) {

				// Set API To Selected Token
				benchmarkemaillite_api::$token = $key;

				// Show Detail Page
				if( $meta->show ) { self::showDetail( $meta->show ); }

				// Show Campaign Summary Page
				else { self::showCampaignSummary(); }
			}
		}
	}

	// Show Email Campaign Listings
	static function showListings() {
		$options = get_option( 'benchmark-email-lite_group' );
		$url = self::url();
		$flush = ( isset( $_REQUEST['flush'] ) && $_REQUEST['flush'] == '1' ) ? true : false;

		// Try To Load From Cache
		$data = get_transient( 'benchmarkemaillite_emails' );
		if( ! $data || $flush ) {

			// Loop API Tokens
			$data = array();
			foreach( $options[1] as $key ) {
				if( ! $key ) { continue; }
				$data[$key] = array();

				// Get Email Campaigns For Token
				benchmarkemaillite_api::$token = $key;
				$response = benchmarkemaillite_api::campaigns();

				// Skip If Errors
				if( ! $response || isset( $response['faultCode'] ) ) { continue; }

				// Loop Email Campaigns For Token
				foreach( $response as $email ) {

					// Append Data
					$email['toListName'] = isset( $email['toListName'] ) ? $email['toListName'] : '[none]';

					// Cache Email Specifics For 5 Minutes
					$data[$key][] = $email['id'];
					set_transient( "benchmarkemaillite_{$email['id']}", $email, 300 );
				}
			}

			// Cache Email List For 5 Minutes
			set_transient( 'benchmarkemaillite_emails', $data, 300 );
		}

		// Output
		require( dirname( __FILE__ ) . '/../views/reports.level1.html.php' );
	}

	// Show Email Campaign Summary
	static function showCampaignSummary() {
		$meta = self::meta();
		$url = self::$base_url . http_build_query( $meta );
		$flush = ( isset( $_REQUEST['flush'] ) && $_REQUEST['flush'] == '1' ) ? true : false;

		// Output Back Link
		echo '
			<p>
				<a href="' . self::$base_url . '" title="' . __( 'Back to Emails', 'benchmark-email-lite' ) . '">
					' . __( 'Back to Emails', 'benchmark-email-lite' ) . '
				</a>
			</p>
		';

		// Try To Use Cache
		$response = get_transient( "benchmarkemaillite_{$meta->campaign}" );
		if( ! isset( $response['unopens'] ) || $flush ) {

			// Get Campaign Stats
			$response = benchmarkemaillite_api::campaign_summary( $meta->campaign );
			$response['unopens'] = intval( $response['mailSent'] ) - intval( $response['opens'] ) - intval( $response['bounces'] );
			$response['clicks_percent'] = ( $response['opens'] ) ? 100 * $response['clicks'] / $response['opens'] : 0;

			// Cache Email Specifics For 5 Minutes
			set_transient( "benchmarkemaillite_{$meta->campaign}", $response, 300 );
		}

		// Output
		require( dirname( __FILE__ ) . '/../views/reports.level2.html.php' );
	}

	// Used For All Reports - Loops And Accumulates Page Content
	static function reportQueryAllPages() {
		$args = func_get_args();
		$data = array();
		$run = true;
		$page = 1;
		while( $run ) {
			$response = call_user_func_array( array( 'benchmarkemaillite_api', 'query' ), $args );
			if( ! is_array( $response ) ) { break; }
			$run = ( sizeof( $response ) == 100 ) ? true : false;
			$data = array_merge( $data, $response );
			foreach( $args as $key => $val ) {
				if( $val === $page ) {
					$args[$key]++;
					$page++;
				}
			}
		}
		return $data;
	}

	// Show Requested Detail Report
	static function showDetail( $show ) {
		$meta = self::meta();
		$data = array();
		switch ( $show ) {

			// Opens By Location Report
			case 'locations':
				$response = self::reportQueryAllPages(
					'reportGetOpenCountry', benchmarkemaillite_api::$token, (string) $meta->campaign
				);
				foreach( $response as $row ) {
					if( ! $row['openCount'] ) { continue; }
					$data[] = array(
						__( 'Country', 'benchmark-email-lite' ) => ucwords( strtolower( $row['country_name'] ) ),
						__( 'Opens', 'benchmark-email-lite' ) => $row['openCount'],
					);
				}
				benchmarkemaillite_display::maketable( $data );
				return;

			// Click Performance Report
			case 'clicks':
				$response = self::reportQueryAllPages( 'reportGetClicks', benchmarkemaillite_api::$token, (string) $meta->campaign );
				foreach ($response as $row) {
					$link = self::url( array( 'show' => 'clicks_detail', 'url' => urlencode( $row['URL'] ) ) );
					$data[] = array(
						__( 'URL', 'benchmark-email-lite' ) => "<a href='{$link}'>{$row['URL']}</a>",
						__( 'Clicks', 'benchmark-email-lite' ) => $row['clicks'],
						__( 'Percent', 'benchmark-email-lite' ) => $row['percent'] . '%',
					);
				}
				benchmarkemaillite_display::maketable( $data );
				return;

			// Click Performance Sub Reports
			case 'clicks_detail':
				$title = __( 'Link Clicked Detail Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays the subscribers who clicked on the following email link:', 'benchmark-email-lite' );
				$instructions .= '<br /><em>' . urldecode( $_GET['url'] ) . '</em>';
				$response = self::reportQueryAllPages( 'reportGetClickEmails', benchmarkemaillite_api::$token, (string) $meta->campaign, $_GET['url'], 1, 100, 'date', 'desc' );
				foreach( $response as $row ) {
					$data[] = array(
						__( 'Name', 'benchmark-email-lite' ) => $row['name'],
						__( 'Email', 'benchmark-email-lite' ) => $row['email'],
						__( 'Date', 'benchmark-email-lite' ) => $row['logdate'],
					);
				}
				break;

			// Click Performance Sub Reports
			case 'clicks_all':
				$title = __( 'Links Clicked Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays the subscribers who clicked on specific links in the email.', 'benchmark-email-lite' );
				$response = self::reportQueryAllPages( 'reportGetClicks', benchmarkemaillite_api::$token, (string) $meta->campaign );
				foreach ($response as $row) {
					$link = self::url( array( 'show' => 'clicks_detail', 'url' => urlencode( $row['URL'] ) ) );
					$response2 = self::reportQueryAllPages( 'reportGetClickEmails', benchmarkemaillite_api::$token, (string) $meta->campaign, $row['URL'], 1, 100, 'date', 'desc' );
					foreach( $response2 as $row2 ) {
						$data[] = array(
							__( 'URL', 'benchmark-email-lite' ) => "<a href='{$link}'>{$row['URL']}</a>",
							__( 'Email', 'benchmark-email-lite' ) => $row2['email'],
							__( 'Date', 'benchmark-email-lite' ) => $row2['logdate'],
						);
					}
				}
				break;

			// Email Opened Report
			case 'opens':
				$title = __( 'Emails Opened Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays the subscribers who opened the email in their email client.', 'benchmark-email-lite' );
				$response = self::reportQueryAllPages( 'reportGetOpens', benchmarkemaillite_api::$token, (string) $meta->campaign, 1, 100, 'date', 'desc' );
				foreach( $response as $row ) {
					$data[] = array(
						__( 'Name', 'benchmark-email-lite' ) => $row['name'],
						__( 'Email', 'benchmark-email-lite' ) => $row['email'],
						__( 'Date', 'benchmark-email-lite' ) => $row['logdate'],
					);
				}
				break;

			// Email Unopened Report
			case 'unopens':
				$title = __( 'Emails Unopened Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays the subscribers who never opened the email.', 'benchmark-email-lite' );
				$response = self::reportQueryAllPages( 'reportGetUnopens', benchmarkemaillite_api::$token, (string) $meta->campaign, 1, 100, 'date', 'desc' );
				foreach( $response as $row ) {
					$data[] = array(
						__( 'Name', 'benchmark-email-lite' ) => $row['name'],
						__( 'Email', 'benchmark-email-lite' ) => $row['email'],
					);
				}
				break;

			// Email Bounced Report
			case 'bounces':
				$title = __( 'Emails Bounced Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays the subscribers whose email service provider rejected the email.', 'benchmark-email-lite' );
				$response1 = self::reportQueryAllPages( 'reportGetHardBounces', benchmarkemaillite_api::$token, (string) $meta->campaign, 1, 100, 'date', 'desc' );
				$response2 = self::reportQueryAllPages( 'reportGetSoftBounces', benchmarkemaillite_api::$token, (string) $meta->campaign, 1, 100, 'date', 'desc' );
				$response = array_merge( $response1, $response2 );
				foreach( $response as $row ) {
					$data[] = array(
						__( 'Name', 'benchmark-email-lite' ) => $row['name'],
						__( 'Email', 'benchmark-email-lite' ) => $row['email'],
						__( 'Bounce Type', 'benchmark-email-lite' ) => $row['type'],
					);
				}
				break;

			// Email Unsubscribed Report
			case 'unsubscribes':
				$title = __( 'Emails Unsubscribed Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays previous subscribers who unsubscribed from the list during this campaign.', 'benchmark-email-lite' );
				$response = self::reportQueryAllPages( 'reportGetUnsubscribes', benchmarkemaillite_api::$token, (string) $meta->campaign, 1, 100, 'date', 'desc' );
				foreach( $response as $row ) {
					$data[] = array(
						__( 'Name', 'benchmark-email-lite' ) => $row['name'],
						__( 'Email', 'benchmark-email-lite' ) => $row['email'],
						__( 'Date', 'benchmark-email-lite' ) => $row['logdate'],
					);
				}
				break;

			// Emails Forwarded Report
			case 'forwards':
				$title = __( 'Emails Forwarded Report', 'benchmark-email-lite' );
				$instructions = __( 'Displays the subscribers who successfully forwarded the email to others.', 'benchmark-email-lite' );
				$response = self::reportQueryAllPages( 'reportGetForwards', benchmarkemaillite_api::$token, (string) $meta->campaign, 1, 100, 'date', 'desc' );
				foreach( $response as $row ) {
					$data[] = array(
						__( 'Name', 'benchmark-email-lite' ) => $row['name'],
						__( 'Email', 'benchmark-email-lite' ) => $row['email'],
						__( 'Date', 'benchmark-email-lite' ) => $row['logdate'],
					);
				}
				break;
		}

		// Output Requested Report
		$url = self::url( array( 'show' => '' ) );
		$response = get_transient( "benchmarkemaillite_{$meta->campaign}" );
		require( dirname( __FILE__ ) . '/../views/reports.level3.html.php' );
	}
}

?>