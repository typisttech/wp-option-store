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

/**
 * Class ConstantStrategy
 *
 * Get options from PHP constants.
 */
class ConstantStrategy implements StrategyInterface
{
    /**
     * Option getter.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return mixed|null Null if option not exists or its value is actually null.
     */
    public function get(string $optionName)
    {
        $constantName = $this->constantNameFor($optionName);

        if (! defined($constantName)) {
            return null;
        }

        return constant($constantName);
    }

    /**
     * Normalize option name and key to SCREAMING_SNAKE_CASE constant name.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return string
     */
    private function constantNameFor(string $optionName): string
    {
        return strtoupper($optionName);
    }
}
