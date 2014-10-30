<?php
/*
 * Plugin Name: Power of Moms Programs
 * Version: 2.0
 * Plugin URI: https://github.com/jloosli/pom-programs/
 * Description: Power of Moms Programs
 * Author: Jared Loosli
 * Author URI: https://github.com/jloosli/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: power-of-moms-programs
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jared Loosli
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-power-of-moms-programs.php' );
require_once( 'includes/class-power-of-moms-programs-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-power-of-moms-programs-admin-api.php' );
require_once( 'includes/lib/class-power-of-moms-programs-post-type.php' );
require_once( 'includes/lib/class-power-of-moms-programs-taxonomy.php' );

/**
 * Returns the main instance of Power_of_Moms_Programs to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Power_of_Moms_Programs
 */
function Power_of_Moms_Programs () {
	$instance = Power_of_Moms_Programs::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Power_of_Moms_Programs_Settings::instance( $instance );
	}

	return $instance;
}

Power_of_Moms_Programs();