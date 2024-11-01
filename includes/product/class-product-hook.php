<?php
/**
 * Product Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Product_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter('is_protected_meta', [__CLASS__, 'protect_special_meta'], 10, 3);
   }



   /**
    * Hides special meta keys.
    *
    * @param bool $protected
    * @param string $meta_key
    * @param string $meta_type
    * @return bool
    */
   public static function protect_special_meta($protected, $meta_key, $meta_type){

      $special_keys = [
         Util::prefix('available_for_app'),
         Util::prefix('error'),
         Util::prefix('status'),
         Util::prefix('generated_at'),
         Util::prefix('settings'),
      ];

      if(in_array($meta_key, $special_keys)){
         $protected = true;
      }

      return $protected;
   }
}