<?php

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

interface OptionStoreInterface
{
    public const TRUE_VALUES = [
        '1',
        'on',
        'On',
        'ON',
        'true',
        'True',
        'TRUE',
        'y',
        'Y',
        'yes',
        'Yes',
        'YES',
        1,
        true,
    ];

    /**
     * Get an option value.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return mixed|null
     */
    public function get(string $optionName);

    /**
     * Cast an option value into boolean.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return bool
     */
    public function getBoolean(string $optionName): bool;

    /**
     * Cast an option value into integer.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return int
     */
    public function getInt(string $optionName): int;

    /**
     * Cast an option value into string.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return string
     */
    public function getString(string $optionName): string;

    /**
     * Cast an option value into array.
     *
     * @param string $optionName Name of option to retrieve.
     *
     * @return array
     */
    public function getArray(string $optionName): array;
}
