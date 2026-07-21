<?php
/**
 * AJAX Drop-in to track ads.
 *
 * @package AdvancedAds\Tracking
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.x.x
 *
 * This method speeds up ad tracking by a factor of 100 compared to wp-admin/admin-ajax.php
 * If you wish not to use this tracking method, please set the constant ADVANCED_ADS_TRACKING_LEGACY_AJAX,
 * i.e. define( 'ADVANCED_ADS_TRACKING_LEGACY_AJAX', true ) in your wp-config.php
 */

// 10: autoloader file path
require_once '%11$s';

use AdvancedAds\Tracking\Constants;
use AdvancedAds\Tracking\Debugger;

// phpcs:disable WordPress.DB.RestrictedFunctions
// phpcs:disable WordPress.Security.NonceVerification
// phpcs:disable WordPress.DateTime.RestrictedFunctions
// phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged

$start_time = microtime( true );

// Set some headers to avoid caching.
$headers = [
	'X-Content-Type-Options: nosniff',
	'Cache-Control: no-cache, must-revalidate, max-age=0, smax-age=0',
	'Expires: Sat, 26 Jul 1997 05:00:00 GMT',
	'X-Accel-Expires: 0',
	'X-Robots-Tag: noindex',
];

foreach ( $headers as $header ) {
	@header( $header );
}

header_remove( 'Last-Modified' );

// Ensure headers are send.
flush();

// Do not stop when user ended the connection.
@ignore_user_abort( true );

$data = strtolower( $_SERVER['REQUEST_METHOD'] ) === 'get' ? $_GET : $_POST; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

if ( empty( $data ) ) {
	$data = json_decode( file_get_contents( 'php://input' ), true );
	if ( is_array( $data ) ) {
		// Add data back to global request vars.
		$_POST    = array_merge( $_POST, $data );
		$_REQUEST = array_merge( $_REQUEST, $data );
	}
}

if ( empty( $data['ads'] ) || ! is_array( $data['ads'] ) ) {
	die( 'no ads' );
}

if ( empty( $data['action'] ) || ! in_array( $data['action'], [ Constants::TRACK_IMPRESSION, Constants::TRACK_CLICK ], true ) ) {
	die( 'nothing to do' );
}

// 12: regex string to match bots. Empty if tracking bots.
$bots = '%12$s';
if ( ! empty( $bots ) ) {
	// Check user agent if is bot.
	$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? stripslashes( $_SERVER['HTTP_USER_AGENT'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	if ( empty( $user_agent ) ) {
		die( 'not tracking bots' );
	}

	// Create regex as variable.
	if ( preg_match( '/' . $bots . '/i', $user_agent ) ) {
		die( 'not tracking bots' );
	}
}

@date_default_timezone_set( 'UTC' );

// open db connection
// 1: host, 2: user, 3: password, 4: db name, 5: port, 6: socket.
$mysqli = @mysqli_connect( '%1$s', '%2$s', '%3$s', '%4$s', '%5$d', '%6$s' );
if ( ! $mysqli ) {
	die( 'Could not connect to database' );
}

// 7: table prefix.
$prefix = $data['bid'] > 1 ? '%7$s' . $data['bid'] . '_' : '%7$s';
$table  = 'aatrack-records' === $data['action']
	? 'advads_impressions' : 'advads_clicks';

$ads = array_count_values(
	array_filter(
		array_map(
			function ( $value ) {
				return (int) $value;
			},
			$data['ads']
		)
	)
);

foreach ( $ads as $ad_id => $count ) {
	$error_msg = adt_track( $ad_id, $mysqli, $prefix, $table, $count );

	// 9: debugging active, 10: ad_id to debug.
	if ( '%9$s' === 'true' || (int) '%10$d' === $ad_id ) {
		while ( $count-- ) {
			Debugger::log(
				(int) $ad_id,
				$prefix . $table,
				empty( $error_msg ) ? round( ( microtime( true ) - $start_time ) * 1000 ) : -1,
				'Frontend on AMP' === $data['handler'] ? 'Frontend on AMP' : 'Frontend',
				$error_msg,
				'%8$s' // 8: debug file.
			);
		}
	}
}

mysqli_close( $mysqli );

/**
 * Write impression to database.
 *
 * @param int    $ad_id  The ID of the ad to track.
 * @param mysqli $mysqli DB instance.
 * @param string $prefix Table prefix to track into.
 * @param string $table  Table to track click|impression.
 * @param int    $count  Number of impressions.
 *
 * @return string Error message, or empty string on success.
 */
function adt_track( $ad_id, $mysqli, $prefix, $table, $count ) {
	$ts    = advads_timestamp();
	$count = (int) $count;
	// 7: table prefix.
	$success = mysqli_query( $mysqli, "INSERT INTO `{$prefix}{$table}` (`ad_id`, `timestamp`, `count`) VALUES ({$ad_id}, {$ts}, {$count}) ON DUPLICATE KEY UPDATE `count` = `count`+ {$count}" );

	if ( $success ) {
		return '';
	}

	return mysqli_error( $mysqli );
}

/**
 * Get timestamp string; only do this once per request.
 *
 * @return string
 */
function advads_timestamp() {
	static $ts;
	if ( ! is_null( $ts ) ) {
		return $ts;
	}
	$timezone = '%13$s';
	if ( preg_match( '/^\d/', $timezone ) ) {
		$timezone = '+' . $timezone;
	}
	$time = new DateTime( 'now', new DateTimeZone( $timezone ) );
	// Default timestamp.
	$ts = $time->format( 'ymWd06' );

	// Check for week/month inconsistencies.
	$week  = abs( $time->format( 'W' ) );
	$month = abs( $time->format( 'm' ) );

	if ( 52 <= $week && 1 === $month ) {
		// Still week 52 but already in January.
		$ts = $time->format( 'ym01d06' );
	} elseif ( 12 === $month && $week > 52 ) {
		// Still in December but week 53.
		$ts = $time->format( 'ym52d06' );
	}

	return $ts;
}

die( 'ok' );
