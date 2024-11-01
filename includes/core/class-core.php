<?php
/**
 * Core
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Core{


   /**
    * Checks whether or not the plugin YOAST is active.
    *
    * @return boolean
    */
    public static function is_YOAST_active(){
      return is_plugin_active('wordpress-seo/wp-seo.php') || is_plugin_active('wordpress-seo-premium/wp-seo-premium.php');
   }



   /**
    * Checks whether or not the plugin Rank Math is active.
    *
    * @return boolean
    */
   public static function is_RankMath_active(){
      return is_plugin_active('seo-by-rank-math/rank-math.php') || is_plugin_active('seo-by-rank-math-pro/rank-math-pro.php');
   }
}