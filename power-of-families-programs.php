<?php
/*
 * Plugin Name: Power of Families Programs
 * Version: 3.0
 * Plugin URI: https://github.com/jloosli/pof-programs/
 * Description: Power of Families Programs
 * Author: Jared Loosli
 * Author URI: https://github.com/jloosli/
 * Requires at least: 4.8
 * Tested up to: 4.8
 *
 * Text Domain: power-of-moms-programs
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jared Loosli
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

// Autoload classes
require_once('lib/Autoloader.class.php');
$Autoloader = new Autoloader();

// Load Main Programs
$PowerOfFamiliesPrograms = new \POF\Power_of_Families_Programs(trailingslashit(plugin_dir_path(__FILE__)));


//// Load plugin class files
//require_once('includes/Power_of_Families_Programs.class.php');
//require_once('includes/Settings.class.php');
//
//// Load plugin libraries
//require_once('includes/lib/Admin_API.class.php');
//require_once('includes/lib/Post_Type.class.php');
//require_once('includes/lib/Taxonomy.php');

/**
 * Returns the main instance of Power_of_Moms_Programs to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Power_of_Moms_Programs
 */
//function PowerOfFamiliesPrograms()
//{
//    $version = POF_getVersion();
//    $instance = \POF\Power_of_Families_Programs::instance(__FILE__, $version);
//
//    if (is_null($instance->settings)) {
//        $instance->settings = \POF\Settings::instance($instance);
//    }
//
//    return $instance;
//}
//
//
//
//PowerOfFamiliesPrograms();