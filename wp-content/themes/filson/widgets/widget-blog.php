<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Widget Blog
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'widgets_init', 'hexwp_register_widget_blog' );
function hexwp_register_widget_blog() {
    register_widget( 'hexwp_widget_blog' );
}

class hexwp_widget_blog extends WP_Widget {
 	function __construct() {
		parent::__construct(
			hexwp_slug().'_blog',
			
			__('hexwp','hexwp').' - '. esc_html__('Blog', 'hexwp') 
		);
	}
 
 
 	public  function option(){
		$option=array();
     	$option[]=array(
				'name' =>__('Title' , 'hexwp' ),
 				'id' => 'title',
 				'type' => 'text'
		);
		$option[]= array( 
			"name"			=> esc_html__('Number of Posts to show','hexwp'),
			"id"			=> "number",
 			"type"			=> "number",
			  
		);
	
		 
		$option[]= array( 
			"name"			=> esc_html__('Category','hexwp'),
			"id"			=> "cats",
			"type"			=> "select",
			"options"		=>  hexwp_category_array_options('cats'),						
		);
			
		$option[]= array( 
			"name"			=> esc_html__('Orderby','hexwp'),
			"id"			=> "orderby",
			"type"			=> "select",
			"options"		=>  hexwp_array_options('orderby'),						
		); 		
		
 		$option[]= array( 
			"name"			=> esc_html__('Limit Title length','hexwp'),
			"id"			=> "title_limit", 	
			"type"			=> "number",
		); 
		
		
		$option[]= array( 
				"name"			=> esc_html__('Show Excerpt Posts','hexwp'),
				"id"			=> "excerpt",
				"type"			=> "checkbox",
		);
		
		$option[]= array( 
			"name"			=> esc_html__('Limit Excerpt length','hexwp'),
			"id"			=> "excerpt_limit",
			"type"			=> "number",
		); 
		
		
		
		$option[]= array( 
				"name"			=> esc_html__('Show Category Meta','hexwp'),
				"id"			=> "meta_category",
				"type"			=> "checkbox",
		);
		
		$option[]= array( 
				"name"			=> esc_html__('Show Author Meta','hexwp'),
				"id"			=> "meta_author",
				"type"			=> "checkbox",
		);
			
		$option[]= array( 
				"name"			=> esc_html__('Show Date Meta','hexwp'),
				"id"			=> "meta_date",
				"type"			=> "checkbox",
		);
		
		$option[]= array( 
				"name"			=> esc_html__('Show View Meta','hexwp'),
				"id"			=> "meta_view",
				"type"			=> "checkbox",
		);	
	
		$option[]= array( 
				"name"			=> esc_html__('Show Comments Meta','hexwp'),
				"id"			=> "meta_comments",
				"type"			=> "checkbox",
		);	
		
		$option[]= array( 
			"name"			=> __('Icon in Hover post','hexwp'),
			"id"			=> "hover_post_icon",
			"type"			=> "select",
			"options"		=>  array(
									'' 		=>   __('Default', 'hexwp' ),
									'show'	=>   __('Show', 'hexwp' ),
									'hide'	=>   __('Hide', 'hexwp' ),
						)					
		);  		
		
	 /**********************Layout************************/
		$option[]= array( 
			"name"			=> esc_html__('Layout','hexwp'),
			"id"			=> "layout",
			"type"			=> "select",
			"options"		=>  array( 
				"list"		=> __('List','hexwp'),
				"grid"		=> __('Grid','hexwp'),
				"featured"	=> __('Glider','hexwp'),
			  
			),						
					  
		); 
			$column=__('Column','hexwp');
	 /**********************Layout************************/
		$option[]= array( 
			"name"			=> esc_html__('Columns','hexwp'),
			"id"			=> "column",
			"type"			=> "select",
			"options"		=> array(
					"1"	=> "1 $column", 
					"2"	=> "2 $column", 
					"3"	=> "3 $column", 
 				),
					
					  
		); 
		$option[]= array( 
			"name"			=> esc_html__('Column Width in Tablet and Mobile','hexwp'),
			"id"			=> "responsive_column",
			"type"			=> "select",
			"options" 		=> hexwp_array_options('responsive_column',true), 
 		); 	
	 
  			
		$option[]= array( 
			"name"			=> esc_html__('Space Between Item','hexwp'),
			"id"			=> "between",
			"type"			=> "select",
			"default"		=>  '',
			"options"		=>  hexwp_array_options('between',true),						
		);
		
	 
		$option[]= array( 
			"name"			=> esc_html__('Image Ratio','hexwp'),
			"id"			=> "ratio",
			"group"			=>  esc_html__('Layout','hexwp'),
			"default"		=>  'hw-ratio75',
			"type"			=> "select",
			"options"		=>  hexwp_array_options('ratio',true),						
			
		); 	 
	
		$option[]= array( 
			"name"			=> esc_html__('Image Width','hexwp'),
			"id"			=> "image_width",
			"type"			=> "select",
			"options"		=>  hexwp_array_options('image_width',true),		 
 		); 	 	
		
		$option[]= array( 
			"name"			=> esc_html__('Image Size','hexwp'),
			"id"			=> "image_size",
			"group"			=>  esc_html__('Layout','hexwp'),
			"type"			=> "select",
			"default"		=>  'full',
			"options" 		=>	hexwp_all_image_sizes(),
			 
		); 	  
	 
	 
		$option[]= array( 
			'name'			=> esc_html__('Details Alignment','hexwp'),
			'id'			=> 'alignment',
				"type"			=> "select",
		
			"options"		=>  hexwp_array_options('alignment',true),						
		 
		); 
		$option[]= array( 
			"name"			=> esc_html__('Box Layout','hexwp'),
			"id"			=> "box_layout", 		
			"type"			=> "select",
			"options"		=>  hexwp_array_options('box_layout',true),						
		); 		
	 
		$option[]= array( 
			"name"			=> esc_html__('Caption Layout','hexwp'),
			"id"			=> "caption_layout", 
			"group"			=>  esc_html__('Layout','hexwp'), 		
			"type"			=> "select",
			"options"		=>  hexwp_array_options('caption_layout',true),						
	 
			
		); 			
			
		return $option;
    }
   /********** Update the widget info from the admin panel *******/
 	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
  		return	hexwp_widget_options_save( $new_instance, $old_instance,$this->option() );
 	}
 
	/********** Display the widget update form *******/
 	public function form( $instance ) { 
		$defaults = array( 'title'=>__('Title' , 'hexwp' ),'number' => '5', 'image_size' => 'full' );
		$instance = wp_parse_args( (array) $instance, $defaults );
 
		hexwp_widget_options($instance,$this->id_base,$this->number,$this->option());
  
 	}
 
    /**********  The widget For Display *******/
 	public function widget( $args, $instance ) {
		extract( $args );
		if(!empty($instance[ 'title' ])){
 			$title = apply_filters( 'widget_title', $instance[ 'title' ] );
		}
 		$filson_data =$instance;
 		$count = 0;
		$args=array();
  		$args['key'] = 'widget_blog';
  		$column = !empty( $instance['column'] ) ? $instance['column'] : 1;
		
		$args['option'] =array(
 			'title' => !empty( $instance['title'] ) ? $instance['title'] : '',				
 			'number' => !empty( $instance['number'] ) ? $instance['number'] : '5',
 			'cats' => !empty( $instance['cats'] ) ? $instance['cats'] : '',
 			'orderby' => !empty( $instance['orderby'] ) ? $instance['orderby'] : '',
			'post_title' => 1,
			'ignore_sticky_posts'=> true,
  			'excerpt' => !empty( $instance['excerpt'] ) ? $instance['excerpt'] : '',
  			'excerpt_limit' => !empty( $instance['excerpt_limit'] ) ? $instance['excerpt_limit'] : '',
			'title_limit' => !empty( $instance['title_limit'] ) ? $instance['title_limit'] : '',
   			'meta'  =>  array( 
				'meta_category' => !empty( $instance['meta_category'] ) ? $instance['meta_category'] : '', 
				'meta_author' => !empty( $instance['meta_author'] ) ? $instance['meta_author'] : '', 
				'meta_date' =>  !empty( $instance['meta_date'] ) ? $instance['meta_date'] : '',
				'meta_view' =>  !empty( $instance['meta_view'] ) ? $instance['meta_view'] : '',
				'meta_comments' =>  !empty( $instance['meta_comments'] ) ? $instance['meta_comments'] : '',
			),
 			'hover_post_icon' => !empty( $instance['hover_post_icon'] ) ? $instance['hover_post_icon'] : '',
 			'layout' => !empty( $instance['layout'] ) ? $instance['layout'] : 'list',
			'list_layout' 		=>  'list_'.$column,
			'grid_layout'	 	=>  'grid_'.$column,
			'featured_layout' 	=>  'featured_'.$column,
 			'responsive_column'=> !empty( $instance['responsive_column'] ) ? $instance['responsive_column'] : '',	
			'between'=> !empty( $instance['between'] ) ? $instance['between'] : '',	
 			'image_size' => !empty( $instance['image_size'] ) ? $instance['image_size'] : 'full',
			'ratio' => !empty( $instance['ratio'] ) ? $instance['ratio'] : '',
 			'image_width' => !empty( $instance['image_width'] ) ? $instance['image_width'] : '',
			'alignment' => !empty( $instance['alignment'] ) ? $instance['alignment'] : '',
			'box_layout' => !empty( $instance['box_layout'] ) ? $instance['box_layout'] : '',
			'caption_layout' => !empty( $instance['caption_layout'] ) ? $instance['caption_layout'] : '',
			 
		) ; 
 
		global $smof_data; 
		?>
		<div id="<?php echo esc_attr($widget_id) ?>" class="hw-element-blog_1 hw-widget-post hw-element-item">
 			 <?php 	echo hexwp_blog_config($args, true);?>
		</div>

 		<?php 
 	}
}