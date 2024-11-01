<?php
/**
 * Settings Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Settings_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\module\settings\menu_icon', [__CLASS__, 'define_menu_icon']);
      add_filter(PREFIX . '\module\settings\menu_position', [__CLASS__, 'define_menu_position']);
      add_filter(PREFIX . '\module\settings\page\logo_url', [__CLASS__, 'define_logo_url']);
   }



   /**
    * Defines the SVG as Settings menu icon.
    *
    * @param string $icon
    * @return string
    */
   public static function define_menu_icon($icon){

      $icon = 'data:image/svg+xml;base64,'.base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path fill="#a2aab2" d="M234.7 42.7L197 56.8c-3 1.1-5 4-5 7.2s2 6.1 5 7.2l37.7 14.1L248.8 123c1.1 3 4 5 7.2 5s6.1-2 7.2-5l14.1-37.7L315 71.2c3-1.1 5-4 5-7.2s-2-6.1-5-7.2L277.3 42.7 263.2 5c-1.1-3-4-5-7.2-5s-6.1 2-7.2 5L234.7 42.7zM461.4 48L496 82.6 386.2 192.3l-34.6-34.6L461.4 48zM80 429.4L317.7 191.7l34.6 34.6L114.6 464 80 429.4zM427.4 14.1L46.1 395.4c-18.7 18.7-18.7 49.1 0 67.9l34.6 34.6c18.7 18.7 49.1 18.7 67.9 0L529.9 116.5c18.7-18.7 18.7-49.1 0-67.9L495.3 14.1c-18.7-18.7-49.1-18.7-67.9 0zM7.5 117.2C3 118.9 0 123.2 0 128s3 9.1 7.5 10.8L64 160l21.2 56.5c1.7 4.5 6 7.5 10.8 7.5s9.1-3 10.8-7.5L128 160l56.5-21.2c4.5-1.7 7.5-6 7.5-10.8s-3-9.1-7.5-10.8L128 96 106.8 39.5C105.1 35 100.8 32 96 32s-9.1 3-10.8 7.5L64 96 7.5 117.2zm352 256c-4.5 1.7-7.5 6-7.5 10.8s3 9.1 7.5 10.8L416 416l21.2 56.5c1.7 4.5 6 7.5 10.8 7.5s9.1-3 10.8-7.5L480 416l56.5-21.2c4.5-1.7 7.5-6 7.5-10.8s-3-9.1-7.5-10.8L480 352l-21.2-56.5c-1.7-4.5-6-7.5-10.8-7.5s-9.1 3-10.8 7.5L416 352l-56.5 21.2z"/></svg>');

      return $icon;
   }



   /**
    * Defines the position of Settings menu.
    *
    * @param string $icon
    * @return string
    */
   public static function define_menu_position($position){

      $position = 3;

      return $position;
   }



   /**
    * Defines the logo url of Settings page.
    *
    * @param string $url
    * @return string
    */
   public static function define_logo_url($url){
      return untrailingslashit(plugin_dir_url(__FILE__)) . '/assets/images/woosa-logo.png';
   }

}