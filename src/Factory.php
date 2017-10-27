<?php
/**
 * WP Option Store
 *
 * Extending WordPress Options API, read options from places other than database, the OOP way.
 *
 * @package   TypistTech\WPOptionStore
 *
 * @author    Typist Tech <wp-option-store@typist.tech>
 * @copyright 2017 Typist Tech
 * @license   GPL-2.0+
 *
 * @see       https://www.typist.tech/projects/wp-option-store
 * @see       https://github.com/TypistTech/wp-option-store
 */

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

final class Factory
{
    /**
     * Set up option store with default strategies.
     *
     * @return FilteredOptionStore
     */
    public static function build(): FilteredOptionStore
    {
        return new FilteredOptionStore(
            new ConstantStrategy(),
            new DatabaseStrategy()
        );
    }
}
