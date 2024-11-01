<?php
/**
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


echo Util::get_template("select.php", [
   'field'       => $field,
   'is_disabled' => $is_disabled,
   'instance'    => $instance,
], dirname(__FILE__));