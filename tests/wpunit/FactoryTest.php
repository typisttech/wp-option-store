<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPOptionStore\Factory
 */
class FactoryTest extends WPTestCase
{
    /** @test */
    public function it_builds_an_option_store()
    {
        $actual = Factory::build();

        $this->assertInstanceOf(OptionStoreInterface::class, $actual);
    }

    /** @test */
    public function it_builds_a_filtered_option_store_with_strategies()
    {
        $expected = new FilteredOptionStore(
            new ConstantStrategy(),
            new DatabaseStrategy()
        );

        $actual = Factory::build();

        $this->assertEquals($expected, $actual);
    }
}
