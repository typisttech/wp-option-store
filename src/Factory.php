<?php

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
