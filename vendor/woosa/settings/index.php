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
Module_Settings_Hook::init();
Module_Settings_Hook_AJAX::init();
Module_Settings_Hook_Assets::init();
Module_Settings_Hook_Dashboard::init();
