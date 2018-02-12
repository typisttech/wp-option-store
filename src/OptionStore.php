<?php
/**
 * WP Option Store
 *
 * Extending WordPress Options API, read options from places other than database, the OOP way.
 *
 * @package   TypistTech\WPOptionStore
 *
 * @author    Typist Tech <wp-option-store@typist.tech>
 * @copyright 2017-2018 Typist Tech
 * @license   GPL-2.0+
 *
 * @see       https://typist.tech/projects/wp-option-store
 * @see       https://github.com/TypistTech/wp-option-store
 */

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

class OptionStore implements OptionStoreInterface
{
    /**
     * Strategies
     *
     * @var StrategyInterface[]
     */
    private $strategies;

    /**
     * OptionStore constructor.
     *
     * @param StrategyInterface[] ...$strategies Strategies that get option values.
     */
    public function __construct(StrategyInterface ...$strategies)
    {
        $this->strategies = $strategies;
    }

    /**
     * Get an option value.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return mixed|null
     */
    public function get(string $optionName)
    {
        foreach ($this->strategies as $strategy) {
            $value = $strategy->get($optionName);

            if (null !== $value) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Cast an option value into boolean.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return bool
     */
    public function getBoolean(string $optionName): bool
    {
        return in_array(
            $this->get($optionName),
            self::TRUE_VALUES,
            true
        );
    }

    /**
     * Cast an option value into integer.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return int
     */
    public function getInt(string $optionName): int
    {
        return (int) $this->get($optionName);
    }

    /**
     * Cast an option value into string.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return string
     */
    public function getString(string $optionName): string
    {
        return (string) $this->get($optionName);
    }

    /**
     * Cast an option value into array.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return array
     */
    public function getArray(string $optionName): array
    {
        return (array) $this->get($optionName);
    }
}
