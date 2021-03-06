<?php
namespace tests\php\manager\crontab;

use tests\php\manager\crontab\mock\MockCliTool;

require_once __DIR__ . '/mock/MockCliTool.php';

/**
 * Test class for CliTool.
 * Generated by PHPUnit on 2012-04-12 at 21:48:37.
 */
class CliToolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MockCliTool
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MockCliTool();
    }

    private $orginalCmd;

    private function restoreArgv()
    {
        if ($this->orginalCmd) {
            $this->setArgv($this->orginalCmd);
        }
    }

    private function setArgv($line)
    {
        if ($this->orginalCmd === null) {
            $this->orginalCmd = join(' ', $_SERVER['argv']);
        }
        $splitd = preg_split('/\s+/', $line);
        $count = count($splitd);
        $_SERVER['argc'] = $argc = $count;
        $_SERVER['argv'] = $argv = $splitd;
    }

    /**
     * @covers php\manager\crontab\CliTool::run
     */
    public function testRun1()
    {
        $this->markTestIncomplete('Implement this');

        $inst = MockCliTool::getInstance();

        $this->setArgv('cronman --help');
        $status = MockCliTool::run();
        $this->assertEquals(0, $status);
        $out = join("\n", MockCliTool::$out);
        $expected = $inst->usage();
        $this->assertEquals($expected, $out);
    }

    /**
     * @covers php\manager\crontab\CliTool::enable
     */
    public function testEnable()
    {
        $manager = $this->object->manager;
        $before = $manager->listJobs();
        $this->object->setTargetFile(__DIR__ . '/resources/enable.txt');
        $this->object->enable();
        $after = $manager->listJobs();

        $this->assertNotEquals($before, $after);
        $this->assertContains('1	2	3	4	5	/usr/bin/uptime', $after);
        $this->assertContains('Autogenerated by CrontabManager', $after);
    }

    /**
     * @covers php\manager\crontab\CliTool::disable
     */
    public function testDisable()
    {
        $manager = $this->object->manager;
        $this->object->setTargetFile(__DIR__ . '/resources/enable.txt');
        $this->object->enable();
        $before = $manager->listJobs();

        $this->object->disable();
        $after = $manager->listJobs();

        $this->assertNotEquals($before, $after);
        $this->assertContains('1	2	3	4	5	/usr/bin/uptime', $before);
        $this->assertContains('Autogenerated by CrontabManager', $before);
        $this->assertNotContains('1	2	3	4	5	/usr/bin/uptime', $after);
        $this->assertNotContains('Autogenerated by CrontabManager', $after);
    }

    /**
     * @covers php\manager\crontab\CliTool::usage
     */
    public function testUsage()
    {
        $actual = $this->object->usage();
        $expected = <<<USAGE
Usage: cronman <--enable|-e FILE,--disable|-d FILE> [--user|-u USER] [--verbose|-v] [--help|-h] [--usage]

Required params:
   --enable|-e FILE   Enable target FILE to crontab, replace it if already set
   --disable|-d FILE  Disable target FILE from crontab

Optional params:
   --user|-u USER     For which user to run this program
   --verbose|-v       Display more massages
   --help|-h,--usage  Displays this help

USAGE;
        $this->assertEquals($expected, $actual);
    }
}
