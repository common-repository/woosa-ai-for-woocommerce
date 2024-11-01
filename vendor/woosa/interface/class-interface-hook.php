<?php
/**
 * Interface Hook
 *
 * This interface is dedicated for general hooks.
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


interface Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init();
}