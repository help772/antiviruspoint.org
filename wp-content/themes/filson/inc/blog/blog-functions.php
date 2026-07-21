<?php 
include_once hexwp_PATH . '/inc/blog/blog-module-1.php';  
include_once hexwp_PATH . '/inc/blog/blog-module-2.php';  
include_once hexwp_PATH . '/inc/blog/blog-module-3.php';  
include_once hexwp_PATH . '/inc/blog/single-functions.php'; 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Archive
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_archive($title =false){
	$args=array();
	$args['key'] = 'archive';
	$args['option'] = array(
		'archive'			=>  '1',
		'number'			=> get_option('posts_per_page'),	
		'title'				=> $title ,
		'title_box_tag'				=> 'h1' ,
 		
		'layout'			=> hexwp_option('blog_layout' ),
		'list_layout'		=> hexwp_option('blog_list_layout' ),
		'grid_layout'		=> hexwp_option('blog_grid_layout' ),
 		'excerpt' 			=> hexwp_option('blog_excerpt',true),
		'title_limit' 		=> hexwp_option('title_excerpt_limit'),
		'excerpt_limit' 	=> hexwp_option('blog_excerpt_limit'),
 		'readmore' 			=> hexwp_option('blog_readmore',true),
 		"responsive_column" => hexwp_option('blog_responsive_column'), 
		
		'meta'			=> array(
		
				'meta_category'			=>  hexwp_option('blog_meta_category',true),
				'meta_author'			=> hexwp_option('blog_meta_author',true),
				'meta_date'				=>  hexwp_option('blog_meta_date',true),
				'meta_view'				=>  hexwp_option('blog_meta_view',true),
				'meta_comments'			=>  hexwp_option('blog_meta_comments',true),
		),
		'more_posts'		=>  'pagenavi',
 
     );
	echo hexwp_blog_config($args, true);
}

 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Thumb
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_thumb( $option,$figcaption=false,$caption_layout=false) { 
 	
	if(has_post_thumbnail()){
		
 		global $post;
		
		$thumb =  hexwp_data($option,'image_size','full'); 
		$hover_post_icon = hexwp_data($option,'hover_post_icon',hexwp_option('blog_hover_post_icon')); 
 		$style='';
  		
		$thumbnail= wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $thumb );
		$the_permalink = get_permalink();
 		if(!empty($thumbnail[0])){
 		?>
            <div class="hw-thumb" > 
            
                <a <?php if(!empty($the_permalink)){?>href="<?php echo esc_url($the_permalink) ?>"  <?php } ?> >
                
                    <?php the_post_thumbnail($thumb,array('alt'=>esc_attr(get_the_title())));	?>
                    
                    <?php if(hexwp_data($option,'ratio',hexwp_option('blog_ratio'))!=='hw-ratio-auto'){?>
                        <figure style="background-image:url('<?php echo get_the_post_thumbnail_url($post->ID, $thumb);?>');"></figure>
                    <?php }?>
                    
                 </a>
                 
                 <?php
                
                 if($hover_post_icon =='show' && !empty($figcaption)){ ?>
     
                    <figcaption>
                    <?php hexwp_post_hover_link($the_permalink);?>
                    </figcaption> 
    
                <?php }?>
                  
            </div>
		<?php
		}
	}
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Post Title
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_post_title($option= false,$font=false) {
	 	$limit = hexwp_data($option,'title_limit');    

	$the_title = strip_tags(get_the_title());
  	if ( !empty($limit) && strlen($the_title) > $limit){
 		 $content= mb_substr($the_title, 0,$limit).'...';
		 
	} else {
		$content= $the_title;
		$dots='';
	}
  	?>
    
 	<h3 class="hw-title"><a href="<?php the_permalink(); ?>"><?php echo esc_html($content);?></a></h3>
 	 	
	<?php 
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Excerpt
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_excerpt($option=false) {
	global $post;
	if(empty(hexwp_meta('sao_show_page_builder')) || has_excerpt()){		  
		 $limit = hexwp_data($option,'excerpt_limit');
   		$the_excerpt = strip_tags(get_the_excerpt());
		if ( !empty($limit) && strlen($the_excerpt) > $limit){
			 $content= mb_substr($the_excerpt, 0,$limit).'...';
			 
		}else{
			$content= $the_excerpt;
		}
		if(!empty($content)){?>
		<div class="hw-excerpt"><?php echo esc_html($content);?></div>
		<?php 
		}
	}
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Excerpt Category
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_category($item= 1) {
	$terms = wp_get_post_terms(get_the_ID(),"category");
	
	$class='';
  
 	$count=0;
 	if(!empty($terms)){
 		echo '<div class="hw-all-category-blog">';
			foreach ( $terms as $term ) { $count++;  
			echo '<a href="'.esc_url(get_term_link($term->term_id)).'">'.esc_html( $term->name).'</a>';
	 
			if($count == $item) { 
				break;
			}
			} 
		echo '</div>';
 	}
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Meta
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_meta($option =false){ 
	global  $post,$smof_data;
	$meta = hexwp_data($option,'meta');
  
  	$meta_author = !empty($meta['meta_author'] )? $meta['meta_author']:'' ;
	$meta_category = !empty($meta['meta_category'] )? $meta['meta_category']:'' ;
	$meta_date = !empty($meta['meta_date'] )? $meta['meta_date']:'' ;
 	$meta_view =!empty( $meta['meta_view'])? $meta['meta_view']:'' ; 
 	$meta_comments =!empty( $meta['meta_comments'])? $meta['meta_comments']:'' ; 
	
	 
  	if( !empty($meta_author) || !empty($meta_comments) || !empty($meta_date) ||!empty($meta_view) || !empty($meta_category)  ){ 
 	
	$class='';
	$layout = hexwp_option_2('blog_meta_layout','layout');
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
			if( !empty($meta_author)){ 
				echo '<li class="hw-author  ">'.wp_kses_post($avatar.esc_html($by)).' ';
					the_author_posts_link();
				echo '</li>';
			}
			//Category
 			if( !empty($meta_category)){ 	
				echo '<li class="hw-cats">';
				hexwp_blog_category(1);
				echo '</li>';
			}
	  
    
			 if(!empty( $meta_date) ){ 
				echo '<li class="hw-date">';
					hexwp_get_time();
				echo '</li>';
			} 
    
 			if( !empty($meta_view )){
				echo '<li class="hw-view ">'.hexwp_getPostViews(get_the_ID()).'</li>';
			}	
 			if( !empty($meta_comments )){
				echo '<li class="hw-comment">'.hexwp_blog_meta_comments().'</li>';
			}	
			?>
 		 
	</ul> 
      
	<?php
	 }
} 
  
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Meta Comments
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_blog_meta_comments($no_text =false){ 
	ob_start(); 

 		if(!empty($no_text)){
			  comments_popup_link(hexwp_t('0'),hexwp_t('1') ,esc_html('%'), '' , hexwp_t('0'));
 		} else{
			  comments_popup_link(hexwp_t('nocommentsyet'),hexwp_t('1').' '.hexwp_t('commentalready') ,esc_html('%'.' '.hexwp_t('commentsalready')), '' , hexwp_t('commetsoff'));
		}
		   return  ob_get_clean();;

}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Blog Read More
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_readmore($option =false){ ?>
	 <div class="hw-readmore"><a href="<?php the_permalink();?>"  ><?php echo esc_html(hexwp_t('read_more'));?></a></div>
	<?php
}