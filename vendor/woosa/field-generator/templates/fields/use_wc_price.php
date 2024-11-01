<?php
/**
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


$use_wc_price   = Option::get($field['id'], $field['default']);
$price_addition = Option::get('price_addition');
$display_field  = 'yes' === $use_wc_price ? 'display:block;' : 'display:none;';
?>
<tr valign="top" class="<?php echo $is_disabled;?>">
   <th><label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?> <?php echo wc_help_tip($field['desc_tip']); ?></label></th>
   <td class="forminp">
      <label>
         <input type="hidden" name="<?php echo $instance->get_field_name($field['id']); ?>" value="no">
         <input type="checkbox" id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo $instance->get_field_name($field['id']); ?>" <?php checked($use_wc_price, 'yes');?> data-<?php echo PREFIX;?>-has-extra-field="<?php echo esc_attr( $field['id'] );?>" value="yes"> <?php _e('Yes', 'woosa-ai-for-woocommerce');?>
      </label>
      <p data-<?php echo PREFIX;?>-extra-field-<?php echo esc_attr( $field['id'] );?>="yes" style="<?php echo esc_attr( $display_field );?>">
         <label style="font-style:italic; font-size:12px;"><?php _e('Adjust the price (e.g. "10" for fixed amount or "10%" for percentage amount)', 'woosa-ai-for-woocommerce');?></label><br/>
         <input type="text" name="<?php echo Util::prefix('fields[price_addition]');?>" value="<?php echo esc_attr( $price_addition );?>" placeholder="e.g. 10% or 10.00">
      </p>
   </td>
</tr>
