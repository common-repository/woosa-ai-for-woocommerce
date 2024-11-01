<?php
/**
 * Rest API
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Rest_API {


   /**
    * Sets debug log for income requests.
    *
    * @param \WP_REST_Request $request
    * @return void
    */
   public static function set_debug_log(\WP_REST_Request $request){

      if(DEBUG){
         Util::log()->debug([
            'title' => '==== INCOME REQUEST ====',
            'message' => 'This is an income request.',
            'data' => [
               'endpoint' => $request->get_route(),
               'method' => $request->get_method(),
               'headers' => array_map(function($value) {
                  return is_array($value) ? $value[0] : $value;
               }, $request->get_headers()),
               'body' => $request->get_body(),
            ]
         ]);
      }
   }



   /**
    * Checks whether or not the required params are valid.
    *
    * @param \WP_REST_Request $request
    * @param string $type
    * @return void|\WP_Error
    */
   public static function valid_required_params($request, $type = 'product'){

      $params = $request->get_params();

      $valid_tones_of_voice = [
         'informal',
         'formal',
         'casual',
         'enthusiastic',
         'funny',
         'respectful'
      ];
      $required_params = [
         'settings/focus_keyword',
         'settings/synonyms',
         'settings/must_used_words',
         'settings/tone_of_voice',
         'settings/audience',
         'settings/output_language',
         'settings/min_chars',
         'settings/max_chars',
         'settings/content_type/description',
         'settings/content_type/short_description',
         'settings/content_type/seo_description',
         'settings/content_type/seo_focus_keyword',
         'settings/content_type/seo_title',
         'results/status',
         'results/generated_at',
      ];

      //remove the short description validation
      if('category' === $type){
         unset($required_params[array_search('settings/content_type/short_description', $required_params)]);
      }

      foreach ($required_params as $param) {
         $keys = explode('/', $param);
         $temp = $params;

         foreach ($keys as $key) {
            if ( ! isset($temp[$key]) || '' === $temp[$key] ) {
               return new \WP_Error('invalid_parameter', "The parameter '$param' is missing or empty.", ['status' => 400]);
            }

            if ('settings/tone_of_voice' == $param) {
               if (!in_array(Util::array($params)->get($param), $valid_tones_of_voice)) {
                  return new \WP_Error('invalid_value', "The parameter '$param' has an invalid value. Allowed values are: " . implode(', ', $valid_tones_of_voice), ['status' => 400]);
               }
            }

            if (
               'settings/content_type/description' == $param ||
               'settings/content_type/short_description' == $param ||
               'settings/content_type/seo_description' == $param ||
               'settings/content_type/seo_focus_keyword' == $param ||
               'settings/content_type/seo_title' == $param
            ) {
               if ( ! is_bool(Util::array($params)->get($param)) ) {
                  return new \WP_Error('invalid_value', "The parameter '$param' has an invalid value. Allowed value is boolean.", ['status' => 400]);
               }
            }

            if (
               'settings/min_chars' == $param ||
               'settings/max_chars' == $param
            ) {
               if ( ! is_int(Util::array($params)->get($param)) ) {
                  return new \WP_Error('invalid_value', "The parameter '$param' has an invalid value. Allowed value is integer.", ['status' => 400]);
               }
            }

            $temp = $temp[$key];
         }
      }
   }
}