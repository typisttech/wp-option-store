<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPOptionStore\ConstantStrategy
 */
class ConstantStrategyTest extends WPTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->strategy = new ConstantStrategy();
    }

    /** @test */
    public function it_is_an_instance_of_strategy_interface()
    {
        $this->assertInstanceOf(StrategyInterface::class, $this->strategy);
    }

    /** @test */
    public function it_gets_option()
    {
        define('TESTING_IT_GETS_OPTION', 'abc123');

        $actual = $this->strategy->get('TESTING_IT_GETS_OPTION');
        $this->assertSame('abc123', $actual);

        $actual = $this->strategy->get('testing_it_gets_option');
        $this->assertSame('abc123', $actual);
    }

    /** @test */
    public function it_defaults_option_to_null()
    {
        $actual = $this->strategy->get('my_non_existing_option');

        $this->assertNull($actual);
    }
}
