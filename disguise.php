<?php
/**
 * Plugin Name:     Disguise Http Requests
 * Plugin URI:      https://github.com/g000m/disguise-http-requests
 * Description:     Disguise the origin of http requests. Designed to allow Satispress to retrieve updates in the name of a licensed host.
 * Author:          Gabe Herbert
 * Author URI:      https://gabeherbert.com
 * Text Domain:     disguise
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Disguise
 */

namespace Disguise;

// Exit if accessed directly.
if ( ! \defined( 'ABSPATH' ) ) {
	exit;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

// Display a notice and bail if dependencies are missing.
if ( ! function_exists( __NAMESPACE__ . '\autoloader_classmap' ) ) {
	require_once __DIR__ . '/src/functions.php';

	return;
}

