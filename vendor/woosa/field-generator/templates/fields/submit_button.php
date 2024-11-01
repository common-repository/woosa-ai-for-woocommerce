<?php
/**
 * @author Woosa Team
 */

namespace WoosaAi\WooCommerce;


//prevent direct access data leaks
defined( 'ABSPATH' ) || exit;


?>
<tr valign="top" class="<?php echo $is_disabled;?>">
   <td colspan="2" class="forminp forminp-<?php echo $instance->get_field_type($field['type']); ?>">
      <div>
         <button type="button" class="button button-primary" data-submit-button="<?php echo $instance->get_field_id($field['id']);?>"><?php _e('Save changes', 'woosa-ai-for-woocommerce');?></button>
      </div>
   </td>
</tr>