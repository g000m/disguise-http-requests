<?php

namespace Disguise;

add_action( 'init', function () {
	add_filter( 'http_request_args', 'Disguise\disguise_request_args', 10, 2 );

	// @TODO pass these in via settings
	if ( class_exists( 'BuddyBossApp\Build' ) && class_exists( 'BuddyBossApp\Admin\Build\Build' ) ) {
		remove_filter( 'admin_notices', [ \BuddyBossApp\Build::instance(), 'app_core_version_admin_notice' ] );
		remove_filter( 'admin_notices', [ \BuddyBossApp\Admin\Build\Build::instance(), 'admin_notices' ] );
	}
} );


function disguise_request_args( $args, $url ) {
	//bb-integration
	//page-family-enqueue
	//eh-mods
	//bbapp-page-html-shortcodes

	//accessally
	//accessally-user-migration
	//cf7-zendesk-pro
	//optimizePressPlugin
	//optimizePressExperiments
	//optimizePressHelperTools
	//optimizePressPlusPack
	//progressally
	//wpgdprPro_r4duTI

	// block
	// http://23.23.102.166/sl/public/api/ping

	// @TODO add matched urls via settings
	// @TODO add support for partial matches

	$urls  = [
		'https://jvqo6bncab.execute-api.us-east-2.amazonaws.com/v1/verify/',
		'https://update.buddyboss.com/theme',
		'https://update.buddyboss.com/plugin',
		'https://appcenter.buddyboss.com/wp-json/center/v1/update-app-info',
		'http://23.23.102.166/sl/public/api/*',
		'http://members.ambitionally.com/hosted_plugin/*'
	];
	$hosts = array(
		'local'    => str_replace( [ 'https://', 'http://' ], '', home_url() ),
		'licensed' => 'members.evolutionaryherbalism.com'
	);

	$args2 = $args;

	if ( matched_url( $url, $urls ) ) {
		array_walk_recursive( $args2, fix_hostnames(), $hosts );

		return $args2;
	}

	return $args;
}

/**
 * @param $url
 * @param array $urls
 *
 * @return bool
 */
function matched_url( $requested_url, array $urls ): bool {
	//quick check for URL in list
	if ( in_array( $requested_url, $urls ) ) {
		return true;
	}

	foreach ( $urls as $url ) {
		// if wildcard URL
		if ( substr( $url, - 1 ) === "*" ) {
			$url_search = substr_replace( $url, "", - 1 );
			if ( strpos( $requested_url, $url_search ) === 0 ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * @return Closure
 */
function fix_hostnames(): \Closure {
	return function ( &$value, $key, $host ) {
		if ( gettype( $host['local'] ) === 'string' && gettype( $value ) === 'string' && strpos( $value, $host['local'] ) !== false && $key !== "sslcertificates" && ! is_serialized( $value ) ) {
			$value = str_replace( $host['local'], $host['licensed'], $value );

		} elseif ( is_serialized( $value ) ) {
			$unserialized_value = unserialize( $value );
			// run this recursively
			array_walk_recursive( $unserialized_value, fix_hostnames(), $host );
			$value = serialize( $unserialized_value );
		}
	};
}

