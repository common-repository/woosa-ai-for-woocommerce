<?php
/**
 * Rest API Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Rest_API_Hook implements Interface_Hook_Register_REST_API_Endpoints {


   /**
    * The base of the REST API.
    */
   const NAMESPACE = 'wc/v3/woosa-ai';



   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init() {

      add_action('rest_api_init', [__CLASS__, 'register_endpoints']);

      add_filter('woocommerce_product_data_store_cpt_get_products_query', [__CLASS__, 'add_meta_in_product_query'], 10, 2);
   }



   /**
    * Register REST API endpoints
    *
    * @return void
    */
   public static function register_endpoints() {

      register_rest_route( self::NAMESPACE, '/authorization/status', [
         'methods' => 'GET',
         'callback' => [__CLASS__, 'process_authorization_status'],
         'permission_callback' => [__CLASS__, 'verify_nonce'],
      ]);

      register_rest_route( self::NAMESPACE, '/authorization/token', [
         'methods' => 'POST',
         'callback' => [__CLASS__, 'create_api_token'],
         'permission_callback' => [__CLASS__, 'verify_nonce'],
      ]);

      register_rest_route( self::NAMESPACE, '/authorization/token', [
         'methods' => 'DELETE',
         'callback' => [__CLASS__, 'delete_api_token'],
         'permission_callback' => [__CLASS__, 'verify_nonce'],
      ]);

      register_rest_route( self::NAMESPACE, '/categories', [
         'methods' => 'GET',
         'callback' => [__CLASS__, 'retrieve_category_list'],
         'permission_callback' => [__CLASS__, 'verify_api_token'],
      ]);

      register_rest_route(self::NAMESPACE, '/categories/(?P<id>\d+)', [
         'methods' => 'PATCH',
         'callback' => [__CLASS__, 'update_category'],
         'permission_callback' => [__CLASS__, 'verify_api_token'],
      ]);

      register_rest_route( self::NAMESPACE, '/products', [
         'methods' => 'GET',
         'callback' => [__CLASS__, 'retrieve_product_list'],
         'permission_callback' => [__CLASS__, 'verify_api_token'],
      ]);

      register_rest_route(self::NAMESPACE, '/products/(?P<id>\d+)', [
         'methods' => 'PATCH',
         'callback' => [__CLASS__, 'update_product'],
         'permission_callback' => [__CLASS__, 'verify_api_token'],
      ]);

      register_rest_route( self::NAMESPACE, '/statistics', [
         'methods' => 'GET',
         'callback' => [__CLASS__, 'retrieve_statistics'],
         'permission_callback' => [__CLASS__, 'verify_api_token'],
      ]);

   }



   /**
    * Checks for security nonce.
    *
    * @param \WP_REST_Request $request
    * @return boolean
    */
   public static function verify_nonce($request){

      $local_nonce = Transient::get('connection_nonce');
      $nonce       = $request->get_param('nonce');

      if(empty($local_nonce) || $nonce !== $local_nonce){
         return false;
      }

      return true;
   }



   /**
    * Checks for API token.
    *
    * @param \WP_REST_Request $request
    * @return boolean
    */
   public static function verify_api_token($request){

      $local_token = Option::get('api_token');
      $token       = preg_replace('/Bearer\s(\S+)/', '$1', $request->get_header('authorization'));

      if($token !== $local_token){
         return false;
      }

      return true;
   }



   /**
    * Validate the Kaufland  signature
    *
    * @param \WP_REST_Request $request
    * @return string|void
    */
   public static function process_authorization_status($request){

      $required_params = [
         'nonce',
         'action',
         'result',
      ];

      $action  = $request->get_param('action');
      $result  = $request->get_param('result');
      $message = empty($request->get_param('message')) ? __('Unknow error. Please try again.', 'woosa-ai-for-woocommerce') : $request->get_param('message');

      //check required parameters
      foreach($required_params as $param_key){

         $param_value = $request->get_param($param_key);

         if(empty($param_value)){
            return new \WP_Error( 'invalid_parameter', "The parameter `$param_key` is missing or empty.", [ 'status' => 400 ] );
         }
      }

      //invalid `action` value
      if( ! in_array($action, ['connect', 'disconnect']) ){
         return new \WP_Error( 'invalid_parameter', 'The parameter `action` has invalid value.', [ 'status' => 403 ] );
      }

      //invalid `result` value
      if( ! in_array($result, ['success', 'error']) ){
         return new \WP_Error( 'invalid_parameter', 'The parameter `result` has invalid value.', [ 'status' => 403 ] );
      }

      if('success' === $result){

         $ma = new Module_Authorization();
         $ma->set_env($ma->get_env());

         if('connect' === $action){
            $ma->connect();
         }

         if('disconnect' === $action){
            $ma->disconnect();
         }

         Option::delete('connection_error');
      }

      if('error' === $result){
         $message = 'connect' === $action ? sprintf('Granting the access has failed with the following message "%s"', $message) : sprintf('Revoking the access has failed with the following message "%s"', $message);
         Option::set('connection_error', $message);
      }

      wp_redirect(add_query_arg([
         'tab' => 'authorization',
         'wsa_nonce' => wp_create_nonce('authorization')
      ], SETTINGS_URL));

      exit;
   }



   /**
    * Generates the API token.
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function create_api_token($request){

      $token = wp_generate_uuid4();

      Option::set('api_token', $token);

      return new \WP_REST_Response([
         'token' => $token
      ], 201);
   }



   /**
    * Deletes the API token.
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function delete_api_token($request){

      Option::delete('api_token');

      return new \WP_REST_Response(null, 204);
   }



   /**
    * Addes custom meta query support.
    *
    * @param array $query
    * @param array $query_vars
    * @return array
    */
   public static function add_meta_in_product_query($query, $query_vars){

      if ( ! empty($query_vars[Util::prefix('available_for_app')] )) {
         $query['meta_query'][] = [
            'key'   => Util::prefix('available_for_app'),
            'value' => esc_attr( $query_vars[Util::prefix('available_for_app')] ),
         ];
      }

      return $query;
   }



   /**
    * Provides the list of product categories.
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function retrieve_category_list($request){

      Rest_API::set_debug_log($request);

      $terms = [];
      $page  = empty($request->get_param('page')) ? 1 : (int) $request->get_param('page');
      $limit = empty($request->get_param('limit')) ? 25 : (int) $request->get_param('limit');

      $taxonomy   = 'product_cat';
      $offset     = $limit * ($page -1);
      $meta_query = [
         [
            'key' => Util::prefix('available_for_app'),
            'value' => 'yes'
         ]
      ];

      $total = wp_count_terms( array(
         'taxonomy'   => $taxonomy,
         'hide_empty' => false,
         'meta_query' => $meta_query,
      ));

      if(is_wp_error($total)){
         return new \WP_Error($total->get_error_code(), $total->get_error_message(), ['status' => 500]);
      }

      $results  = get_terms([
         'taxonomy'   => $taxonomy,
         'number'     => $limit,
         'hide_empty' => false,
         'orderby'    => 'ID',
         'order'      => 'DESC',
         'offset'     => $offset,
         'meta_query' => $meta_query,
      ]);

      if(is_wp_error($results)){
         return new \WP_Error($total->get_error_code(), $results->get_error_message(), ['status' => 500]);
      }

      foreach($results as $term){

         $ai_settings = null;
         $ai_results  = null;
         $status      = get_term_meta($term->term_id, Util::prefix('status'),  true);
         $image_url   = wp_get_attachment_image_url(get_term_meta($term->term_id, 'thumbnail_id', true), 'full');

         if($status){

            $settings = array_filter((array) get_term_meta($term->term_id, Util::prefix('settings'), true));

            $ai_settings = [
               'focus_keyword'   => Util::array($settings)->get('focus_keyword', ''),
               'synonyms'        => Util::array($settings)->get('synonyms', ''),
               'must_used_words' => Util::array($settings)->get('must_used_words', ''),
               'tone_of_voice'   => Util::array($settings)->get('tone_of_voice', ''),
               'audience'        => Util::array($settings)->get('audience', ''),
               'output_language' => Util::array($settings)->get('output_language', ''),
               'min_chars'       => Util::array($settings)->get('min_chars', 0),
               'max_chars'       => Util::array($settings)->get('max_chars', 0),
               'content_type'    => [
                  'description'       => Util::array($settings)->get('content_type/description', false),
                  'seo_description'   => Util::array($settings)->get('content_type/seo_description', false),
                  'seo_focus_keyword' => Util::array($settings)->get('content_type/seo_focus_keyword', false),
                  'seo_title'         => Util::array($settings)->get('content_type/seo_title', false),
               ],
            ];

            $ai_results = [
               'status'       => $status,
               'error'        => get_term_meta($term->term_id, Util::prefix('error'), true),
               'generated_at' => get_term_meta($term->term_id, Util::prefix('generated_at'), true),
               'content' => array_merge([
                  'description' => $term->description,
               ], Category::get_seo_content($term->term_id)),
            ];
         }

         $inital_data = DB_Table::get_entry($term->term_id);

         $terms[] = [
            'id'          => $term->term_id,
            'name'        => $term->name,
            'description' => Util::array($inital_data)->get('description', $term->description),
            'link'        => get_term_link($term->term_id, $taxonomy),
            'image_url'   => empty($image_url) ? null : $image_url,
            'ai'          => [
               'settings' => $ai_settings,
               'results'  => $ai_results,
            ]
         ];
      }

      return new \WP_REST_Response([
         'data'  => $terms,
         'total' => (int) $total,
      ], 200);
   }



   /**
    * Updates the given category.
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function update_category($request){

      Rest_API::set_debug_log($request);

      $valid_params = Rest_API::valid_required_params($request, 'category');

      if(is_wp_error( $valid_params )){
         return new \WP_Error($valid_params->get_error_code(), $valid_params->get_error_message(), $valid_params->get_error_data() );
      }

      $term = get_term( $request->get_param('id') );

      if(empty($term)){
         return new \WP_Error('invalid_category', 'The category could not be found.', [ 'status' => 404 ] );
      }

      $inital_data = DB_Table::get_entry($term->term_id);

      if(empty($inital_data)){

         $inital_data = DB_Table::create_entry([
            'object_id'         => $term->term_id,
            'type'              => 'category',
            'name'              => $term->name,
            'description'       => $term->description,
         ]);

         if(is_wp_error( $inital_data )){
            return new \WP_Error($inital_data->get_error_code(), $inital_data->get_error_message(), ['status' => 500]);
         }
      }

      $status            = Util::array($request->get_param('results'))->get('status');
      $error             = Util::array($request->get_param('results'))->get('error');
      $generated_at      = Util::array($request->get_param('results'))->get('generated_at');
      $description       = Util::array($request->get_param('results'))->get('content/description');
      $seo_description   = Util::array($request->get_param('results'))->get('content/seo_description');
      $seo_focus_keyword = Util::array($request->get_param('results'))->get('content/seo_focus_keyword');
      $seo_title         = Util::array($request->get_param('results'))->get('content/seo_title');

      //meta
      update_term_meta($term->term_id, Util::prefix('status'), $status);
      update_term_meta($term->term_id, Util::prefix('error'), $error);
      update_term_meta($term->term_id, Util::prefix('generated_at'), $generated_at);
      update_term_meta($term->term_id, Util::prefix('settings'), $request->get_param('settings'));

      $args = [];
      $taxonomy = 'product_cat';

      //content
      if(!empty($description)){
         $args['description'] = $description;
      }

      //SEO
      Category::set_seo_content($term->term_id, $seo_description, $seo_focus_keyword, $seo_title);

      //allow HTML in term description
      remove_filter('pre_term_description', 'wp_filter_kses');

      wp_update_term($term->term_id, $taxonomy, $args);

      return new \WP_REST_Response(null, 204);
   }



   /**
    * Provides the list of products
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function retrieve_product_list($request){

      Rest_API::set_debug_log($request);

      $products = [];
      $page     = empty($request->get_param('page')) ? 1 : (int) $request->get_param('page');
      $limit    = empty($request->get_param('limit')) ? 25 : (int) $request->get_param('limit');

      $query = wc_get_products([
         'limit'    => $limit,
         'page'     => $page,
         'paginate' => true,
         'orderby'  => 'ID',
         'order'    => 'DESC',
         Util::prefix('available_for_app') => 'yes',
      ]);

      foreach($query->products as $product){

         $ai_settings = null;
         $ai_results  = null;
         $status      = $product->get_meta(Util::prefix('status'));
         $image_url   = wp_get_attachment_image_url($product->get_image_id(), 'full');

         if($status){

            $settings = array_filter((array) $product->get_meta(Util::prefix('settings')));

            $ai_settings = [
               'focus_keyword'   => Util::array($settings)->get('focus_keyword', ''),
               'synonyms'        => Util::array($settings)->get('synonyms', ''),
               'must_used_words' => Util::array($settings)->get('must_used_words', ''),
               'tone_of_voice'   => Util::array($settings)->get('tone_of_voice', ''),
               'audience'        => Util::array($settings)->get('audience', ''),
               'output_language' => Util::array($settings)->get('output_language', ''),
               'min_chars'       => Util::array($settings)->get('min_chars', 0),
               'max_chars'       => Util::array($settings)->get('max_chars', 0),
               'content_type'    => [
                  'description'       => Util::array($settings)->get('content_type/description', false),
                  'short_description' => Util::array($settings)->get('content_type/short_description', false),
                  'seo_description'   => Util::array($settings)->get('content_type/seo_description', false),
                  'seo_focus_keyword' => Util::array($settings)->get('content_type/seo_focus_keyword', false),
                  'seo_title'         => Util::array($settings)->get('content_type/seo_title', false),
               ],
            ];

            $ai_results = [
               'status'       => $status,
               'error'        => $product->get_meta(Util::prefix('error')),
               'generated_at' => $product->get_meta(Util::prefix('generated_at')),
               'content' => array_merge([
                  'description'       => $product->get_description(),
                  'short_description' => $product->get_short_description(),
               ], Product::get_seo_content($product)),
            ];
         }

         $inital_data = DB_Table::get_entry($product->get_id());

         $products[] = [
            'id'          => $product->get_id(),
            'name'        => $product->get_name(),
            'description' => Util::array($inital_data)->get('description', $product->get_description()),
            'link'        => $product->get_permalink(),
            'image_url'   => empty($image_url) ? null : $image_url,
            'ai'          => [
               'settings' => $ai_settings,
               'results'  => $ai_results,
            ]
         ];
      }

      return new \WP_REST_Response([
         'data'  => $products,
         'total' => (int) $query->total,
      ], 200);
   }



   /**
    * Updates the given product.
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function update_product($request){

      Rest_API::set_debug_log($request);

      $valid_params = Rest_API::valid_required_params($request);

      if(is_wp_error( $valid_params )){
         return new \WP_Error($valid_params->get_error_code(), $valid_params->get_error_message(), $valid_params->get_error_data() );
      }

      $product = wc_get_product( $request->get_param('id') );

      if( ! $product instanceof \WC_Product ){
         return new \WP_Error( 'invalid_product', 'The product could not be found.', [ 'status' => 404 ] );
      }

      $inital_data = DB_Table::get_entry($product->get_id());

      if(empty($inital_data)){

         $inital_data = DB_Table::create_entry([
            'object_id'         => $product->get_id(),
            'type'              => 'product',
            'name'              => $product->get_name(),
            'short_description' => $product->get_short_description(),
            'description'       => $product->get_description(),
         ]);

         if(is_wp_error( $inital_data )){
            return new \WP_Error($inital_data->get_error_code(), $inital_data->get_error_message(), ['status' => 500]);
         }
      }

      $status            = Util::array($request->get_param('results'))->get('status');
      $error             = Util::array($request->get_param('results'))->get('error');
      $generated_at      = Util::array($request->get_param('results'))->get('generated_at');
      $description       = Util::array($request->get_param('results'))->get('content/description');
      $short_description = Util::array($request->get_param('results'))->get('content/short_description');
      $seo_description   = Util::array($request->get_param('results'))->get('content/seo_description');
      $seo_focus_keyword = Util::array($request->get_param('results'))->get('content/seo_focus_keyword');
      $seo_title         = Util::array($request->get_param('results'))->get('content/seo_title');

      //meta
      $product->update_meta_data(Util::prefix('status'), $status);
      $product->update_meta_data(Util::prefix('error'), $error);
      $product->update_meta_data(Util::prefix('generated_at'), $generated_at);
      $product->update_meta_data(Util::prefix('settings'), $request->get_param('settings'));

      //content
      if(!empty($description)){
         $product->set_description($description);
      }

      if(!empty($short_description)){
         $product->set_short_description($short_description);
      }

      //SEO
      Product::set_seo_content($product, $seo_description, $seo_focus_keyword, $seo_title);

      $product->save();

      return new \WP_REST_Response(null, 204);
   }



   /**
    * Provides the general statistics.
    *
    * @param \WP_REST_Request $request
    * @return string
    */
   public static function retrieve_statistics($request){

      global $wpdb;

      Rest_API::set_debug_log($request);

      $meta_key1 = Util::prefix('available_for_app');
      $meta_key2 = Util::prefix('status');

      $products = $wpdb->get_row(
         $wpdb->prepare(
            "SELECT
                SUM(CASE WHEN pm1.meta_key = %s AND pm1.meta_value = 'yes' THEN 1 ELSE 0 END) AS total,
                SUM(CASE WHEN pm1.meta_key = %s AND pm1.meta_value = 'yes' AND pm2.meta_key = %s AND pm2.meta_value = 'published' THEN 1 ELSE 0 END) AS generated
            FROM {$wpdb->posts} p
               LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = %s
               LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = %s
            WHERE p.post_type = 'product' AND p.post_status = 'publish'",
            $meta_key1,
            $meta_key1,
            $meta_key2,
            $meta_key1,
            $meta_key2
         )
      );

      $terms = $wpdb->get_row(
         $wpdb->prepare(
            "SELECT
                SUM(CASE WHEN tm1.meta_key = %s AND tm1.meta_value = 'yes' THEN 1 ELSE 0 END) AS total,
                SUM(CASE WHEN tm1.meta_key = %s AND tm1.meta_value = 'yes' AND tm2.meta_key = %s AND tm2.meta_value = 'published' THEN 1 ELSE 0 END) AS generated
            FROM {$wpdb->terms} t
               LEFT JOIN {$wpdb->termmeta} tm1 ON t.term_id = tm1.term_id AND tm1.meta_key = %s
               LEFT JOIN {$wpdb->termmeta} tm2 ON t.term_id = tm2.term_id AND tm2.meta_key = %s
            WHERE 1=1",
            $meta_key1,
            $meta_key1,
            $meta_key2,
            $meta_key1,
            $meta_key2
         )
      );

      return new \WP_REST_Response([
         'generated' => (int) $products->generated + (int) $terms->generated,
         'total' => (int) $products->total + (int) $terms->total,
      ], 200);
   }
}