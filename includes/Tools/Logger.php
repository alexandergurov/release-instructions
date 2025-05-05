<?php

namespace ReleaseInstructions\Tools;

/**
 * The file that defines the logger class for core plugin class.
 *
 * @link  https://github.com/Authorname/release-instructions
 * @since 1.0.0
 *
 * @package    ReleaseInstructions
 * @subpackage ReleaseInstructions/Tools
 */

/**
 * Logger class.
 *
 * Used to define logger methods used by main plugin class.
 *
 * @since      1.0.0
 * @package    ReleaseInstructions
 * @subpackage ReleaseInstructions/Tools
 * @author     Firstname Lastname <mail@gmail.com>
 */
class Logger
{

    /**
     * Standard delimeter.
     *
     * @var string Defines default delimiter.
     *
     * @since  1.0.0
     * @access private
     */
    private static $delimiter = '##############################';

    /**
     * Delimiter string.
     *
     * @return string Returns default delimiter.
     *
     * @since 1.0.0
     */
    public static function getDelimiter(): string
    {
        return self::$delimiter;
    }

    /**
     * Defines the logger method to use and outputs messages.
     *
     * @param string $message Message text.
     * @param string $type    Message type.
     *
     * @since 1.0.0
     */
    public static function log(string $message, string $type)
    {
        if (Utils::isCLI()) {
            self::cli($message, $type);
            return;
        }

        if (\function_exists('esc_html')) {
            echo esc_html('<pre>' . ($type ? '[' . $type . ']: ' : '') . $message . '</pre>');
        }
    }

    /**
     * Using WP_CLI methods for output.
     *
     * @param string $message Message text.
     * @param string $type    Message type.
     *
     * @since  1.0.0
     * @access protected
     */
    protected static function cli(string $message, string $type)
    {
        /*
         * WP_CLI output methods.
         *
         * @see https://make.wordpress.org/cli/handbook/references/internal-api/#output
         */
        if (Utils::isCLI()) {
            switch ($type) {
                case 'success':
                    \WP_CLI::success($message);
                    break;

                case 'status':
                case 'found':
                case 'info':
                case 'x':
                case ' ':
                    \WP_CLI::log(($type ? '[' . $type . ']: ' : '') . $message);
                    break;

                case 'notice':
                case 'warning':
                    \WP_CLI::warning($message);
                    break;

                case 'error':
                    \WP_CLI::error($message);
                    break;

                default:
                    \WP_CLI::log($message);
            }
        }
    }
}
