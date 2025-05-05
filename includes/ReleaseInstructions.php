<?php

namespace ReleaseInstructions;

/*
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link  https://github.com/Authorname/release-instructions
 * @since 1.0.0
 *
 * @package ReleaseInstructions
 */

use ReleaseInstructions\Tools\Utils;
use ReleaseInstructions\Command\CoreCommand;

/**
 * The core plugin class.
 *
 * This is used to define plugin functionality methods,
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since   1.0.0
 * @package ReleaseInstructions
 * @author  Firstname Lastname <mail@gmail.com>
 */
class ReleaseInstructions
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var Loader $loader Maintains and registers all hooks for the plugin.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @var string $release_instructions The string used to uniquely identify this plugin.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $release_instructions;

    /**
     * The current version of the plugin.
     *
     * @var string $version The current version of the plugin.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $version;

    /**
     * The core functionality of the plugin.
     *
     * @var CoreCommand $core Core functionality.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $ri;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the release instructions and the plugin version that can be used throughout the plugin.
     * Load the dependencies, utilities and logger.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->version              = \defined('RELEASE_INSTRUCTIONS_VERSION') ? RELEASE_INSTRUCTIONS_VERSION : '1.0.2';
        $this->release_instructions = 'release-instructions';
        $this->loadDependencies();
        $this->ri = new CoreCommand();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Loader. Orchestrates the hooks of the plugin.
     * - Utils. Adds helper functions.
     * - Logger. Logs all actions and events.
     * - Core_Command. Defines all core commands.
     * - CLI_Command. Defines all cli commands.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since  1.0.0
     * @access private
     */
    private function loadDependencies(): ReleaseInstructions
    {
        /*
         * The class responsible for defining command line commands.
         */
        if (Utils::isCLI() && \function_exists('plugin_dir_path')) {
            include_once plugin_dir_path(__DIR__) . 'includes/Command/CLICommand.php';
        }

        $this->loader = new Loader();
        if (\defined('RELEASE_INSTRUCTIONS_FILE')) {
            $file = plugin_basename(RELEASE_INSTRUCTIONS_FILE);
            $this->loader->addAction('activate_' . $file, $this, 'activate');
            $this->loader->addAction('deactivate_' . $file, $this, 'deactivate');
        }

        $this->loader->addFilter('extra_plugin_headers', $this, 'addRiHeader');

        return $this;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since 1.0.0
     */
    public function run(): ReleaseInstructions
    {
        $this->loader->run();

        return $this;
    }

    /**
     * Extends plugin headers to support Release Instructions plugin.
     */
    public function addRiHeader(): array
    {
        return ['RI' => 'RI'];
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return string The name of the plugin.
     *
     * @since 1.0.0
     */
    public function getReleaseInstructions(): string
    {
        return $this->release_instructions;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return Loader Orchestrates the hooks of the plugin.
     *
     * @since 1.0.0
     */
    public function getLoader(): Loader
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return string The version number of the plugin.
     *
     * @since 1.0.0
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Retrieve core functionality.
     *
     * @return CoreCommand Core functionality.
     *
     * @since 1.0.0
     */
    public function getRi(): CoreCommand
    {
        return $this->ri;
    }

    /**
     * Plugin cleanup.
     *
     * @since 1.0.2
     */
    public function deactivate(): void
    {
        if (
            Utils::isMultisite()
            && class_exists('WP_Site_Query') && \function_exists('get_sites')
            && \function_exists('delete_blog_option')
        ) {
            $sites = get_sites();
            foreach ($sites as $site) {
                delete_blog_option($site->blog_id, 'ri_executed');
            }
        } elseif (\function_exists('delete_site_option')) {
            delete_site_option('ri_executed');
        }
    }

    /**
     * Plugin preparation.
     *
     * @since 1.0.2
     */
    public function activate(): void
    {
        if (
            Utils::isMultisite()
            && class_exists('WP_Site_Query') && \function_exists('get_sites')
            && \function_exists('add_blog_option')
        ) {
            $sites = get_sites();
            foreach ($sites as $site) {
                add_blog_option($site->blog_id, 'ri_executed', []);
            }
        } elseif (\function_exists('add_site_option')) {
            add_site_option('ri_executed', []);
        }
    }
}
