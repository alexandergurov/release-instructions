<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/Authorname/release-instructions
 * @since      1.0.0
 *
 * @package    Release_Instructions
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_site_option('ri_executed');
