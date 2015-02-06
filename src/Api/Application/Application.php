<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Api\Application;

use OutOfBoundsException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\Command\CommandCollection;
use Webmozart\Console\Api\Config\ApplicationConfig;
use Webmozart\Console\Api\Input\InputDefinition;

/**
 * A console application.
 *
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
interface Application
{
    /**
     * Returns the application configuration.
     *
     * @return ApplicationConfig The application configuration.
     */
    public function getConfig();

    /**
     * Returns the base input definition of the application.
     *
     * @return InputDefinition The base input definition.
     */
    public function getBaseInputDefinition();

    /**
     * Returns the command for a given name.
     *
     * @param string $name The name of the command.
     *
     * @return Command The command.
     *
     * @throws OutOfBoundsException If the command is not found.
     *
     * @see addCommand(), getCommands()
     */
    public function getCommand($name);

    /**
     * Returns all registered commands.
     *
     * @return CommandCollection The commands.
     *
     * @see addCommand(), getCommand()
     */
    public function getCommands();

    /**
     * Returns whether the application has a command with a given name.
     *
     * @param string $name The name of the command.
     *
     * @return bool Returns `true` if the command with the given name exists and
     *              `false` otherwise.
     *
     * @see hasCommands(), getCommand()
     */
    public function hasCommand($name);

    /**
     * Returns whether the application has any registered commands.
     *
     * @return bool Returns `true` if the application has any commands and
     *              `false` otherwise.
     *
     * @see hasCommand(), getCommands()
     */
    public function hasCommands();

    /**
     * Executes the command for a given input.
     *
     * @param InputInterface  $input  The console input. If not given, the
     *                                input passed to the PHP process is used.
     * @param OutputInterface $output The console output. If not given, the
     *                                application prints to the standard output.
     *
     * @return int The exit status.
     */
    public function run(InputInterface $input = null, OutputInterface $output = null);
}
