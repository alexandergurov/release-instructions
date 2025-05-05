<?php

namespace ReleaseInstructions;

/**
 * Register all actions and filters for the plugin.
 *
 * @link  https://github.com/Authorname/release-instructions
 * @since 1.0.0
 *
 * @package ReleaseInstructions
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @since   1.0.0
 * @package ReleaseInstructions
 * @author  Firstname Lastname <mail@gmail.com>
 */
class Loader
{

    /**
     * The array of actions registered with WordPress.
     *
     * @var array $actions The actions registered with WordPress to fire when the plugin loads.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $actions;

    /**
     * The array of filters registered with WordPress.
     *
     * @var array $filters The filters registered with WordPress to fire when the plugin loads.
     *
     * @since  1.0.0
     * @access protected
     */
    protected $filters;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->actions = [];
        $this->filters = [];
    }

    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @param string  $hook          The name of the WordPress action that is being registered.
     * @param object  $component     A reference to the instance of the object on which the action is defined.
     * @param string  $callback      The name of the function definition on the $component.
     * @param integer $priority      Optional. The priority at which the function should be fired. Default is 10.
     * @param integer $accepted_args Optional. The number of arguments that should be passed to the $callback.
     * Default is 1.
     *
     * @since 1.0.0
     */
    public function addAction(
        string $hook,
        object $component,
        string $callback,
        int $priority = 10,
        int $accepted_args = 1
    ): void {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @param string  $hook          The name of the WordPress filter that is being registered.
     * @param object  $component     A reference to the instance of the object on which the filter is defined.
     * @param string  $callback      The name of the function definition on the $component.
     * @param integer $priority      Optional. The priority at which the function should be fired. Default is 10.
     * @param integer $accepted_args Optional. The number of arguments that should be passed to the $callback.
     * Default is 1.
     *
     * @since 1.0.0
     */
    public function addFilter(
        string $hook,
        object $component,
        string $callback,
        int $priority = 10,
        int $accepted_args = 1
    ): void {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }

    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @param  array   $hooks         The collection of hooks that is being registered (that is, actions or filters).
     * @param  string  $hook          The name of the WordPress filter that is being registered.
     * @param  object  $component     A reference to the instance of the object on which the filter is defined.
     * @param  string  $callback      The name of the function definition on the $component.
     * @param  integer $priority      The priority at which the function should be fired.
     * @param  integer $accepted_args The number of arguments that should be passed to the $callback.
     * @return array The collection of actions and filters registered with WordPress.
     *
     * @since  1.0.0
     * @access private
     */
    private function add(
        array $hooks,
        string $hook,
        object $component,
        string $callback,
        int $priority,
        int $accepted_args
    ): array {
        $hooks[] = [
            'hook'          => $hook,
            'component'     => $component,
            'callback'      => $callback,
            'priority'      => $priority,
            'accepted_args' => $accepted_args,
        ];

        return $hooks;
    }

    /**
     * Register the filters and actions with WordPress.
     *
     * @since 1.0.0
     */
    public function run(): void
    {
        foreach ($this->filters as $hook) {
            if (\function_exists('add_filter')) {
                add_filter(
                    $hook['hook'],
                    [
                        $hook['component'],
                        $hook['callback'],
                    ],
                    $hook['priority'],
                    $hook['accepted_args']
                );
            }
        }

        foreach ($this->actions as $hook) {
            if (\function_exists('add_action')) {
                add_action(
                    $hook['hook'],
                    [
                        $hook['component'],
                        $hook['callback'],
                    ],
                    $hook['priority'],
                    $hook['accepted_args']
                );
            }
        }
    }
}
