<?php
/**
 * Authorization Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Authorization_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action('admin_init', [__CLASS__, 'process_connection_action']);

   }



   /**
    * Processes the connection action.
    *
    * @return void
    */
   public static function process_connection_action(){

      $nonce = sanitize_text_field($_GET['wsa_nonce'] ?? '');
      $page  = sanitize_text_field($_GET['page'] ?? '');
      $tab   = sanitize_text_field($_GET['tab'] ?? '');

      if( ! wp_verify_nonce('authorization', $nonce) && SETTINGS_TAB_ID !== $page && 'authorization' !== $tab ){
         return;
      }

      $connection_type = sanitize_text_field($_GET['connection_type'] ?? '');
      $redirect_url    = Service_API::init()->get_connection_url($connection_type);

      if( empty($redirect_url) ){
         return;
      }

      $nonce = wp_generate_uuid4();

      Transient::set('connection_nonce', $nonce, MINUTE_IN_SECONDS * 2);

      $redirect_url = add_query_arg([
         'nonce' => $nonce,
      ], $redirect_url);

      wp_redirect($redirect_url);

      exit;
   }
}