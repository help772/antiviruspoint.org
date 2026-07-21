<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Single
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_single() {
 	global $post, $smof_data;
	if ( (  is_page() || is_single())) {
	$meta = get_post_meta( $post->ID );
	}
	$hexwp_single_template = isset( $meta['single_template'][0] ) ?  $meta['single_template'][0]  : 'default';
	$hexwp_single_admin = isset( $smof_data['single_template'] ) ?  $smof_data['single_template']  : '3';
 
  	if( $hexwp_single_template == 'default' || empty($hexwp_single_template)  ) {
 		$hexwp_single = $hexwp_single_admin; 

	} else{
		$hexwp_single = $hexwp_single_template;
 	}
	return $hexwp_single;
	
}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Single Body Classses
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter('body_class', 'hexwp_single_body_classes');
function hexwp_single_body_classes($classes) {
	
 	global $post, $smof_data;
	if ( (  is_page() || is_single())) {
	$meta = get_post_meta( $post->ID );
	}
	$hexwp_single_template = isset( $meta['single_template'][0] ) ?  $meta['single_template'][0]  : 'default';
	$hexwp_single_admin = isset( $smof_data['single_template'] ) ?  $smof_data['single_template']  : hexwp_option_default('single_template');
	
  	if( $hexwp_single_template == 'default' || empty($hexwp_single_template)  ) {
 		$hexwp_single = $hexwp_single_admin; 

	} else{
		$hexwp_single = $hexwp_single_template;
 	}
 	
		 
	if(is_single()){
		$classes[] = 'hw-single-template-'.$hexwp_single;
	}
	
 	if( hexwp_option('body_layout' )=='enable'){
		$classes[]='hw-body-boxed';	
	}

	return $classes;
 
}  

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Single Tags
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_tags() {
	if( hexwp_meta('hide_post_tags') !== 'hide' && hexwp_option('single_tags',true) && has_tag()) {
	?>
 	<div class="hw-tags">
		 <?php the_tags('',''); ?>
	</div>
	<?php 
	}
} 
  

 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																	Author Box
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_author_box() { 
	if(  hexwp_meta('hide_author_box') !== 'hide' && hexwp_option('single_author_box',true) && get_the_author_meta( 'description' )){
		$box_layout_single = hexwp_box_layout_single();?>
		
		<?php if($box_layout_single!=='hw-single-boxed'){?>
			<div class="hw-el-line"></div>
		<?php }?>
			<aside class="hw-el-author hw-aw <?php echo $box_layout_single;?>">
			 
				<div class="hw-author-thumb"><?php echo get_avatar( get_the_author_meta( 'user_email' ), '80' ); ?></div>
				
				<div class="hw-author-details">
					<h4 class="hw-author-name"><?php the_author_posts_link(); ?></h4>
					<?php if(get_the_author_meta( 'description' )){?>
						<p class="hw-author-description  ">
							<?php the_author_meta( 'description' ); ?>
						</p>
					<?php } 
					
					$social=array(			
						'facebook'	=>	get_the_author_meta('facebook'),
						'twitter'		=>	get_the_author_meta('twitter'),
						'googleplus'	=>	get_the_author_meta('googleplus'),
						'flickr'		=>	get_the_author_meta('flickr'),
						'skype'		=>	get_the_author_meta('skype'),
						'tumblr'		=>	get_the_author_meta('tumblr'),
						'vimeo'		=>	get_the_author_meta('vimeo'),
						'youtube'		=>	get_the_author_meta('youtube'),
						'instagram'	=>	get_the_author_meta('instagram'),
						'telegram'	=>	get_the_author_meta('telegram'),
						'pinterest'	=>	get_the_author_meta('pinterest'),
						'whatsapp'	=>	get_the_author_meta('whatsapp'),
					);	
					
					$style = hexwp_option('single_share_icons_style');
					?>
					
					 <div class="hw-social-icon-style">
						<?php hexwp_social_content($style,$social,'');?>
					</div>
		 
					
					
					 
				</div>
			</aside>
 	<?php
	}
}
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																	Share Post
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_share_post($align='right'){
	if( hexwp_meta('hide_post_share') !== 'hide' && hexwp_option('single_share_icons',true)){	
		$share_array['key'] = 'share-single';
		$share_array['option'] = array(
			'share_url' => get_permalink(),
			'between' => '',
			'icon_style' => hexwp_option('single_share_icons_style'),
			 'alignment' => $align,
	);
	echo hexwp_share_icons_config($share_array,1); 
	 }
 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
															Box Layout Single
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_box_layout_single($type='blog'){ 
	if($type=='blog'){
  		$box_layout = hexwp_option('blog_box_layout');
	}else{
  		$box_layout = hexwp_option($type.'_box_layout');
	}
  	if($box_layout !== 'none' && $box_layout !== ''){
    		return  'hw-single-boxed' ;
	}else{
   		return  '' ;
	}
	 
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
															 Single Meta
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_single_meta(){ 
	 
	global  $post,$smof_data;
 
	 if(hexwp_meta('hide_post_meta') !== 'hide') {
	if(  hexwp_option('single_meta_author',true)  || hexwp_option('single_meta_comments',true) || hexwp_option('single_meta_date',true) || hexwp_option('single_meta_view',true) || hexwp_option('single_meta_category',true)  ){ 
	
		
		$class='';
	   $layout = hexwp_option_2('single_meta_layout','layout');
		if($layout=='layout-2' || $layout=='layout-5' ){
			$avatar = get_avatar( get_the_author_meta( 'ID' ), 32 );
		}else{
			$avatar='';
		}
	   
		if($layout=='layout-3'){
			$by='';
		}else{
			$by = hexwp_t('by');
		}
		?>
		  
			<ul class="hw-meta">
			 
				<?php
				//Meta Author
				if(hexwp_option('single_meta_author',true)){ 
					echo '<li class="hw-author  ">'.wp_kses_post($avatar.esc_html($by)).' ';
						the_author_posts_link();
					echo '</li>';
				}
				//Category
				if(hexwp_option('single_meta_category',true)){ 	
					echo '<li class="hw-cats">';
					hexwp_blog_category(1);
					echo '</li>';
				}
		  
		
				 if(hexwp_option('single_meta_date',true) ){ 
					echo '<li class="hw-date">';
						hexwp_get_time();
					echo '</li>';
				} 
		
				if( !empty(hexwp_option('single_meta_view',true))){
					echo '<li class="hw-view ">'.hexwp_getPostViews(get_the_ID()).'</li>';
				}	
				if( !empty(hexwp_option('single_meta_comments',true) )){
					echo '<li class="hw-comment">'.hexwp_blog_meta_comments().'</li>';
				}	
				?>
                
  			 </ul>
             
         <?php
		}
	}
} 
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
															LightBox
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'wp_footer', 'hexwp_lightbox' );
function hexwp_lightbox() {
	global $smof_data ;
 	?>
	<div class="hw-lightbox hw-lightbox-active">
		<div class="hw-lightbox-outer"></div>
		<i class="hw-lightbox-close"></i>
		<i class="hw-lightbox-nextbig"></i>
		<i class="hw-lightbox-prevbig"></i>
		<div class="hw-lightbox-img">
		<img src="#" class="hw-lightbox-targetimg" alt="#"/>
		</div>
		<div class="hw-lightbox-loading"></div>
		<h3></h3>
		<span></span>
	</div>
	<?php 
 
}
 
 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
															wp link pages
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_wp_link_pages( $args = '' ) {
	$defaults = array(
		'before' => '<div class="hw-post-pagination"><a>' . hexwp_t('pages').'</a>', 
		'after' => '</div>',
		'text_before' => '',
		'text_after' => '',
		'next_or_number' => 'number', 
		'nextpagelink' =>  hexwp_t('next').' '.hexwp_t('page') ,
		'previouspagelink' =>  hexwp_t('previous').' '.hexwp_t('page'),
		'pagelink' => '%',
		'echo' => 1
	);

	$r = wp_parse_args( $args, $defaults );
	$r = apply_filters( 'wp_link_pages_args', $r );
	extract( $r, EXTR_SKIP );

	global $page, $numpages, $multipage, $more, $pagenow;

	$output = '';
	if ( $multipage ) {
		if ( 'number' == $next_or_number ) {
			$output .= $before;
			for ( $i = 1; $i < ( $numpages + 1 ); $i = $i + 1 ) {
				$j = str_replace( '%', $i, $pagelink );
				$output .= ' ';
				if ( $i != $page || ( ( ! $more ) && ( $page == 1 ) ) )
					$output .= _wp_link_page( $i );
				else
					$output .= '<span class="current-post-page">';

				$output .= $text_before . $j . $text_after;
				if ( $i != $page || ( ( ! $more ) && ( $page == 1 ) ) )
					$output .= '</a>';
				else
					$output .= '</span>';
			}
			$output .= $after;
		} else {
			if ( $more ) {
				$output .= $before;
				$i = $page - 1;
				if ( $i && $more ) {
					$output .= _wp_link_page( $i );
					$output .= $text_before . $previouspagelink . $text_after . '</a>';
				}
				$i = $page + 1;
				if ( $i <= $numpages && $more ) {
					$output .= _wp_link_page( $i );
					$output .= $text_before . $nextpagelink . $text_after . '</a>';
				}
				$output .= $after;
			}
		}
	} 
	if ( $echo )
		echo wp_kses_post($output);
	return   $output;
} 