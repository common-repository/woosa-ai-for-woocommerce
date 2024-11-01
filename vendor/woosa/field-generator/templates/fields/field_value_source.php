<?php
/**
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


//Backward compatibility since 1.0.1
$f_ids = [
   Util::prefix('ean'),
   Util::prefix('identifier_code_source'),
];

$old_1 = Option::get('ian_custom_field_name');
$old_2 = Option::get('ian_attribute_name');

foreach($f_ids as $id){

   if($field['id'] == $id){

      if( ! empty($old_1) ){
         Option::set("{$field['id']}__custom_field_name", $old_1);
         Option::delete('ian_custom_field_name');
      }
      if( ! empty($old_2) ){
         Option::set("{$field['id']}__attribute_name", $old_2);
         Option::delete('ian_attribute_name');
      }
   }
}
//end of backward compatibily

$source            = $field['value'];
$custom_field_name = Option::get("{$field['id']}__custom_field_name");
$attribute_name    = Option::get("{$field['id']}__attribute_name");
$parent_field_id   = Util::unprefix( $field['id'] );

$display_field1 = $source === 'custom_field' ? 'display:block;' : 'display:none;';
$display_field2 = $source === 'attribute' ? 'display:block;' : 'display:none;';
?>
<tr>
   <th><label for="<?php echo $instance->get_field_id($field['id']);?>"><?php echo esc_html( $field['title'] ); ?> <?php echo $instance->get_field_tooltip($field['desc_tip']);?></label></th>
   <td class="forminp">
      <select id="<?php echo $instance->get_field_id($field['id']);?>" name="<?php echo $instance->get_field_name($field['id']);?>" data-<?php echo PREFIX;?>-has-extra-field="<?php echo $instance->get_field_id($field['id']);?>">
         <option value="default" <?php selected($source, 'default');?>><?php _e('Default', 'woosa-ai-for-woocommerce');?></option>
         <option value="custom_field" <?php selected($source, 'custom_field');?>><?php _e('Product custom field', 'woosa-ai-for-woocommerce');?></option>
         <option value="sku" <?php selected($source, 'sku');?>><?php _e('Product SKU', 'woosa-ai-for-woocommerce');?></option>
         <option value="attribute" <?php selected($source, 'attribute');?>><?php _e('Product attribute', 'woosa-ai-for-woocommerce');?></option>
      </select>
      <p data-<?php echo PREFIX;?>-extra-field-<?php echo $instance->get_field_id($field['id']);?>="custom_field" style="<?php echo esc_attr( $display_field1 );?>">
         <label style="font-style:italic; font-size:12px;"><?php _e('Specify the custom field name', 'woosa-ai-for-woocommerce');?></label><br/>
         <input type="text" name="<?php echo $instance->get_field_name("{$parent_field_id}__custom_field_name");?>" value="<?php echo esc_attr( $custom_field_name );?>">
      </p>
      <div data-<?php echo PREFIX;?>-extra-field-<?php echo $instance->get_field_id($field['id']);?>="attribute" style="<?php echo esc_attr( $display_field2 );?>">
         <p>
            <label style="font-style:italic; font-size:12px;"><?php _e('Specify below the slug or name of the attribute.', 'woosa-ai-for-woocommerce');?></label>
         </p>
         <p>
            <input type="text" name="<?php echo $instance->get_field_name("{$parent_field_id}__attribute_name");?>" value="<?php echo esc_attr( $attribute_name );?>">
         </p>
         <p style="font-style:italic; font-size:12px; display: block;">&bull; <?php _e('only the first value of the attribute values will be used.', 'woosa-ai-for-woocommerce');?></p>
         <p style="font-style:italic; font-size:12px; display: block; margin: 0;">&bull; <?php _e('it does not work for custom attributes and variable products.', 'woosa-ai-for-woocommerce');?></p>
      </div>
      <?php echo $instance->get_field_description($field); ?>
   </td>
</tr>
