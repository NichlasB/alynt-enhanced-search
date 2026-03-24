<?php

use Brain\Monkey;

class Test_Sample extends \PHPUnit\Framework\TestCase {
    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_plugin_constants_defined() {
        $this->assertTrue( defined( 'ALYNT_ES_VERSION' ) );
    }
}
