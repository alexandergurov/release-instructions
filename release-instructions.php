<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Authorname/release-instructions
 * @since             1.0.0
 * @package           Release_Instructions
 *
 * @wordpress-plugin
 * Plugin Name:       Release Instructions
 * Plugin URI:        https://github.com/Authorname/release-instructions
 * Description:       Run custom code per deployment/release.
 * Version:           1.0.6
 * Author:            Firstname Lastname (mail@gmail.com)
 * Author URI:        https://github.com/Authorname
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       release-instructions
 * Domain Path:       /languages
 * Network:           false
 * RI:                true
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('RELEASE_INSTRUCTIONS_VERSION', '1.0.6');
define('RELEASE_INSTRUCTIONS_FILE', __FILE__);

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
if (function_exists('plugin_dir_path')) {
    require plugin_dir_path(__FILE__) . 'vendor/autoload.php';
}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
(new ReleaseInstructions\ReleaseInstructions())->run();
