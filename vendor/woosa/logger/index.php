<?php
/**
 * Index
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


//init
Module_Logger_Hook::init();
Module_Logger_Hook_Assets::init();
Module_Logger_Hook_Settings::init();