<?php

/*
 * This file is part of the webmozart/console package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webmozart\Console\Tests\Api\Config;

use PHPUnit_Framework_TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Webmozart\Console\Api\Command\Command;
use Webmozart\Console\Api\Config\ApplicationConfig;
use Webmozart\Console\Api\Config\CommandConfig;
use Webmozart\Console\Api\Config\SubCommandConfig;
use Webmozart\Console\Handler\RunnableHandler;
use Webmozart\Console\Tests\Api\Config\Fixtures\TestNestedRunnableConfig;
use Webmozart\Console\Tests\Api\Config\Fixtures\TestRunnableConfig;
use Webmozart\Console\Tests\Handler\Fixtures\TestRunnable;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class SubCommandConfigTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $config = new SubCommandConfig();

        $this->assertNull($config->getParentConfig());
        $this->assertNull($config->getApplicationConfig());
        $this->assertNull($config->getName());
    }

    public function testCreateWithArguments()
    {
        $applicationConfig = new ApplicationConfig();
        $parentConfig = new CommandConfig('command', $applicationConfig);
        $config = new SubCommandConfig('sub', $parentConfig, $applicationConfig);

        $this->assertSame($parentConfig, $config->getParentConfig());
        $this->assertSame($applicationConfig, $config->getApplicationConfig());
        $this->assertSame('sub', $config->getName());
    }

    public function testGetHandlerInheritsParentHandlerByDefault()
    {
        $parentConfig = new CommandConfig();
        $parentConfig->setCallback($callback = function () { return 'foo'; });

        $config = new SubCommandConfig('command', $parentConfig);

        $handler = $config->getHandler(new Command($config));

        $this->assertInstanceOf('Webmozart\Console\Handler\CallableHandler', $handler);
        $this->assertSame('foo', $handler->handle(new StringInput('test')));
    }

    public function testGetHandlerWithCallback()
    {
        $parentConfig = new CommandConfig();
        $parentConfig->setCallback($parentCallback = function () { return 'foo'; });

        $config = new SubCommandConfig('command', $parentConfig);
        $config->setCallback($callback = function () { return 'bar'; });
        $command = new Command($config);

        $handler = $config->getHandler($command);
        $handler->initialize($command, new BufferedOutput(), new BufferedOutput());

        $this->assertInstanceOf('Webmozart\Console\Handler\CallableHandler', $handler);
        $this->assertSame('bar', $handler->handle(new StringInput('test')));
    }

    public function testGetHandlerWithRunnableConfig()
    {
        $parentConfig = new TestRunnableConfig();
        $config = new TestNestedRunnableConfig('command', $parentConfig);
        $command = new Command($config);

        $handler = $config->getHandler($command);
        $handler->initialize($command, new BufferedOutput(), new BufferedOutput());

        $this->assertInstanceOf('Webmozart\Console\Handler\RunnableHandler', $handler);
        $this->assertSame('bar', $handler->handle(new StringInput('test')));
    }

    public function testSetHandler()
    {
        $handler = $this->getMock('Webmozart\Console\Api\Handler\CommandHandler');

        $parentConfig = new CommandConfig();
        $config = new SubCommandConfig('command', $parentConfig);
        $config->setHandler($handler);
        $command = new Command($config);

        $this->assertSame($handler, $config->getHandler($command));
    }

    public function testSetHandlerToFactoryCallback()
    {
        $handler = $this->getMock('Webmozart\Console\Api\Handler\CommandHandler');

        $factory = function (Command $command) use (&$passedCommand, $handler) {
            $passedCommand = $command;

            return $handler;
        };

        $parentConfig = new CommandConfig();
        $config = new SubCommandConfig('command', $parentConfig);
        $config->setHandler($factory);
        $command = new Command($config);

        $this->assertSame($handler, $config->getHandler($command));
        $this->assertSame($command, $passedCommand);
    }

    public function testSetHandlerToRunnable()
    {
        $runnable = new TestRunnable(function () { return 'bar'; });

        $parentConfig = new CommandConfig();
        $config = new SubCommandConfig('command', $parentConfig);
        $config->setHandler($runnable);
        $command = new Command($config);

        $this->assertEquals(new RunnableHandler($runnable), $config->getHandler($command));
    }
}
