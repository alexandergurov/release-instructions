<?php

namespace ReleaseInstructions\Command;

/*
 * The file that defines the core command class for plugin.
 *
 * @link  https://github.com/Authorname/release-instructions
 * @since 1.0.0
 *
 * @package    ReleaseInstructions
 * @subpackage ReleaseInstructions/Command
 */

use ReleaseInstructions\Tools\Logger;
use ReleaseInstructions\Tools\Utils;

/**
 * Utility class.
 *
 * Used to define core command methods to extend child command methods. Example: CLI_Command
 *
 * @since      1.0.0
 * @package    ReleaseInstructions
 * @subpackage ReleaseInstructions/Command
 * @author     Firstname Lastname <mail@gmail.com>
 */
class CoreCommand implements CommandInterface
{

    /**
     * The logger which is responsible for logging all actions and events.
     *
     * @var Logger $logger Logs all actions for this plugin.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $logger;

    /**
     * Core_Command constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->logger = new Logger();
    }

    /**
     * Interlayer for a logger.
     *
     * @param  string $message Message text.
     * @param  string $status  Status text.
     * @return $this
     *
     * @since 1.0.0
     */
    public function log(string $message = '', string $status = ''): CoreCommand
    {
        $this->logger::log($message, $status);
        return $this;
    }

    /**
     * List all plugins with defined RI support.
     *
     * @return array Plugin list.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function getPlugins(): array
    {
        return array_filter(
            Utils::getPlugins(),
            static function ($plugin) {
                return $plugin['RI'];
            }
        );
    }

    /**
     * List all plugins and included release instructions.
     *
     * @return array Plugin + files list.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function getFiles(): array
    {
        $ri_files = [];
        if (!\function_exists('plugin_dir_path') || !\defined('WP_PLUGIN_DIR')) {
            return $ri_files;
        }

        foreach ($this->getPlugins() as $plugin_key => $plugin) {
            $dir = WP_PLUGIN_DIR . '/' . plugin_dir_path($plugin_key) . 'ri';
            if (is_dir($dir)) {
                if ($files = glob($dir . '/*[.ri.inc]')) {
                    foreach ($files as $file) {
                        $ri_files[] = [
                            'plugin' => $plugin_key,
                            'name'   => $file,
                        ];
                    }
                }
            }
        }

        return $ri_files;
    }

    /**
     * Returns the list of all the RIs available.
     *
     * @param  boolean $exclude_executed Self-describing: excludes executed RIs.
     * @return array Returns assoc array, with a list of RI-supported plugins, with all RIs attached.
     *
     * @since  1.0.0
     * @access protected
     */
    protected function getUpdates($exclude_executed = true): array
    {
        $updates = [];
        foreach ($this->getFiles() as $file) {
            // Load file.
            Utils::fileInclude($file['name']);

            $plugin = $this->getPlugins()[$file['plugin']];
            // Get function names prefix.
            $separator     = '_';
            $function_name = trim(preg_replace('@[^a-z0-9_]+@', $separator, strtolower($plugin['Name'])), $separator);

            // Prepare regular expression to match all possible defined hook_update_N().
            $regexp    = '/^.*' . $function_name . '_ri_(?P<version>\d+)$/';
            $functions = get_defined_functions();
            /*
             * Narrow this down to functions ending with an integer, since all
             * hook_ri_N() functions end this way, and there are other
             * possible functions which match '_ri_'. We use preg_grep() here
             * instead of foreaching through all defined functions, since the loop
             * through all PHP functions can take significant page execution time.
             */
            foreach (preg_grep('/_\d+$/', $functions['user']) as $function) {
                // If this function is a module update function, add it to the list of module updates.
                if (preg_match($regexp, $function, $matches)) {
                    $updates[$file['plugin']][$function] = $matches['version'];
                }
            }

            // Ensure that updates are applied in numerical order.
            foreach ($updates as &$plugin_updates) {
                ksort($plugin_updates);
            }

            unset($plugin_updates);
        }

        // Exclude.
        if ($exclude_executed) {
            foreach ($updates as $plugin => $functions) {
                foreach ($functions as $function => $version) {
                    if ($this->getStatus($function)) {
                        unset($updates[$plugin][$function]);
                    }
                }
            }
        }

        return $updates;
    }

    /**
     * Runs RI. Updates RI status.
     *
     * @param  string $function Function name.
     * @return $this
     *
     * @since  1.0.0
     * @access protected
     */
    protected function functionExecute($function = ''): CoreCommand
    {
        // Message.
        $this->log($this->logger::getDelimiter() . "\n")->log('Running ' . $function . '()' . "\n");

        // Execute.
        $is_executed = false;
        if (function_exists($function)) {
            if (!($message = $function())) {
                $message = 'Release instruction ' . $function . '() was executed.';
            }

            // Mark as executed.
            $is_executed = $this->setStatus($function);
        } else {
            $message = 'Release instruction ' . $function . '() does not exist.';
        }

        // Message.
        return $this->log("\n")->log($message, $is_executed ? 'status' : 'notice')->log(
            $this->logger::getDelimiter() . "\n\n"
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $function = ''): CoreCommand
    {
        // Preload the files.
        $updates = $this->getUpdates(false);

        // Function - direct matching case.
        if (false === strpos($function, '*')) {
            $this->functionExecute($function);
        } else {
            $pattern = '@^' . str_replace('*', '.*', $function) . '$@';

            // Now run the updates.
            foreach ($updates as $plugin => $functions) {
                foreach ($functions as $_function => $version) {
                    if (preg_match($pattern, $_function)) {
                        $this->functionExecute($_function);
                    }
                }
            }
        }

        // Message.
        return $this->log('Release instruction execution is finished.', 'success');
    }

    /**
     * {@inheritdoc}
     */
    public function executeAll(): CoreCommand
    {
        // Retrieve all RIs.
        $updates = $this->getUpdates(false);

        // Now run the updates.
        foreach ($updates as $plugin => $functions) {
            foreach ($functions as $function => $version) {
                // Skip if already executed.
                if ($this->getStatus($function)) {
                    continue;
                }

                $this->functionExecute($function);
            }
        }

        // Message.
        return $this->log('Release instructions were executed.', 'success');
    }

    /**
     * {@inheritdoc}
     */
    public function preview(bool $all = false): CoreCommand
    {
        // Message.
        $message = $all ? 'List of all release instructions:' : 'Release instructions to be executed (in order):';
        $this->log($message . "\n");

        $count            = 0;
        $scheduled_exists = false;
        foreach ($this->getUpdates($all ? false : true) as $plugin => $functions) {
            foreach ($functions as $function => $version) {
                $message = $function . '()' . "\n";
                if (!($is_executed = $this->getStatus($function))) {
                    $scheduled_exists = true;
                }

                $status_mark = $is_executed ? 'x' : ' ';
                $status      = $all ? $status_mark : '';

                // Message.
                $this->log($message, $status);
                $count++;
            }
        }

        // Notice.
        if (!$count || !$scheduled_exists) {
            $this->log('Nothing to execute.' . "\n", 'notice');
        }

        // Message.
        return $this->log('End of list.' . "\n");
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(string $function = '')
    {
        $ri_executed = Utils::getOption('ri_executed', []);
        return $function ? !empty($ri_executed[$function]) : $ri_executed;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(string $function = '', bool $flag = true): bool
    {
        $ri_executed            = $this->getStatus();
        $ri_executed[$function] = $flag;
        return Utils::setOption('ri_executed', $ri_executed);
    }
}
