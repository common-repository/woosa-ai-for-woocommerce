<?php
/**
 * Plugin Name: Integration Woosa AI for WooCommerce
 * Description: Generate product descriptions in WooCommerce with AI and have unique product content for SEO purposes.
 * Version: 1.1.2
 * Author: Woosa.ai
 * Author URI: https://woosa.ai
 * Text Domain: woosa-ai-for-woocommerce
 * Domain Path: /languages
 * Network: false
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * WC requires at least: 5.0
 * WC tested up to: 9.0
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


define(__NAMESPACE__ . '\PREFIX', 'wsaw');

define(__NAMESPACE__ . '\VERSION', '1.1.2');

define(__NAMESPACE__ . '\NAME', 'Integration Woosa AI for WooCommerce');

define(__NAMESPACE__ . '\DIR_URL', untrailingslashit(plugin_dir_url(__FILE__)));

define(__NAMESPACE__ . '\DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

define(__NAMESPACE__ . '\DIR_NAME', plugin_basename(DIR_PATH));

define(__NAMESPACE__ . '\DIR_BASENAME', DIR_NAME . '/'.basename(__FILE__));

define(__NAMESPACE__ . '\SETTINGS_TAB_ID', 'woosa-ai');

define(__NAMESPACE__ . '\SETTINGS_TAB_NAME', 'Woosa AI');

define(__NAMESPACE__ . '\SETTINGS_URL', admin_url('/admin.php?page=' . SETTINGS_TAB_ID));

define(__NAMESPACE__ . '\DEBUG', get_option(PREFIX . '_debug') === 'yes' ? true:false);

define(__NAMESPACE__ . '\DEBUG_FILE', DIR_PATH . '/debug.log');


//include files
require_once DIR_PATH . '/vendor/autoload.php';

//init
Module_Core_Hook::init();