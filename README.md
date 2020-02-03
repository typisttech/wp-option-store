# WP Option Store

[![Packagist](https://img.shields.io/packagist/v/typisttech/wp-option-store.svg?style=flat-square)](https://packagist.org/packages/typisttech/wp-option-store)
[![Packagist](https://img.shields.io/packagist/dt/typisttech/wp-option-store.svg?style=flat-square)](https://packagist.org/packages/typisttech/wp-option-store)
![PHP from Packagist](https://img.shields.io/packagist/php-v/TypistTech/wp-option-store?style=flat-square)
[![CircleCI](https://circleci.com/gh/TypistTech/wp-option-store.svg?style=svg)](https://circleci.com/gh/TypistTech/wp-option-store)
[![codecov](https://codecov.io/gh/TypistTech/wp-option-store/branch/master/graph/badge.svg)](https://codecov.io/gh/TypistTech/wp-option-store)
[![GitHub](https://img.shields.io/github/license/TypistTech/wp-option-store.svg?style=flat-square)](https://github.com/TypistTech/wp-option-store/blob/master/LICENSE.md)
[![GitHub Sponsor](https://img.shields.io/badge/Sponsor-GitHub-ea4aaa?style=flat-square&logo=github)](https://github.com/sponsors/TangRufus)
[![Sponsor via PayPal](https://img.shields.io/badge/Sponsor-PayPal-blue.svg?style=flat-square&logo=paypal)](https://typist.tech/donate/wp-option-store/)
[![Hire Typist Tech](https://img.shields.io/badge/Hire-Typist%20Tech-ff69b4.svg?style=flat-square)](https://typist.tech/contact/)
[![Twitter Follow @TangRufus](https://img.shields.io/twitter/follow/TangRufus?style=flat-square&color=1da1f2&logo=twitter)](https://twitter.com/tangrufus)

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
  - [Will you add support for older PHP versions?](#will-you-add-support-for-older-php-versions)
  - [It looks awesome. Where can I find some more goodies like this?](#it-looks-awesome-where-can-i-find-some-more-goodies-like-this)
  - [Where can I give :star::star::star::star::star: reviews?](#where-can-i-give-starstarstarstarstar-reviews)
- [Sponsoring :heart:](#sponsoring-heart)
  - [GitHub Sponsors Matching Fund](#github-sponsors-matching-fund)
  - [Why don't you hire me?](#why-dont-you-hire-me)
  - [Want to help in other way? Want to be a sponsor?](#want-to-help-in-other-way-want-to-be-a-sponsor)
- [Running the Tests](#running-the-tests)
- [Feedback](#feedback)
- [Change log](#change-log)
- [Security](#security)
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

### Will you add support for older PHP versions?

Never! This plugin will only work on [actively supported PHP versions](https://secure.php.net/supported-versions.php).

Don't use it on **end of life** or **security fixes only** PHP versions.

### It looks awesome. Where can I find some more goodies like this?

- Articles on Typist Tech's [blog](https://typist.tech)
- More projects on [Typist Tech's GitHub profile](https://github.com/TypistTech/)
- More plugins on [TangRufus'](https://profiles.wordpress.org/tangrufus/#content-plugins) wp.org profiles
- Stay tuned on [Typist Tech's newsletter](https://typist.tech/go/newsletter)
- Follow [@TangRufus](https://twitter.com/tangrufus) on Twitter
- Hire [Tang Rufus](https://typist.tech/contact) to build your next awesome site

### Where can I give :star::star::star::star::star: reviews?

Thanks! Glad you like it. It's important to let my know somebody is using this project. Since this is not hosted on wordpress.org, please consider:

- tweet something good with mentioning [@TangRufus](https://twitter.com/tangrufus)
- :star: star this [Github repo](https://github.com/typisttech/wp-option-store)
- :eyes: [watch](https://github.com/typisttech/wp-option-store/subscription) this Github repo
- write blog posts
- submit [pull requests](https://github.com/typisttech/wp-option-store)
- [sponsor](https://github.com/sponsors/TangRufus) Tang Rufus to maintain his open source projects
- hire [Tang Rufus](https://typist.tech/contact) to build your next awesome site

## Sponsoring :heart:

Love `WP Option Store`? Help me maintain it, a [sponsorship here](https://typist.tech/donation/) can help with it.

### GitHub Sponsors Matching Fund

Do you know [GitHub is going to match your sponsorship](https://help.github.com/en/github/supporting-the-open-source-community-with-github-sponsors/about-github-sponsors#about-the-github-sponsors-matching-fund)?

[Sponsor now via GitHub](https://github.com/sponsors/TangRufus) to double your greatness.

### Why don't you hire me?

Ready to take freelance WordPress jobs. Contact me via the contact form [here](https://typist.tech/contact/) or, via email [info@typist.tech](mailto:info@typist.tech)

### Want to help in other way? Want to be a sponsor?

Contact: [Tang Rufus](mailto:tangrufus@gmail.com)

## Running the Tests

Run the tests:

``` bash
$ composer test
$ composer style:check
```

## Feedback

**Please provide feedback!** We want to make this project useful in as many projects as possible.
Please submit an [issue](https://github.com/TypistTech/wp-option-store/issues/new) and point out what you do and don't like, or fork the project and make suggestions.
**No issue is too small.**

## Change log

Please see [CHANGELOG](./CHANGELOG.md) for more information on what has changed recently.

## Security

If you discover any security related issues, please email [wp-option-store@typist.tech](mailto:wp-option-store@typist.tech) instead of using the issue tracker.

## Credits

[`WP Option Store`](https://github.com/TypistTech/wp-option-store) is a [Typist Tech](https://typist.tech) project and maintained by [Tang Rufus](https://twitter.com/Tangrufus), freelance developer for [hire](https://typist.tech/contact/).

Full list of contributors can be found [here](https://github.com/TypistTech/wp-option-store/graphs/contributors).

## License

The MIT License (MIT). Please see [License File](./LICENSE.md) for more information.
