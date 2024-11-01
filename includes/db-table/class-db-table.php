<?php
/**
 * DB Table
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class DB_Table  implements Interface_DB_Table{


   /**
    * Retrieves the database table name.
    *
    * @return string
    */
   public static function get_table_name() {

      global $wpdb;

      return $wpdb->prefix . Util::prefix('initial_content');
   }



   /**
    * Creates the database table if not exists.
    *
    * @return void
    */
   public static function create_table(){

      $table_name = self::get_table_name();

      if( ! Util_DB_Table::is_created($table_name) ){

         Util_DB_Table::create($table_name, "
            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            object_id bigint(20) NOT NULL,
            type varchar(50) NOT NULL,
            name text NOT NULL,
            short_description text NOT NULL,
            description longtext NOT NULL
         ");
      }
   }



   /**
    * Deletes the database table.
    *
    * @return void
    */
   public static function delete_table(){
      Util_DB_Table::delete( self::get_table_name() );
   }



   public static function get_entries(array $args){}



   /**
    * Retrieves a single table entry.
    *
    * @param int $object_id
    * @return array|null
    */
   public static function get_entry($object_id){

      global $wpdb;

      $result = $wpdb->get_row(
         $wpdb->prepare(
            "SELECT * FROM %i WHERE object_id = %s",
            self::get_table_name(),
            $object_id,
         ), 'ARRAY_A');

      return $result;
   }



   public static function create_entries(array $args){}



   /**
    * Creates a single table entry.
    *
    * @param array $args
    * [
    *    'object_id'         => '',
    *    'type'              => '',
    *    'name'              => '',
    *    'short_description' => '',
    *    'description'       => '',
    * ]
    * @return void|\WP_Error
    */
   public static function create_entry(array $args){

      global $wpdb;

      if(array_key_exists('object_id', $args)){
         $columns['object_id'] = $args['object_id'];
      }

      if(array_key_exists('type', $args)){
         $columns['type'] = $args['type'];
      }

      if(array_key_exists('name', $args)){
         $columns['name'] = $args['name'];
      }

      if(array_key_exists('short_description', $args)){
         $columns['short_description'] = $args['short_description'];
      }

      if(array_key_exists('description', $args)){
         $columns['description'] = $args['description'];
      }

      if(empty($columns)){
         return new \WP_Error('missing_args', 'No arguments provided for creating the entry.', [
            'args' => $args
         ]);
      }

      $result = $wpdb->insert(
         self::get_table_name(),
         $columns,
      );

      if($result === false){

         return new \WP_Error('query_failed', 'Creating the entry failed.', [
            'error' => $wpdb->last_error,
            'query' => $wpdb->last_query,
         ]);
      }
   }



   public static function update_entries(array $args){}



   public static function update_entry($id, array $args){}



   public static function delete_entries(array $args){}



   /**
    * Deletes a single table entry.
    *
    * @param int $id
    * @return void|\WP_Error
    */
   public static function delete_entry($id){

      global $wpdb;

      $result = $wpdb->delete(
         self::get_table_name(),
         [
            'id' => $id
         ]
      );

      if($result === false){

         return new \WP_Error('query_failed', 'Deleting the entry failed.', [
            'error' => $wpdb->last_error,
            'query' => $wpdb->last_query,
         ]);
      }
   }
}