<?php

namespace Tests\Core;

class FakeModule extends \Framework\Module {

    public const DEFINITIONS = [
        'a' => 'b'
    ];

}

class AppTest extends \PHPUnit\Framework\TestCase {

    public function testDefinitions () {
        $app = new \Framework\App([], [FakeModule::class]);
        $this->assertEquals('b', $app->getContainer()->get('a'));
    }

    public function testGetModule () {
        $app = new \Framework\App([], [FakeModule::class]);
        $this->assertEquals([FakeModule::class], $app->getModules());
    }

}