# WP Option Store

[![Latest Stable Version](https://poser.pugx.org/typisttech/wp-option-store/v/stable)](https://packagist.org/packages/typisttech/wp-option-store)
[![Total Downloads](https://poser.pugx.org/typisttech/wp-option-store/downloads)](https://packagist.org/packages/typisttech/wp-option-store)
[![Build Status](https://travis-ci.org/TypistTech/wp-option-store.svg?branch=master)](https://travis-ci.org/TypistTech/wp-option-store)
[![codecov](https://codecov.io/gh/TypistTech/wp-option-store/branch/master/graph/badge.svg)](https://codecov.io/gh/TypistTech/wp-option-store)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/TypistTech/wp-option-store/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/TypistTech/wp-option-store/?branch=master)
[![PHP Versions Tested](http://php-eye.com/badge/typisttech/wp-option-store/tested.svg)](https://travis-ci.org/TypistTech/wp-option-store)
[![StyleCI](https://styleci.io/repos/106634459/shield?branch=master)](https://styleci.io/repos/106634459)
[![Dependency Status](https://gemnasium.com/badges/github.com/TypistTech/wp-option-store.svg)](https://gemnasium.com/github.com/TypistTech/wp-option-store)
[![License](https://poser.pugx.org/typisttech/wp-option-store/license)](https://packagist.org/packages/typisttech/wp-option-store)
[![Donate via PayPal](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.typist.tech/donate/wp-option-store/)
[![Hire Typist Tech](https://img.shields.io/badge/Hire-Typist%20Tech-ff69b4.svg)](https://www.typist.tech/contact/)

Extending WordPress Options API, read options from places other than database, the OOP way.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->


- [The Goals, or What This Package Does?](#the-goals-or-what-this-package-does)
- [Install](#install)
- [Usage](#usage)
  - [Example](#example)
  - [ConstantStrategy](#constantstrategy)
  - [DatabaseStrategy](#databasestrategy)
  - [OptionStore](#optionstore)
    - [`__construct(StrategyInterface ...$strategies)`](#__constructstrategyinterface-strategies)
    - [`get(string $optionName)`](#getstring-optionname)
  - [Type casting](#type-casting)
  - [FilteredOptionStore](#filteredoptionstore)
    - [`get(string $optionName)`](#getstring-optionname-1)
  - [Factory](#factory)
    - [`build(): FilteredOptionStore`](#build-filteredoptionstore)
- [Frequently Asked Questions](#frequently-asked-questions)
  - [Can I implement my own strategy classes?](#can-i-implement-my-own-strategy-classes)
  - [Can I change the order of the strategies?](#can-i-change-the-order-of-the-strategies)
  - [Is this a plugin?](#is-this-a-plugin)
  - [What to do when wp.org plugin team tell me to clean up the `vendor` folder?](#what-to-do-when-wporg-plugin-team-tell-me-to-clean-up-the-vendor-folder)
  - [Can two different plugins use this package at the same time?](#can-two-different-plugins-use-this-package-at-the-same-time)
  - [Do you have real life examples that use this package?](#do-you-have-real-life-examples-that-use-this-package)
- [Support!](#support)
  - [Donate via PayPal *](#donate-via-paypal-)
  - [Donate Monero](#donate-monero)
  - [Mine me some Monero](#mine-me-some-monero)
  - [Why don't you hire me?](#why-dont-you-hire-me)
  - [Want to help in other way? Want to be a sponsor?](#want-to-help-in-other-way-want-to-be-a-sponsor)
- [Developing](#developing)
- [Running the Tests](#running-the-tests)
- [Feedback](#feedback)
- [Change log](#change-log)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## The Goals, or What This Package Does?

WordPress Option API only allows you `get_option` from database. This package is for those who not accepting the status quo.

## Install

Installation should be done via composer, details of how to install composer can be found at [https://getcomposer.org/](https://getcomposer.org/).

``` bash
$ composer require typisttech/wp-option-store
```

You should put all `WP Option Store` classes under your own namespace to avoid class name conflicts.

- [imposter-plugin](https://github.com/Typisttech/imposter-plugin)
- [mozart](https://github.com/coenjacobs/mozart)

## Usage

### Example

```php
use TypistTech\WPOptionStore\Factory;

// By default, the `Factory` adds 2 strategies (order matters):
//  1. ConstantStrategy
//  2. DatabaseStrategy
$filteredOptionStore = Factory::build();

// To get an option from strategies:
//  1. Read `MY_OPTION` constant
//  2. If (1) is not `null`, jump to (6)
//  3. Read from `get_option('my_option')`, the normal WordPress Options API
//  4. If (3) is not `null`, jump to (6)
//  5. We have tried all strategies, pass `null` to (6)
//  6. Pass whatever we have read (could be null) through `apply_filters('my_option', $whateverWeHaveRead);`
$filteredOptionStore->get('my_option');

// To get an option and perform type cast.
$filteredOptionStore->getBoolean('my_boolean_option');
$filteredOptionStore->getInt('my_integer_option');
$filteredOptionStore->getString('my_string_option');
$filteredOptionStore->getArray('my_array_option');
```

### ConstantStrategy

This strategy gets options from [PHP constants](http://php.net/manual/en/function.constant.php).

```php
define('MY_OPTION', 'abc123');

$strategy = new ConstantStrategy();

$value1 = $strategy->get('my_option');
// $value1 === 'abc123';

$value2 = $strategy->get('my_non_exist_option');
// $value2 === null;
```

### DatabaseStrategy

This strategy gets options from [WordPress Options API](https://codex.wordpress.org/Options_API).

```php
update_option('my_option', 'abc123');

$strategy = new DatabaseStrategy();

$value1 = $strategy->get('my_option');
// $value1 === 'abc123';

$value2 = $strategy->get('my_non_exist_option');
// $value2 === null;
```

**Important**: An unset value should be `null` instead of WordPress' default `false`.

### OptionStore

This class gets option values from strategies.

#### `__construct(StrategyInterface ...$strategies)`

`OptionStore` constructor.

 * @param StrategyInterface[] ...$strategies Strategies that get option values.

```php
$databaseStrategy = new DatabaseStrategy();
$constantStrategy = new ConstantStrategy();
$optionStore = new OptionStore($constantStrategy, $databaseStrategy);
```

Note: Strategies order matters!

#### `get(string $optionName)`

Get an option value.

 * @param string $optionName Name of option to retrieve.

```php
// It returns the first non-null value from strategies.
define('MY_OPTION', 'abc');
update_option('my_option', 'xyz');

$value1 = $optionStore->get('my_option');
// $value1 === 'abc';

// It returns `null` when option not found.
$value2 = $optionStore->get('my_non_exist_option');
// $value2 === null;
```

### Type casting

`OptionStore` provides several helper methods for type casting.

```php
$optionStore->getBoolean('my_boolean_option');
$optionStore->getInt('my_integer_option');
$optionStore->getString('my_string_option');
$optionStore->getArray('my_array_option');
```

### FilteredOptionStore

This is a subclass of `OptionStore`.

#### `get(string $optionName)`

Get an option value.

 * @param string $optionName Name of option to retrieve.

```php
// It returns the first non-null value from strategies,
// and applies filters.
define('MY_OPTION', 'abc');
update_option('my_option', 'xyz');

add_filter('my_option', function($value) {
    return 'filtered ' . $value;
});

$value = $filteredOptionStore->get('my_option');
// $value === 'filtered abc';
```

Note: Filters are applied before type casting.

### Factory

Factory is a helper class to reduce boilerplate code for those who use default strategies.
If you use a [custom strategy](#can-i-implement-my-own-strategy-classes) or [reorder the strategies](#can-i-change-the-order-of-the-strategies), don't use this class.

#### `build(): FilteredOptionStore`

```php
$filteredOptionStore = Factory::build();
```

## Frequently Asked Questions

### Can I implement my own strategy classes?

Of course! Just implements the `StrategyInterface`.

Take a look at classes `ConstantStrategy` and `DatabaseStrategy` as well as their tests for example implementations of `StrategyInterface`.

If you'd like to create a open-source package to do this to help others, open a [new issue](https://github.com/TypistTech/wp-option-store/issues/new) to let us know, we'd love to help you with it.

### Can I change the order of the strategies?

Why not? Don't use `Factory`.

```php
// Order matters!
$optionStore = new FilteredOptionStore(
    new MyStrategy1(),
    new MyStrategy2(),
    new MyStrategy3(),
);
```

### Is this a plugin?

No, this is a package that should be part of your plugin.

### What to do when wp.org plugin team tell me to clean up the `vendor` folder?

Re-install packages via the following command. This package exports only necessary files to `dist`.

```bash
$ composer install --no-dev --prefer-dist --optimize-autoloader
```

### Can two different plugins use this package at the same time?

Yes, if put all `WP Option Store` classes under your own namespace to avoid class name conflicts.

- [imposter-plugin](https://github.com/Typisttech/imposter-plugin)
- [mozart](https://github.com/coenjacobs/mozart)

### Do you have real life examples that use this package?

Here you go:

 * [Sunny](https://github.com/Typisttech/sunny)
 * [WP Cloudflare Guard](https://github.com/TypistTech/wp-cloudflare-guard)

*Add your own plugin [here](https://github.com/TypistTech/wp-option-store/edit/master/README.md)*

## Support!

### Donate via PayPal [![Donate via PayPal](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.typist.tech/donate/wp-option-store/)

Love WP Option Store? Help me maintain WP Option Store, a [donation here](https://www.typist.tech/donate/wp-option-store/) can help with it.

### Donate Monero

Send Monero to my public address: `43fiS7JzAK7eSHCpjTL5J1JYqPb6pvM2dGex7aoFZ5u5e5QRg6NKNnFGXqPh6C53E3M8UvqzemVt43uLgimwDpW41zXUHAp`

### Mine me some Monero

1. Open one of the follow web pages open on your computer
2. Start the miner
3. Adjust threads and CPU usages
4. Keep it running

If you have an AdBlocker:

[https://authedmine.com/media/miner.html?key=I2z6pueJaeVCz5dh1uA8cru5Fl108DtH&user=wp-option-store&autostart=1](https://authedmine.com/media/miner.html?key=I2z6pueJaeVCz5dh1uA8cru5Fl108DtH&user=wp-option-store&autostart=1)

else:

[https://coinhive.com/media/miner.html?key=I2z6pueJaeVCz5dh1uA8cru5Fl108DtH&user=wp-option-store&autostart=1](https://coinhive.com/media/miner.html?key=I2z6pueJaeVCz5dh1uA8cru5Fl108DtH&user=wp-option-store&autostart=1)

### Why don't you hire me?

Ready to take freelance WordPress jobs. Contact me via the contact form [here](https://www.typist.tech/contact/) or, via email [info@typist.tech](mailto:info@typist.tech)

### Want to help in other way? Want to be a sponsor?

Contact: [Tang Rufus](mailto:tangrufus@gmail.com)

## Developing

To setup a developer workable version you should run these commands:

```bash
$ composer create-project --keep-vcs --no-install typisttech/wp-option-store:dev-master
$ cd wp-option-store
$ composer install
```

## Running the Tests

[WP Option Store](https://github.com/TypistTech/wp-option-store) run tests on [Codeception](http://codeception.com/) and relies [wp-browser](https://github.com/lucatume/wp-browser) to provide WordPress integration.
Before testing, you have to install WordPress locally and add a [codeception.yml](http://codeception.com/docs/reference/Configuration) file.
See [*.suite.example.yml](./tests/) for [Local by Flywheel](https://share.getf.ly/v20q1y) configuration examples.

Actually run the tests:

``` bash
$ composer test
```

We also test all PHP files against [PSR-2: Coding Style Guide](http://www.php-fig.org/psr/psr-2/) and part of the [WordPress coding standard](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards).

Check the code style with ``$ composer check-style`` and fix it with ``$ composer fix-style``.

## Feedback

**Please provide feedback!** We want to make this package useful in as many projects as possible.
Please submit an [issue](https://github.com/TypistTech/wp-option-store/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email wp-option-store@typist.tech instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CODE_OF_CONDUCT](./CODE_OF_CONDUCT.md) for details.

## Credits

[WP Option Store](https://github.com/TypistTech/wp-option-store) is a [Typist Tech](https://www.typist.tech) project and maintained by [Tang Rufus](https://twitter.com/Tangrufus), freelance developer for [hire](https://www.typist.tech/contact/).

Full list of contributors can be found [here](https://github.com/TypistTech/wp-option-store/graphs/contributors).

## License

[WP Option Store](https://github.com/TypistTech/wp-option-store) is licensed under the GPLv2 (or later) from the [Free Software Foundation](http://www.fsf.org/).
Please see [License File](LICENSE) for more information.
