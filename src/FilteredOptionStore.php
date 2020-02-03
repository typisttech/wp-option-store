<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

class FilteredOptionStore extends OptionStore
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
        return apply_filters(
            $this->filterTagFor($optionName),
            parent::get($optionName)
        );
    }

    /**
     * Normalize option name and key to snake_case filter tag.
     *
     * @param string $optionName Name of option to retrieve.
     *                           Expected to not be SQL-escaped.
     *
     * @return string
     */
    private function filterTagFor(string $optionName): string
    {
        return strtolower($optionName);
    }
}
