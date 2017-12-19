<?php
/**
 * Copyright 2017 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Cloud\Samples\Tasks\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Unit Tests for tasks commands.
 */
class tasksTest extends \PHPUnit_Framework_TestCase
{
    protected static $hasCredentials;
    protected static $project;
    protected static $queue;
    protected static $location;


    public static function setUpBeforeClass()
    {
        $path = getenv('GOOGLE_APPLICATION_CREDENTIALS');
        self::$hasCredentials = $path && file_exists($path) &&
            filesize($path) > 0;
        self::$project = getenv('GOOGLE_CLOUD_PROJECT');
        self::$queue = getenv('CLOUD_TASKS_APPENGINE_QUEUE');
        self::$location = getenv('CLOUD_TASKS_LOCATION');
    }

    public function setUp()
    {
        if (!self::$hasCredentials) {
            $this->markTestSkipped('No application credentials were found.');
        } else if (!self::$project) {
            $this->markTestSkipped('No project ID was found.');
        } else if (!self::$queue) {
            $this->markTestSkipped('No App Engine Queue was found.');
        } else if (!self::$location) {
            $this->markTestSkipped('No location was found.');
        }
    }

    public function testCreateTask()
    {
        $output = $this->runCommand('create-task', ['project' => self::$project, 'queue' => self::$queue, 'location' => self::$location]);
        $taskNamePrefix = sprintf('projects/%s/locations/%s/queues/%s/tasks/',
            self::$project,
            self::$location,
            self::$queue
        );
        $expectedOutput = sprintf('Created task %s', $taskNamePrefix);
        $this->assertContains($expectedOutput, $output);
    }

    private function runCommand($commandName, $args)
    {
        $application = require __DIR__ . '/../tasks.php';
        $command = $application->get($commandName);
        $commandTester = new CommandTester($command);

        ob_start();
        $commandTester->execute(
            $args,
            ['interactive' => false]);
        return ob_get_clean();
    }
}
