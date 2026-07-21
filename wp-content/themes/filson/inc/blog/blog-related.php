<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
																		
																		Related Blog
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
if(hexwp_meta('hide_related_post') !== 'hide' && hexwp_option('related',true) ) {
		
		
	$orig_post = $post;
	global $post,$smof_data,$categories;
	$tags = wp_get_post_tags($post->ID);
  	$count=0;
	$hexwp_related_row = hexwp_option('related_row');
	$hexwp_related_query =  hexwp_option('related_query');
	$related_ratio = hexwp_option('related_ratio')  ;
	$box_layout=hexwp_option('related_box_layout' );
 	$related_between=hexwp_option('related_between');
	$col = hexwp_option('body_width');
	$full_width = hexwp_option('full_width');
	
	if(!empty($full_width)){
		if( $col == '1600px' || $col == '1920px' || $col == '100%' ){
			$layout = 'grid_6' ; 
			
		}elseif( $col == '1300px' || $col == '1400px' || $col == '1500px' ){
			$layout = 'grid_5' ; 
				
		}elseif( $col == '1200px' || $col == '1100px' || $col == '1100px' ){
			$layout = 'grid_4' ; 
				
		} else{
			$layout = 'grid_3' ;
		}
	}else{
			$layout = hexwp_option('related_layout');
		  
	}
		 
	if($layout == 'grid_6'){
		$max_col= 6;
	}elseif($layout == 'grid_5'){
		$max_col= 5;
	}elseif($layout == 'grid_4'){
		$max_col= 4;
	}else {
		$max_col= 3;
	}
		
	$hexwp_related_number = $max_col * $hexwp_related_row;
		 
	$args_category = array(
		'category__in' => wp_get_post_categories($post->ID),
		'post__not_in' => array($post->ID),
		'posts_per_page'=> 	$hexwp_related_number,
		'ignore_sticky_posts'=>1,
		'orderby'=>'rand',
		'post_type'=>'post'
		
	);
	$related_category = new wp_query( $args_category );
	
 	$posttags = get_the_tags();
	$test = '';
	$sep = '';
	if ($posttags) {
		foreach($posttags as $tag) {
			$test .= $sep . $tag->name; 
			$sep = ",";
		}
	}
		
	$args_tags = array(
		'tag' => $test,
		'post_type'=>'post',
		'post__not_in' => array($post->ID),
		'posts_per_page'=>	$hexwp_related_number, 
		'ignore_sticky_posts'=>1,
	);
	
	$related_tags = new wp_query( $args_tags );
	
		
	if($hexwp_related_query =='category' && $related_category->have_posts()){

		$args = $args_category;
						
	} elseif($hexwp_related_query =='tag' && $related_tags->have_posts()){
		 
		$args = $args_tags;
					
	} elseif($hexwp_related_query =='random'){
		$args = array(
			'posts_per_page'=> $hexwp_related_number,
			'ignore_sticky_posts'=>1,
			'post_type'=>'post',
 		);	 
			 
	} else {
			
		$args = array( 
			'posts_per_page'=> $hexwp_related_number,
			'orderby'=>'rand',
			'post_type'=>'post',
			'ignore_sticky_posts'=>1,
		);
	}
		   
	 
	$related['key'] = 'related';
	$related['option'] = array(
		'qurey'=>  $args,
		'title'=>  hexwp_option('related_title'),
		'post_title'=> 1,
  		'between'=>$related_between,
  		'box_layout'=>$box_layout,
		'layout'=> 'grid',
		 
		'grid_layout'=> $layout,
		'ratio'=>  hexwp_option('related_ratio' ),
		'image_size'=>  hexwp_option('related_image_size' ),
		'excerpt'=>  hexwp_option('related_excerpt' ,true), 
		"responsive_column" => hexwp_option('related_responsive_column'), 
			
	);
	$box_layout_single = hexwp_box_layout_single();
	
	if($box_layout_single!=='hw-single-boxed'){ 
		echo '<div class="hw-el-line"></div>';
	} 
		 
	echo hexwp_blog_config($related, true);
  	  
	$post = $orig_post;
	wp_reset_postdata();
}
 
?>