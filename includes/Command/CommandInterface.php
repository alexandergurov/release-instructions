<?php

namespace ReleaseInstructions\Command;

/**
 * The file that defines the core command interface.
 *
 * @link  https://github.com/Authorname/release-instructions
 * @since 1.0.0
 *
 * @package    ReleaseInstructions
 * @subpackage ReleaseInstructions/Command
 */

/**
 * Core interface.
 *
 * Used to define RI plugin actions.
 *
 * @since      1.0.0
 * @package    ReleaseInstructions
 * @subpackage ReleaseInstructions/Command
 * @author     Firstname Lastname <mail@gmail.com>
 */
interface CommandInterface
{


    /**
     * Runs a single release instruction.
     *
     * @param  string $function Function name.
     * @return self
     *
     * @since 1.0.0
     */
    public function execute(string $function = ''): CoreCommand;


    /**
     * Executes the release instructions.
     *
     * @return self
     *
     * @since 1.0.0
     */
    public function executeAll(): CoreCommand;


    /**
     * Shows list of release instructions.
     *
     * @param  boolean $all Includes executed in a list.
     * @return self
     *
     * @since 1.0.0
     */
    public function preview(bool $all = false): CoreCommand;


    /**
     * Returns release instruction status(-es).
     *
     * @param  string $function Function name.
     * @return boolean|mixed Status(-es).
     *
     * @since 1.0.0
     */
    public function getStatus(string $function = '');


    /**
     * Sets release instruction status.
     *
     * @param  string  $function Function name.
     * @param  boolean $flag     Flag value.
     * @return boolean Set/unset status.
     *
     * @since 1.0.0
     */
    public function setStatus(string $function = '', bool $flag = true): bool;
}
