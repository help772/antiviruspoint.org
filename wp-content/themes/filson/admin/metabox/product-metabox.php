<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 			Blog Metabox
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'add_meta_boxes', 'hexwp_product_metabox' );
function hexwp_product_metabox($post){
    add_meta_box('hexwp_product_general_metabox',esc_html__('Layout','hexwp'), 'hexwp_product_general_metabox', 'product', 'normal' , 'high');
  
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 			Blog General Metabox
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_product_general_metabox($post){
	global $post;
	
		wp_nonce_field( basename(__FILE__), 'hexwp_product_meta_nonce' );

	$custom = get_post_custom($post->ID);
 	$product_width  = hexwp_array_options('image_width',true);
  	 
 
    $product_image_width = get_post_meta($post->ID, 'single_product_image_width', true);
 
	
    	?>
	 
	<table class="form-table meta_box">     
		<tbody>
        	
             
       
        	
            <tr class="product_image_width">
                <th ><label for="single_product_image_width"><?php echo esc_html__('Image Width','hexwp');?></label></th>
                <td>
					<select name="single_product_image_width" id="product_image_width">
                          	<?php if(!empty($product_width) && is_array($product_width) ){  ?>
                        		<?php foreach ($product_width as $key => $name){  ?>
                    			<option value="<?php echo ''.esc_attr($key) ?>" <?php  if ( $product_image_width  == ''.$key ){ echo 'selected=""';} ?>><?php echo esc_html($name);?></option> 
							<?php }?>                      
							<?php }?>                      
                    </select>
                    
                </td>
            </tr>  
                        
 
                        
     	</tbody>
     </table>
    <?php
} 
add_action('save_post', 'hexwp_save_product_metabox');

function hexwp_save_product_metabox( $post_id){ 
    global $post;
   if (!isset($_POST['hexwp_product_meta_nonce']) || !wp_verify_nonce($_POST['hexwp_product_meta_nonce'], basename(__FILE__))) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

  if (defined('DOING_AJAX') ) {
	return $post_id;
   }
	if ( isset($_POST['single_product_image_width']) ) {
		update_post_meta($post_id, 'single_product_image_width', $_POST['single_product_image_width']);
	}else{
		delete_post_meta($post_id, 'single_product_image_width');
	}
  
	 
}


