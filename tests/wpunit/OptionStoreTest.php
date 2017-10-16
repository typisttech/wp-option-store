<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPOptionStore\OptionStore
 */
class OptionStoreTest extends WPTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->databaseStrategy = new DatabaseStrategy();
        $this->constantStrategy = new ConstantStrategy();
        $this->optionStore = new OptionStore($this->constantStrategy, $this->databaseStrategy);
    }

    public function tearDown()
    {
        delete_option('option_store_test_my_option');
        delete_option('option_store_test_my_non_null_option');

        parent::tearDown();
    }

    /** @test */
    public function it_is_an_instance_of_option_store_interface()
    {
        $this->assertInstanceOf(OptionStoreInterface::class, $this->optionStore);
    }

    /** @test */
    public function it_gets_an_option()
    {
        $expected = 'some option value';
        update_option('option_store_test_my_option', $expected);

        $actual = $this->optionStore->get('option_store_test_my_option');

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_gets_first_non_null_option()
    {
        $expected = 'some non null option';
        define('OPTION_STORE_TEST_MY_NON_NULL_OPTION', $expected);
        update_option('option_store_test_my_non_null_option', 'not expecting value');

        $actual = $this->optionStore->get('option_store_test_my_non_null_option');

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_handles_not_exist_option()
    {
        delete_option('option_store_test_my_non_exist_option');

        $actual = $this->optionStore->get('option_store_test_my_non_exist_option');
        $this->assertNull($actual);

        $actual = $this->optionStore->getBoolean('option_store_test_my_non_exist_option');
        $this->assertFalse($actual);
        $this->assertInternalType('boolean', $actual);

        $actual = $this->optionStore->getInt('option_store_test_my_non_exist_option');
        $this->assertSame(0, $actual);
        $this->assertInternalType('integer', $actual);

        $actual = $this->optionStore->getArray('option_store_test_my_non_exist_option');
        $this->assertSame([], $actual);
        $this->assertInternalType('array', $actual);
    }

    /**
     * @test
     * @dataProvider trueValueProvider
     */
    public function it_casts_true_value($example)
    {
        update_option('option_store_test_my_option', $example);

        $actual = $this->optionStore->getBoolean('option_store_test_my_option');

        $this->assertTrue($actual);
        $this->assertInternalType('boolean', $actual);
    }

    public function trueValueProvider(): array
    {
        return [
            'string 1' => ['1'],
            'on' => ['on'],
            'On' => ['On'],
            'ON' => ['ON'],
            'string true' => ['true'],
            'True' => ['True'],
            'TRUE' => ['TRUE'],
            'y' => ['y'],
            'Y' => ['Y'],
            'yes' => ['yes'],
            'Yes' => ['Yes'],
            'YES' => ['YES'],
            'integer 1' => [1],
            'boolean true' => [true],
        ];
    }

    /**
     * @test
     * @dataProvider falseValueProvider
     */
    public function it_casts_false_value($example)
    {
        update_option('option_store_test_my_option', $example);

        $actual = $this->optionStore->getBoolean('option_store_test_my_option');

        $this->assertFalse($actual);
        $this->assertInternalType('boolean', $actual);
    }

    public function falseValueProvider(): array
    {
        return [
            'string 0' => ['0'],
            'empty string' => [''],
            'off' => ['off'],
            'Off' => ['Off'],
            'OFF' => ['OFF'],
            'string false' => ['false'],
            'False' => ['False'],
            'FALSE' => ['FALSE'],
            'n' => ['n'],
            'N' => ['N'],
            'no' => ['no'],
            'No' => ['No'],
            'NO' => ['NO'],
            'integer 0' => [0],
            'boolean false' => [false],
            'null' => [null],
            'empty array' => [[]],
        ];
    }

    /**
     * @test
     * @dataProvider integerValueProvider
     */
    public function it_casts_integer_value($value, int $expected)
    {
        update_option('option_store_test_my_option', $value);

        $actual = $this->optionStore->getInt('option_store_test_my_option');

        $this->assertSame($expected, $actual);
        $this->assertInternalType('integer', $actual);
    }

    public function integerValueProvider(): array
    {
        return [
            'string -1' => ['-1', -1],
            'integer -1' => [-1, -1],
            'string 0' => ['0', 0],
            'integer 0' => [0, 0],
            'string 1' => ['1', 1],
            'integer 1' => [1, 1],
            'null' => [null, 0],
            'empty string' => ['', 0],
            'empty array' => [[], 0],
        ];
    }

    /**
     * @test
     * @dataProvider stringValueProvider
     */
    public function it_casts_string_value($value, string $expected)
    {
        update_option('option_store_test_my_option', $value);

        $actual = $this->optionStore->getString('option_store_test_my_option');

        $this->assertSame($expected, $actual);
        $this->assertInternalType('string', $actual);
    }

    public function stringValueProvider(): array
    {
        return [
            'string -1' => ['-1', '-1'],
            'integer -1' => [-1, '-1'],
            'string 0' => ['0', '0'],
            'integer 0' => [0, '0'],
            'string 1' => ['1', '1'],
            'integer 1' => [1, '1'],
            'null' => [null, ''],
            'empty string' => ['', ''],
            'string abc' => ['abc', 'abc'],
        ];
    }

    /**
     * @test
     * @dataProvider arrayValueProvider
     */
    public function it_casts_array_value($value, array $expected)
    {
        update_option('option_store_test_my_array_option', $value);

        $actual = $this->optionStore->getArray('option_store_test_my_array_option');

        $this->assertSame($expected, $actual);
        $this->assertInternalType('array', $actual);
    }

    public function arrayValueProvider(): array
    {
        return [
            'string' => ['abc123', ['abc123']],
            'integer' => [1234, [1234]],
            'boolean true' => [true, [true]],
            'boolean false' => [false, []],
            'array false' => [[false], [false]],
            'array null' => [[null], [null]],
            'empty string' => ['', ['']],
            'array' => [[1, false, null, 'xyz', ['a', 'b']], [1, false, null, 'xyz', ['a', 'b']]],
        ];
    }
}
