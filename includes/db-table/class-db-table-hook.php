<?php
/**
 * DB Table Hook
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class DB_Table_Hook implements Interface_Hook{


   /**
    * Initiates the hooks.
    *
    * @return void
    */
   public static function init(){

      add_action(PREFIX . '\core\state\activated', [DB_Table::class, 'create_table']);
      add_action(PREFIX . '\core\state\uninstalled', [__CLASS__, 'delete_table']);

      add_action('init', [__CLASS__, 'update_initial_products_table']);
   }



   /**
    * In case the `remove config` setting is enabled then delete DB table.
    *
    * @return void
    */
   public static function delete_table(){

      if('yes' === Option::get('remove_config')){
         DB_Table::delete_table();
      }
   }



   /**
    * Updates the table `initial_products`.
    *
    * @return void
    */
   public static function update_initial_products_table(){

      global $wpdb;

      $old_table = $wpdb->prefix . Util::prefix('initial_products');
      $new_table = $wpdb->prefix . Util::prefix('initial_content');

      if(Util_DB_Table::is_created($old_table) && ! Util_DB_Table::is_created($new_table)){

         $wpdb->query(
            $wpdb->prepare(
               "ALTER TABLE %i
               RENAME TO %i,
               CHANGE COLUMN product_id object_id BIGINT(20) NOT NULL,
               ADD COLUMN type VARCHAR(30) NOT NULL AFTER object_id",
               $old_table,
               $new_table,
            )
         );
         $wpdb->query(
            $wpdb->prepare(
               "UPDATE %i SET type = %s",
               $new_table,
               'product'
            )
         );
      }
   }
}