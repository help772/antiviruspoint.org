<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 			Profile Fields
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'show_user_profile', 'hexwp_profile_fields' );
add_action( 'edit_user_profile', 'hexwp_profile_fields' );
function hexwp_profile_fields( $user ) {
	?>
 	<table class="form-table">

		<tr>
			<th><label for="Facebook"><?php echo esc_html('Facebook');?></label></th>
            <td><input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
       <tr>
			<th><label for="twitter"><?php echo esc_html('Twitter');?></label></th>
			<td><input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
        <tr>
			<th><label for="googleplus"><?php echo esc_html('Google Plus');?></label></th>
            <td><input type="text" name="googleplus" id="googleplus" value="<?php echo esc_attr( get_the_author_meta( 'googleplus', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
        
        <tr>
			<th><label for="linkin"><?php echo esc_html('linkedIn');?></label></th>
			<td><input type="text" name="linkin" id="linkedin" value="<?php echo esc_attr( get_the_author_meta( 'linkedin', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
        <tr>
			<th><label for="Flickr"><?php echo esc_html('Flickr');?></label></th>
			<td><input type="text" name="flickr" id="flickr" value="<?php echo esc_attr( get_the_author_meta( 'flickr', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
        <tr>
			<th><label for="tumblr"><?php echo esc_html('Tumblr');?></label></th>
			<td><input type="text" name="tumblr" id="tumblr" value="<?php echo esc_attr( get_the_author_meta( 'tumblr', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
         <tr>
			<th><label for="vimeo"><?php echo esc_html('Vimeo');?></label></th>
            <td><input type="text" name="vimeo" id="vimeo" value="<?php echo esc_attr( get_the_author_meta( 'vimeo', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>
         <tr>
         	<th><label for="youtube"><?php echo esc_html('YouTube');?></label></th>
			<td><input type="text" name="youtube" id="youtube" value="<?php echo esc_attr( get_the_author_meta( 'youtube', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr> 
         <tr>
			<th><label for="instagram"><?php echo esc_html('Instagram');?></label></th>
			<td><input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( get_the_author_meta( 'instagram', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>   
         <tr>
			<th><label for="telegram"><?php echo esc_html('Telegram');?></label></th>
			<td><input type="text" name="telegram" id="telegram" value="<?php echo esc_attr( get_the_author_meta( 'telegram', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>   
        
         <tr>
			<th><label for="pinterest"><?php echo esc_html('Pinterest');?></label></th>
			<td><input type="text" name="pinterest" id="pinterest" value="<?php echo esc_attr( get_the_author_meta( 'pinterest', $user->ID ) ); ?>" class="regular-text" /><br /></td>
		</tr>   
           
	</table>
	<?php 
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
														 		Save Profile Fields
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'personal_options_update', 'hexwp_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'hexwp_save_extra_profile_fields' );
function hexwp_save_extra_profile_fields( $user_id ) {
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

 	update_user_meta( $user_id, 'facebook', $_POST['facebook'] );
	update_user_meta( $user_id, 'twitter', $_POST['twitter'] );
	update_user_meta( $user_id, 'googleplus', $_POST['googleplus'] );
	update_user_meta( $user_id, 'linkedin', $_POST['linkedin'] );
	update_user_meta( $user_id, 'flickr', $_POST['flickr'] );
	update_user_meta( $user_id, 'tumblr', $_POST['tumblr'] );
	update_user_meta( $user_id, 'vimeo', $_POST['vimeo'] );
	update_user_meta( $user_id, 'youtube', $_POST['youtube'] );
	update_user_meta( $user_id, 'instagram', $_POST['instagram'] );
	update_user_meta( $user_id, 'telegram', $_POST['telegram'] );
	update_user_meta( $user_id, 'pinterest', $_POST['pinterest'] );
}