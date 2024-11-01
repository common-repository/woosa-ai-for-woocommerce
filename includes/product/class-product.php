<?php
/**
 * Product
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Product{


   /**
    * Checks whether or not the given description is empty.
    *
    * @param string $description
    * @return boolean
    */
   public static function has_empty_description($description){

      $description = wp_strip_all_tags($description);
      $description = html_entity_decode($description);
      $description = str_replace("\xC2\xA0", '', $description);
      $description = preg_replace('/\s+/', '', $description);

      return empty($description);
   }



   /**
    * Sets the SEO content.
    *
    * @param \WC_Product $product
    * @param string $description
    * @param string $focus_keyword
    * @param string $title
    * @return void
    */
   public static function set_seo_content(\WC_Product $product, $description, $focus_keyword, $title){

      //YOAST
      if(Core::is_YOAST_active()){

         if(!empty($description)){
            $product->update_meta_data('_yoast_wpseo_metadesc', $description);
         }

         if(!empty($focus_keyword)){
            $product->update_meta_data('_yoast_wpseo_focuskw', $focus_keyword);
         }

         if(!empty($title)){
            $product->update_meta_data('_yoast_wpseo_title', $title);
         }
      }

      //RANK MATH
      if(Core::is_RankMath_active()){

         if(!empty($description)){
            $product->update_meta_data('rank_math_description', $description);
         }

         if(!empty($focus_keyword)){
            $product->update_meta_data('rank_math_focus_keyword', $focus_keyword);
         }

         if(!empty($title)){
            $product->update_meta_data('rank_math_title', $title);
         }
      }

   }



   /**
    * Gets the SEO content. It will return the first active found.
    *
    * @param \WC_Product $product
    * @param string $description
    * @param string $focus_keyword
    * @return array
    */
   public static function get_seo_content(\WC_Product $product){

      $data = [
         'seo_description'   => '',
         'seo_focus_keyword' => '',
         'seo_title'         => '',
      ];

      //YOAST
      if(Core::is_YOAST_active()){
         return [
            'seo_description'   => $product->get_meta('_yoast_wpseo_metadesc'),
            'seo_focus_keyword' => $product->get_meta('_yoast_wpseo_focuskw'),
            'seo_title'         => $product->get_meta('_yoast_wpseo_title'),
         ];
      }

      //RANK MATH
      if(Core::is_RankMath_active()){
         return [
            'seo_description'   => $product->get_meta('rank_math_description'),
            'seo_focus_keyword' => $product->get_meta('rank_math_focus_keyword'),
            'seo_title'         => $product->get_meta('rank_math_title'),
         ];
      }

      return $data;
   }

}