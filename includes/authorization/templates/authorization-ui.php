<?php
/**
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


$error = Option::get('connection_error');
?>

<tr class="<?php echo esc_attr(PREFIX . '-style');?>">
   <td class="p-0">
      <div>
         <span class="tb"><?php esc_html_e('Status ', 'woosa-ai-for-woocommerce');?></span>
         <?php echo wp_kses_post($status); ?>
      </div>

      <?php if (!empty($authorization->get_wiki_article_url())): ?>
         <p class="pt-20"><?php
            echo wp_kses_post(
               sprintf(
                  // Translators: %1$s is the opening anchor tag with a link to the Help Center article, and %2$s is the closing anchor tag.
                  __('Questions about the authorization of your Woosa AI account? Read our %1$sHelp Center article%2$s, we will guide you step-by-step through the process.', 'woosa-ai-for-woocommerce'),
                  '<a href="'.esc_attr($authorization->get_wiki_article_url()).'" target="_blank" class="tb">',
                  '</a>'
               )
            );
         ?></p>
      <?php endif; ?>

      <div class="pt-15">
         <?php if($authorization->is_authorized()):?>
            <a href="<?php echo esc_attr(SETTINGS_URL . '&tab=authorization&connection_type=disconnect');?>" class="button button-secondary"><?php echo esc_html($button['label']);?></a>
         <?php else:?>
            <a href="<?php echo esc_attr(SETTINGS_URL . '&tab=authorization&connection_type=connect');?>" class="button button-primary"><?php echo esc_html($button['label']);?></a>
         <?php endif;?>
      </div>

      <?php if( ! empty($error) ):?>
         <p class="ajax-response error"><?php echo esc_html($error);?></p>
      <?php endif;?>
   </td>
</tr>