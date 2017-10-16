<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

use AspectMock\Test;
use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPOptionStore\FilteredOptionStore
 */
class FilteredOptionStoreTest extends WPTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->applyFilters = Test::func(
            __NAMESPACE__,
            'apply_filters',
            function ($_tag, $value) {
                return 'filtered ' . $value;
            }
        );

        $this->databaseStrategy = new DatabaseStrategy();
        $this->constantStrategy = new ConstantStrategy();
        $this->optionStore = new FilteredOptionStore($this->constantStrategy, $this->databaseStrategy);
    }

    public function tearDown()
    {
        delete_option('filtered_option_store_test_my_option');

        parent::tearDown();
    }

    /** @test */
    public function it_is_an_instance_of_option_store_interface()
    {
        $this->assertInstanceOf(OptionStoreInterface::class, $this->optionStore);
    }

    /** @test */
    public function it_is_an_instance_of_option_store()
    {
        $this->assertInstanceOf(OptionStore::class, $this->optionStore);
    }

    /** @test */
    public function it_filters_option()
    {
        update_option('filtered_option_store_test_my_option', 'abc123');

        $actual = $this->optionStore->get('filtered_option_store_test_my_option');

        $this->assertSame('filtered abc123', $actual);
        $this->applyFilters->verifyInvokedOnce(['filtered_option_store_test_my_option', 'abc123']);
    }
}
