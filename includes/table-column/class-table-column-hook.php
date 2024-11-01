<?php
/**
 * Table Column Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Table_Column_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_filter(PREFIX . '\table_column\columns', [__CLASS__, 'table_columns']);

   }



   /**
    * Define the table columns.
    *
    * @param array $items
    * @return array
    */
   public static function table_columns($items){

      $items[PREFIX . '_product_status'] = [
         'label'        => __('Woosa AI', 'woosa-ai-for-woocommerce'),
         'post_type'    => ['product'],
         'after_column' => 'product_cat',
         'callback'     => [__CLASS__, 'handle_table_column_status'],
      ];

      $items[PREFIX . '_product_cat_status'] = [
         'label'        => __('Woosa AI', 'woosa-ai-for-woocommerce'),
         'post_type'    => ['product_cat'],
         'after_column' => 'description',
         'callback'     => [__CLASS__, 'handle_table_column_status'],
      ];

      return $items;
   }



   /**
    * Renders the content of table column `{prefix}_product_status` and `{prefix}_product_cat_status`.
    *
    * @param int $object_id
    * @param string $column
    * @return string
    */
   public static function handle_table_column_status($object_id, $column){

      $available = get_post_meta($object_id, Util::prefix('available_for_app'), true);
      $error     = get_post_meta($object_id, Util::prefix('error'), true);

      if(Util::prefix('product_cat_status') === $column){
         $available = get_term_meta($object_id, Util::prefix('available_for_app'), true);
         $error     = get_term_meta($object_id, Util::prefix('error'), true);
      }

      if(!empty($error)){
         echo '<span class="woocommerce-help-tip ' . esc_attr(PREFIX . '-tip-error') . '" data-tip="' . esc_html($error) . '"></span>';
         return;
      }

      if($available){
         echo '<div class="' . esc_attr(PREFIX . '-style') . '"><span class="dashicons dashicons-yes text-color--success" title="' . esc_html(__('This is pushed to Woosa AI', 'woosa-ai-for-woocommerce')) . '"></span></div>';
         return;
      }

      echo '–';
   }
}