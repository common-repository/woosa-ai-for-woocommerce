<?php
/**
 * Category
 *
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


class Category{


   /**
    * Sets the SEO content.
    *
    * @param string $category_id
    * @param string $description
    * @param string $focus_keyword
    * @param string $title
    * @return void
    */
   public static function set_seo_content($category_id, $description, $focus_keyword, $title){

      //YOAST
      if(Core::is_YOAST_active()){

         if(!empty($description)){
            update_term_meta($category_id, '_yoast_wpseo_metadesc', $description);
         }

         if(!empty($focus_keyword)){
            update_term_meta($category_id, '_yoast_wpseo_focuskw', $focus_keyword);
         }

         if(!empty($title)){
            update_term_meta($category_id, '_yoast_wpseo_title', $title);
         }
      }

      //RANK MATH
      if(Core::is_RankMath_active()){

         if(!empty($description)){
            update_term_meta($category_id, 'rank_math_description', $description);
         }

         if(!empty($focus_keyword)){
            update_term_meta($category_id, 'rank_math_focus_keyword', $focus_keyword);
         }

         if(!empty($title)){
            update_term_meta($category_id, 'rank_math_title', $title);
         }
      }

   }



   /**
    * Gets the SEO content. It will return the first active found.
    *
    * @param string category_id
    * @param string $description
    * @param string $focus_keyword
    * @return array
    */
   public static function get_seo_content($category_id){

      $data = [
         'seo_description'   => '',
         'seo_focus_keyword' => '',
         'seo_title'         => '',
      ];

      //YOAST
      if(Core::is_YOAST_active()){
         return [
            'seo_description'   => get_term_meta($category_id, '_yoast_wpseo_metadesc', true),
            'seo_focus_keyword' => get_term_meta($category_id, '_yoast_wpseo_focuskw', true),
            'seo_title'         => get_term_meta($category_id, '_yoast_wpseo_title', true),
         ];
      }

      //RANK MATH
      if(Core::is_RankMath_active()){
         return [
            'seo_description'   => get_term_meta($category_id, 'rank_math_description', true),
            'seo_focus_keyword' => get_term_meta($category_id, 'rank_math_focus_keyword', true),
            'seo_title'         => get_term_meta($category_id, 'rank_math_title', true),
         ];
      }

      return $data;
   }

}