<?php
/**
 * Service Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Service_API implements Interface_API_Client {


   /**
    * @var string The api base url
    */
   const API_BASE = 'https://app.woosa.ai';


   /**
    * @var string The api base url for development
    */
   const API_BASE_DEVELOPMENT = 'https://app-dev.woosa.ai';


   public function version() {}

   public function authorize() {}

   public function revoke() {}

   public function headers(array $items = []) {}

   public function is_authorized() {}

   public function get_access_token() {}



   /**
    * Get the base url for API
    *
    * @param string $endpoint
    * @param bool $use_service
    * @param bool $is_use_version
    * @return string
    */
   public function base_url(string $endpoint, bool $use_service = true) {

      if($this->is_test_mode()){
         return trailingslashit(self::API_BASE_DEVELOPMENT) . ltrim($endpoint, '/');
      }

      return trailingslashit(self::API_BASE) . ltrim($endpoint, '/');
   }



   /**
    * Checks whether or not the test mode is enabled.
    *
    * @return bool
    */
   public function is_test_mode() {
      return defined('\WOOSA_TEST') && \WOOSA_TEST;
   }



   /**
    * Init self class.
    *
    * @return Service_API
    */
   public static function init(){
      return new self;
   }



   /**
    * Retrieves the connection URL.
    *
    * @param string $type
    * @return string|void
    */
   public function get_connection_url(string $type){

      if('connect' === $type){
         return add_query_arg([
            'url' => home_url(),
         ], $this->base_url('woocommerce/connect'));
      }

      if('disconnect' === $type){
         return $this->base_url('woocommerce/disconnect');
      }
   }
}