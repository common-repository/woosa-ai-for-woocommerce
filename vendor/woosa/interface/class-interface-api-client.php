<?php
/**
 * Interface API Client
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


interface Interface_API_Client{


   /**
    * The API version.
    *
    * @return string
    */
   public function version();



   /**
    * The base API url.
    *
    * @param string $endpoint
    * @param bool $use_service - whether or not the endpoint should contain the service
    * @return string
    */
   public function base_url(string $endpoint, bool $use_service = true);



   /**
    * The list of request headers.
    *
    * @param array $items
    * @return array
    */
   public function headers(array $items = []);



   /**
    * Checks whether or not the plugin is authorized/configured to send requests.
    *
    * @return boolean
    */
   public function is_authorized();



   /**
    * Checks whether or not the test mode is enabled.
    *
    * @return bool
    */
   public function is_test_mode();



   /**
    * Sends the request to retrieve the access token by using the `client_id` and `client_secret`.
    *
    * @return object
    */
   public function authorize();



   /**
    * Revokes the authorization.
    *
    * @return void
    */
   public function revoke();



   /**
    * Retrieves the access token (or API key).
    *
    * @return string
    */
   public function get_access_token();
}