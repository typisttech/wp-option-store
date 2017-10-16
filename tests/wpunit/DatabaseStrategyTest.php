<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

use Codeception\TestCase\WPTestCase;
use WP_User;

/**
 * @covers \TypistTech\WPOptionStore\DatabaseStrategy
 */
class DatabaseStrategyTest extends WPTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->strategy = new DatabaseStrategy();
    }

    public function tearDown()
    {
        delete_option('database_strategy_test_my_option');

        parent::tearDown();
    }

    /** @test */
    public function it_is_an_instance_of_strategy_interface()
    {
        $this->assertInstanceOf(StrategyInterface::class, $this->strategy);
    }

    /** @test */
    public function it_gets_option()
    {
        update_option('database_strategy_test_my_option', 'abc123');

        $actual = $this->strategy->get('database_strategy_test_my_option');

        $this->assertSame('abc123', $actual);
    }

    /** @test */
    public function it_gets_option_as_object()
    {
        $expected = new WP_User(999999, 'my tester');
        update_option('database_strategy_test_my_option', $expected);

        $actual = $this->strategy->get('database_strategy_test_my_option');

        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function it_defaults_option_to_null()
    {
        delete_option('database_strategy_test_my_option');

        $actual = $this->strategy->get('database_strategy_test_my_option');

        $this->assertNull($actual);
    }
}
