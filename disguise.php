<?php
/**
 * Plugin Name:     Disguise
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Disguise the origin of http requests
 * Author:          Gabe Herbert
 * Author URI:      https://gabeherbert.com
 * Text Domain:     disguise
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Disguise
 */

use BuddyBossApp\Build;
use BuddyBossApp\Admin\Build\Build as AdminBuild;

add_action( 'init', function () {
	add_filter( 'http_request_args', 'filter_request_args', 10, 2 );

	remove_filter( 'admin_notices', [ Build::instance(), 'app_core_version_admin_notice' ] );
	remove_filter( 'admin_notices', [ AdminBuild::instance(), 'admin_notices' ] );
} );


function filter_request_args( $args, $url ) {
	$urls  = [
		'buddyboss' => [
			'https://jvqo6bncab.execute-api.us-east-2.amazonaws.com/v1/verify/',
			'https://update.buddyboss.com/theme',
			'https://update.buddyboss.com/plugin',
		]
	];
	$hosts = array( 'local'    => str_replace( [ 'https://', 'http://' ], '', home_url() ),
	                'licensed' => 'members.evolutionaryherbalism.com'
	);

	$args2 = $args;

	if ( in_array( $url, $urls['buddyboss'] ) ) {
		array_walk_recursive( $args2, fix_hostnames(), $hosts );

		return $args2;
	}

	return $args;
}

/**
 * @return Closure
 */
function fix_hostnames(): Closure {
	return function ( &$value, $key, $host ) {

		if ( strpos( $value, $host['local'] ) !== false && $key !== "sslcertificates" && ! is_serialized( $value ) ) {
			$value = str_replace( $host['local'], $host['licensed'], $value );
		} elseif ( is_serialized( $value ) ) {
			$unserialized_value = unserialize( $value );
			// run this recursively
			array_walk_recursive( $unserialized_value, fix_hostnames(), $host );
			$value = serialize( $unserialized_value );
		}
	};
}

