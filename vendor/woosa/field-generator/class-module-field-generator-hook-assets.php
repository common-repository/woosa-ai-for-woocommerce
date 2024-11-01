<?php
/**
 * Module Field Generator Hook Assets
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Module_Field_Generator_Hook_Assets implements Interface_Hook_Assets{


   /**
    * Initiates.
    *
    * @return void
    */
   public static function init(){

      add_action('admin_enqueue_scripts', [__CLASS__ , 'admin_assets']);
   }



   /**
    * Enqueues public CSS/JS files.
    *
    * @return void
    */
   public static function public_assets(){}



   /**
    * Enqueues admin CSS/JS files.
    *
    * @return void
    */
   public static function admin_assets(){

      Util::enqueue_scripts([
         [
            'name' => 'quill',
            'css' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/css/',
            ],
            'js' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/js/',
               'dependency' => ['jquery'],
            ],
         ],
         [
            'name' => 'field-generator',
            'css' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/css/',
            ],
         ],
         [
            'name' => 'module-field-generator',
            'js' => [
               'path' => untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/js/',
               'dependency' => ['jquery', PREFIX . '-quill'],
               'localize' => true,
            ],
         ],
      ]);
   }
}