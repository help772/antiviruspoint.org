<?php

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 			Blog Metabox
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'add_meta_boxes', 'hexwp_blog_metabox' );
function hexwp_blog_metabox($post){
    add_meta_box('hexwp_blog_general_metabox',esc_html__('General Options','hexwp'), 'hexwp_blog_general_metabox', 'post', 'normal' , 'high');
   //  add_meta_box('hexwp_blog_video_metabox',esc_html__('Video','hexwp'), 'hexwp_blog_video_metabox', 'post', 'normal' , 'high');
 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 			Blog General Metabox
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_general_metabox($post){
	global $post;
	wp_nonce_field( basename(__FILE__), 'hexwp_blog_meta_nonce' );

	$custom = get_post_custom($post->ID);
	$sidebars  = hexwp_category_array_options('sidebars');
   	 
     $single_template = get_post_meta($post->ID, 'single_template', true);
    $single_right = get_post_meta($post->ID, 'sidebar_single_right', true);
    $single_left = get_post_meta($post->ID, 'sidebar_single_left', true);
    $ratio = get_post_meta($post->ID, 'single_ratio', true);
   	$full_width_value = get_post_meta($post->ID, 'full_width', true);
     if($full_width_value == "yes"){ $full_width_checked = 'checked="checked"';}else{$full_width_checked='';} 
    $hide_post_tags = get_post_meta($post->ID, 'hide_post_tags', true); 
    $hide_post_meta = get_post_meta($post->ID, 'hide_post_meta', true);
    $hide_post_share = get_post_meta($post->ID, 'hide_post_share', true);
     $hide_author_box = get_post_meta($post->ID, 'hide_author_box', true);
    $hide_related_post = get_post_meta($post->ID, 'hide_related_post', true);
    $hide_banner_below = get_post_meta($post->ID, 'hide_banner_below', true);
    $hide_comments = get_post_meta($post->ID, 'hide_comments', true);
    $primary_color = get_post_meta($post->ID, 'primary_color', true);
    $body_background_color = get_post_meta($post->ID, 'body_background_color', true);
    $body_background_type = get_post_meta($post->ID, 'body_background_type', true);
    $body_background_image = get_post_meta($post->ID, 'body_background_image', true);
    $body_background_image_medium = get_post_meta($post->ID, 'body_background_image_medium', true);
    $body_background_pattern = get_post_meta($post->ID, 'body_background_pattern', true);
	
    	?>
	 
	<table class="form-table hexwp-meta_box meta_box">     
		<tbody>
        	
   
            <tr hw-parant="" class="meta_hexwp_single_template">
				<th><label for="single_template"><?php echo esc_html__('Single template','hexwp');?></label></th>
				<td>
                        <li class="single_template-default">
                            <input name="single_template" id="single_template-default"  value="default" type="radio" <?php checked( $single_template, 'default' );?>>
                            <a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/default.jpg"></a>
                         </li>
                         <li class="single_template-1">
                            <input  name="single_template" id="single_template-1" value="1" type="radio" <?php checked( $single_template, '1' );?>>
                            <a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-1.jpg"></a>
                         </li>
                         <li class="single_template-2">
                            <input  name="single_template" id="single_template-2" value="2" type="radio" <?php checked( $single_template, '2' );?>>
                            <a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-2.jpg"></a>
                        </li>
                        <li class="single_template-3"><input  name="single_template" id="single_template-3" value="3" type="radio" <?php checked( $single_template, '3' );?>>
                            <a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-3.jpg"></a>
                        </li>
                        <li class="single_template-4" ><input  name="single_template" id="single_template-4" value="4" type="radio" <?php checked( $single_template, '4' );?>>
                        	<a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-4.jpg"></a>
                  		</li>
                        <li class="single_template-5" ><input  name="single_template" id="single_template-5" value="5" type="radio" <?php checked( $single_template, '5' );?>>
                        	<a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-5.jpg"></a>
                  		</li>
                        <li class="single_template-6" ><input  name="single_template" id="single_template-6" value="6" type="radio" <?php checked( $single_template, '6' );?>>
                        	<a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-6.jpg"></a>
                  		</li>
                        <li class="single_template-7" ><input  name="single_template" id="single_template-7" value="7" type="radio" <?php checked( $single_template, '7' );?>>
                        	<a><img src="<?php echo hexwp_DIR;?>/admin/assets/images/single/single-template-7.jpg"></a>
                  		</li>
                                                                                         
                </td>
       
        	
            <tr class="meta_hexwp_single_sidebar">
                <th ><label for="single_sidebar"><?php echo esc_html__('Custom Sidebar Right','hexwp');?></label></th>
                <td>
					<select name="sidebar_single_right" id="single_sidebar">
                          	<?php if(!empty($sidebars) && is_array($sidebars) ){  ?>
                        		<?php foreach ($sidebars as $key => $name){  ?>
                    			<option value="<?php echo ''.esc_attr($key) ?>" <?php  if ( $sidebars  == ''.$key ){ echo 'selected=""';} ?>><?php echo esc_html($name);?></option> 
							<?php }?>                      
							<?php }?>                      
                    </select>
                    
                </td>
            </tr> 
			<tr class="meta_hexwp_single_sidebar hexwp_meta_radio">
                <th ><label for="single_sidebar"><?php echo esc_html__('Custom Sidebar Left','hexwp');?></label></th>
                <td>
					<select name="sidebar_single_left" id="single_sidebar">
                          	<?php if(!empty($sidebars) && is_array($sidebars) ){  ?>
                        		<?php foreach ($sidebars as $key => $name){  ?>
                    			<option value="<?php echo ''.esc_attr($key) ?>" <?php  if ( $single_left  == ''.$key ){ echo 'selected=""';} ?>><?php echo esc_html($name);?></option> 
							<?php }?>                      
							<?php }?>                      
                    </select>
                    
                </td>
            </tr> 
                        
            
            
            <tr class="meta_hexwp_full_width hexwp_meta_radio">
                <th ><label for="full_width"><?php echo esc_html__('Full Width Post','hexwp');?></label></th>
                <td>
                	<?php
					 hexwp_radio_buttons(
						'full_width',
						array(
							''	=>__('Disable','hexwp'),
							'yes'	=>__('Enable','hexwp'),
						),
						$full_width_value
					);
					?> 
                </td>
            </tr>            
                      	
            <tr class="meta_hexwp_hide_post_tags hexwp_meta_radio">
                <th ><label for="hide_post_tags"><?php echo esc_html__('Hide Post Tags','hexwp');?></label></th>
                <td>
                
              	  <?php
					 hexwp_radio_buttons(
						'hide_post_tags',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_post_tags
					);
					?> 
                     
                </td>
            </tr>  
            
            <tr class="meta_hexwp_hide_post_meta hexwp_meta_radio">
                <th ><label for="hide_post_meta"><?php echo esc_html__('Hide Post Meta','hexwp');?></label></th>
                <td>
                    <?php
					 hexwp_radio_buttons(
						'hide_post_meta',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_post_meta
					);
					?> 
                </td>
            </tr>    
                  
            <tr class="meta_hexwp_hide_post_share hexwp_meta_radio">
                <th ><label for="hide_post_share"><?php echo esc_html__('Hide Post Share','hexwp');?></label></th>
                <td>
                    <?php
					 hexwp_radio_buttons(
						'hide_post_share',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_post_share
					);
					?> 
                </td>
            </tr>        
                   
                   
            <tr class="meta_hexwp_hide_author_box hexwp_meta_radio">
                <th ><label for="hide_author_box"><?php echo esc_html__('Hide Author Bio','hexwp');?></label></th>
                <td>
                    <?php
					 hexwp_radio_buttons(
						'hide_author_box',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_author_box
					);
					?> 
                </td>
            </tr>        
                   
                   
                    
            <tr class="hide_related_post hexwp_meta_radio">
                <th ><label for="hide_related_post"><?php echo esc_html__('Hide Related Post','hexwp');?></label></th>
                <td>
                    <?php
					 hexwp_radio_buttons(
						'hide_related_post',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_related_post
					);
					?> 
                </td>
            </tr>        

            <tr class="meta_hexwp_hide_banner_below hexwp_meta_radio">
                <th ><label for="hide_banner_below"><?php echo esc_html__('Hide Below Ads Widget','hexwp');?></label></th>
                <td>
                    <?php
					 hexwp_radio_buttons(
						'hide_banner_below',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_banner_below
					);
					?> 
                </td>
            </tr>            

            <tr class="meta_hexwp_hide_comments hexwp_meta_radio">
                <th ><label for="hide_comments"><?php echo esc_html__('Hide Comments','hexwp');?></label></th>
                <td>
                     <?php
					 hexwp_radio_buttons(
						'hide_comments',
						array(
							''	=>__('Default','hexwp'),
							'hide'	=>__('Hide','hexwp'),
						),
						$hide_comments
					);
					?> 
                </td>
            </tr>   
            
             
              
                        
            <tr class="meta_hexwp_body_background_color meta_hexwp_color">
                <th ><label for="body_background_color"><?php echo esc_html__('Background Color','hexwp');?></label></th>
                <td>
               		 <input  class="hexwp-coloris hw-color"  data-rgba="false" type="text" name="body_background_color" id="body_background_color" value="<?php echo esc_attr($body_background_color);?>">
                 </td>
            </tr> 
                        
         	<tr class="meta_hexwp_body_background_type hexwp_meta_radio">
                <th ><label for="body_background_type"><?php echo esc_html__('Background Type','hexwp');?></label></th>
                <td>
                    	 <?php
					 hexwp_radio_buttons(
						'body_background_type',
						array(
							''	=>__('Default','hexwp'),
							'none'	=>__('None','hexwp'),
							'pattern'	=>__('Pattern','hexwp'),
							'custom'	=>__('Custom Image','hexwp'),
						),
						$body_background_type
					);
					?> 
                </td>
            </tr>            
            <tr class="meta_hexwp_body_background_pattern">
                <th ><label for="body_background_pattern"><?php echo esc_html__('Background Pattern','hexwp');?></label></th>
                <td>
 					<?php for ($i = 1; $i <= 23; $i++) {
						
						$bg='bg'.$i;  ?>
                        <li>
                            <input  name="body_background_pattern" id="body_background_pattern-default"  value="<?php echo esc_attr($bg) ?>" type="radio" <?php checked( $body_background_pattern, $bg );?>>
                            <a><img src="<?php echo hexwp_DIR;?>/images/bg/<?php echo esc_attr($bg)?>.png"></a>
                         </li>                    
 					<?php }?>                      
                     
                </td>
            </tr> 
            
            <tr class="meta_hexwp_body_background_image">
                <th ><label for="body_background_image"><?php echo esc_html__('Custom Background Image','hexwp');?></label></th>
                <td> 
 	  	 		<a class="hw_add_image button button-small"  data-uploader-title="<?php echo esc_attr__('Choose Image','hexwp');?>" data-remove="<?php echo esc_attr__('Remove','hexwp');?>"  data-uploader-button-text="<?php echo esc_attr__('Use This Image','hexwp');?>"> <?php echo esc_html__('Upload','hexwp')?></a>
 				<input type="hidden" name="body_background_image" value="<?php echo esc_url($body_background_image);?>">
 		
				<?php if(!empty($body_background_image)){     ?>
 	   			<a class="hw_remove_image button button-small" ><?php echo  esc_html__('Remove','hexwp');?></a>
 		 		<img   src="<?php echo esc_url($body_background_image) ?>"/> 
                <?php }?>
               	</td>
            </tr>               
                                                                       
                        
     	</tbody>
     </table>
    <?php
}

 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 			Save Blog  Metabox
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('save_post', 'hexwp_save_blog_metabox');

function hexwp_save_blog_metabox( $post_id){ 
    global $post;
	
	
    if (!isset($_POST['hexwp_blog_meta_nonce']) || !wp_verify_nonce($_POST['hexwp_blog_meta_nonce'], basename(__FILE__))) return;

    if (!current_user_can('edit_post', $post_id)) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	if (defined('DOING_AJAX') ) {
		return $post_id;
	}
	
 
	if (  !empty($_POST['single_template']) ) {
		update_post_meta($post_id, 'single_template', $_POST['single_template']);
	}else{
		delete_post_meta($post_id, 'single_template');
	}
	
	if (  !empty($_POST['single_template']) ) {
		update_post_meta($post_id, 'single_template', $_POST['single_template']);
	}else{
		delete_post_meta($post_id, 'single_template');
	}
 
	if (   !empty($_POST['sidebar_single_right']) ) {
		update_post_meta($post_id, 'sidebar_single_right', $_POST['sidebar_single_right']);
	}else{
		delete_post_meta($post_id, 'sidebar_single_right');
	}			 
	
	if (   !empty($_POST['sidebar_single_left']) ) {
		update_post_meta($post_id, 'sidebar_single_left', $_POST['sidebar_single_left']);
	}else{
		delete_post_meta($post_id, 'sidebar_single_left');
	}		
	
	 	
	   
	if ( !empty($_POST['full_width']) ) {
		update_post_meta($post_id, 'full_width', $_POST['full_width']);
	}else{
		delete_post_meta($post_id, 'full_width');
	}
	
	if ( !empty($_POST['hide_post_tags']) ) {
		update_post_meta($post_id, 'hide_post_tags', $_POST['hide_post_tags']);
	}else{
		delete_post_meta($post_id, 'hide_post_tags');
	}		
	
	if (  !empty($_POST['hide_post_meta']) ) {
		update_post_meta($post_id, 'hide_post_meta', $_POST['hide_post_meta']);
	}else{
		delete_post_meta($post_id, 'hide_post_meta');
	}		
	
	if (  !empty($_POST['hide_post_share']) ) {
		update_post_meta($post_id, 'hide_post_share', $_POST['hide_post_share']);
	}else{
		delete_post_meta($post_id, 'hide_post_share');
	}				 
	
	if ( !empty($_POST['hide_author_box']) ) {
		update_post_meta($post_id, 'hide_author_box', $_POST['hide_author_box']);
	}else{
		delete_post_meta($post_id, 'hide_author_box');
	}								 

	if ( !empty($_POST['hide_related_post']) ) {
		update_post_meta($post_id, 'hide_related_post', $_POST['hide_related_post']);
	}else{
		delete_post_meta($post_id, 'hide_related_post');
	}	
	 

	if ( !empty($_POST['hide_banner_below']) ) {
		update_post_meta($post_id, 'hide_banner_below', $_POST['hide_banner_below']);
	}else{
		delete_post_meta($post_id, 'hide_banner_below');
	}	
	
	if ( !empty($_POST['hide_comments']) ) {
		update_post_meta($post_id, 'hide_comments', $_POST['hide_comments']);
	}else{
		delete_post_meta($post_id, 'hide_comments');
	}	 	  
	   
	  
	 
 
 
	
	if ( !empty($_POST['body_background_color']) ) {
		update_post_meta($post_id, 'body_background_color', $_POST['body_background_color']);
	}else{
		delete_post_meta($post_id, 'body_background_color');
	}		

	if (!empty($_POST['body_background_type']) ) {
		update_post_meta($post_id, 'body_background_type', $_POST['body_background_type']);
	}else{
		delete_post_meta($post_id, 'body_background_type');
	}
	
	 		 	   


	if (  !empty($_POST['body_background_image']) ) {
		update_post_meta($post_id, 'body_background_image', $_POST['body_background_image']);
	}else{
		delete_post_meta($post_id, 'body_background_image');
	}
 
	
	if (  !empty($_POST['body_background_pattern']) ) {
		update_post_meta($post_id, 'body_background_pattern', $_POST['body_background_pattern']);
	}else{
		delete_post_meta($post_id, 'body_background_pattern');
	}			    
 
		
}