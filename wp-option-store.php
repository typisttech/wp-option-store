<?php
/**
 * WP Option Store
 *
 * A simplified OOP implementation of the WordPress Options API.
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

/**
 * Plugin Name: WP Option Store
 * Plugin URI:  https://github.com/TypistTech/wp-option-store
 * Description: Example Plugin for WP Option Store
 * Version:     0.11.0
 * Author:      Tang Rufus
 * Author URI:  https://www.typist.tech/
 * Text Domain: wp-option-store
 * Domain Path: src/languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

declare(strict_types=1);

namespace TypistTech\WPOptionStore;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

require_once __DIR__ . '/vendor/autoload.php';
