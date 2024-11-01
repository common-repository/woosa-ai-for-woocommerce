<?php
/**
 * Action Bulker Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Action_Bulker_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\action_bulker\actions', [__CLASS__, 'add_bulk_actions']);

   }



   /**
    * Adds extra bulk actions.
    *
    * @return array
    */
   public static function add_bulk_actions($items){

      $items[PREFIX . '_push_to_app'] = [
         'label'        => __('WoosaAI: Push to', 'woosa-ai-for-woocommerce'),
         'post_type'    => ['product', 'product_cat'],
         'callback'     => [__CLASS__, 'push_to_app'],
         'bulk_perform' => false,
         'schedulable'  => false,
         'task'         => false,
      ];

      $items[PREFIX . '_remove_from_app'] = [
         'label'        => __('WoosaAI: Remove from ', 'woosa-ai-for-woocommerce'),
         'post_type'    => ['product', 'product_cat'],
         'callback'     => [__CLASS__, 'remove_from_app'],
         'bulk_perform' => false,
         'schedulable'  => false,
         'task'         => false,
      ];

      return $items;
   }



   /**
    * Marks the product (or product cat) to be available for the application.
    *
    * @param int $object_id
    * @return void
    */
   public static function push_to_app($object_id){

      $product = wc_get_product($object_id);

      if($product instanceof \WC_Product){

         $product->delete_meta_data(Util::prefix('error'));
         $product->update_meta_data(Util::prefix('available_for_app'), 'yes');
         $product->save_meta_data();

         return;
      }

      $term = get_term($object_id);

      if(isset($term->term_id)){

         delete_term_meta($term->term_id, Util::prefix('error'));
         update_term_meta($term->term_id, Util::prefix('available_for_app'), 'yes');

         return;
      }
   }



   /**
    * Marks the product (or product cat) NOT to be available for the application.
    *
    * @param int $object_id
    * @return void
    */
   public static function remove_from_app($object_id){

      $product = wc_get_product($object_id);

      if($product instanceof \WC_Product){

         $product->delete_meta_data(Util::prefix('error'));
         $product->delete_meta_data(Util::prefix('available_for_app'));
         $product->save_meta_data();

         return;
      }

      $term = get_term($object_id);

      if(isset($term->term_id)){

         delete_term_meta($term->term_id, Util::prefix('error'));
         delete_term_meta($term->term_id, Util::prefix('available_for_app'));

         return;
      }
   }



   /**
    * Adds bulk actions for terms.
    *
    * @param array $bulk_actions
    * @return array
    */
   public static function add_terms_bulk_actions($bulk_actions) {

      $bulk_actions[PREFIX . '_push_to_app'] = __('WoosaAI: Push to', 'woosa-ai-for-woocommerce');
      $bulk_actions[PREFIX . '_remove_from_app'] = __('WoosaAI: Remove from ', 'woosa-ai-for-woocommerce');

      return $bulk_actions;
   }


   public static function handle_terms_bulk_actions($redirect_to, $action, $ids){

      if ($action !== 'custom_action') {
         return $redirect_to;
      }

      // Example: Process the IDs and perform actions
      foreach ($ids as $id) {
         // Example action: Update term meta, delete terms, or any custom processing
         // Replace with your custom logic
         // update_term_meta($id, 'my_custom_meta_key', 'my_custom_value');
      }

      $redirect_to = add_query_arg('bulk_custom_action_done', count($ids), $redirect_to);
      return $redirect_to;
   }
}