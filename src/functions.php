<?php

namespace Disguise;

add_action( 'init', function () {
	add_filter( 'http_request_args', 'Disguise\disguise_request_args', 10, 2 );

	// @TODO pass these in via settings
	if ( class_exists( '\BuddyBossApp\Build' ) && class_exists( '\BuddyBossApp\Admin\Build\Build' ) ) {
		remove_filter( 'admin_notices', [ \BuddyBossApp\Build::instance(), 'app_core_version_admin_notice' ] );
		remove_filter( 'admin_notices', [ \BuddyBossApp\Admin\Build\Build::instance(), 'admin_notices' ] );
	}
} );


function disguise_request_args( $args, $url ) {
	// @TODO add matched urls via settings
	// @TODO add support for partial matches

	$urls  = [
		'buddyboss' => [
			'https://jvqo6bncab.execute-api.us-east-2.amazonaws.com/v1/verify/',
			'https://update.buddyboss.com/theme',
			'https://update.buddyboss.com/plugin',
		]
	];
	$hosts = array(
		'local'    => str_replace( [ 'https://', 'http://' ], '', home_url() ),
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
function fix_hostnames(): \Closure {
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

