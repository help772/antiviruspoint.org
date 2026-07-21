<?php 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Widget Product
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action( 'widgets_init', 'hexwp_register_widget_product' );
 function hexwp_register_widget_product() {
    register_widget( 'hexwp_widget_product' );
}

class hexwp_widget_product extends WP_Widget {
 	function __construct() {
		parent::__construct(
			hexwp_slug().'_product',
			 __('hexwp','hexwp').' - '. esc_html__('Product', 'hexwp') 
		);
	}
public  function option(){
 
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
 		"options"		=>  hexwp_category_array_options('product_cat'),						
 	);
		
	$option[]= array( 
		"name"			=> esc_html__('Orderby','hexwp'),
 		"id"			=> "orderby",
  		"type"			=> "select",
		"options"		=>  hexwp_array_options('product_orderby'),						
 	); 
	 
	 
	$option[]= array( 
		"name"			=> esc_html__('Limit Title length','hexwp'),
 		"id"			=> "title_limit",
		"desc"			=>  esc_html__('example: "100"','hexwp'),
 	
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
 	 
		"desc"			=>  esc_html__('example: "200"','hexwp'),
   		"type"			=> "number",
   	); 
 	$option[]= array( 
		"name"			=> esc_html__('Show Countdown Sale Timer','hexwp'),
 		"id"			=> "countdown",
		"type"			=> "checkbox",
   	); 
	
	$option[]= array( 
		"name"			=> esc_html__('Show Category Meta','hexwp'),
 		"id"			=> "meta_category",
		"type"			=> "checkbox",
 		
   	);
 
	$option[]= array( 
		"name"			=> esc_html__('Show Rating','hexwp'),
 		"id"			=> "rating",
    	"type"			=> "checkbox",
   	); 
	
	$option[]= array( 
		"name"			=> esc_html__('Show Add to Cart','hexwp'),
 		"id"			=> "addcart",
		"type"			=> "checkbox",
 	);	
	
	$option[]= array( 
		"name"			=> esc_html__('Show Add to Cart,Wishlist,Campare in Hover','hexwp'),
 		"id"			=> "addcart_hover",
		"type"			=> "checkbox",
 	);		
	
	$option[]= array( 
			"name"			=> esc_html__('Layout','hexwp'),
			"id"			=> "layout",
			"type"			=> "select",
			"options"		=>  array( 
				"list"		=> __('List','hexwp'),
				"grid"		=> __('Grid','hexwp'),
 			),						
					  
		); 
			
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
		"name"			=> esc_html__('Space Between Item','hexwp'),
 		"id"			=> "between",
 		"group"			=>  esc_html__('Layout','hexwp'),
		"type"			=> "select",
			"options"		=>  hexwp_array_options('between',true),						

  	);
	
 
	$option[]= array( 
		"name"			=> esc_html__('Image Ratio','hexwp'),
 		"id"			=> "ratio",
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
		"name"			=> esc_html__('Second Image','hexwp'),
 		"id"			=> "second_image",
 		"type"			=> "checkbox",
   	); 	 
	
	$option[]= array( 
		'name'			=> esc_html__('Details Alignment','hexwp'),
 		'id'			=> 'alignment',
 		'type'			=> 'select',
			"options"		=>  hexwp_array_options('alignment',true),						
				
		 
	); 
	$option[]= array( 
		"name"			=> esc_html__('Box Layout','hexwp'),
 		"id"			=> "box_layout",
		"type"			=> "select",
			"options"		=>  hexwp_array_options('box_layout',true),						

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
		$defaults = array(  'title'=>__('Title' , 'hexwp' ), 'number' => '5', 'image_size' => 'full' );
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
	
 		$args['key'] = 'widget_product';
 		$column = !empty( $instance['column'] ) ? $instance['column'] : '1';

		$args['option'] =array(
 			'title' => !empty( $instance['title'] ) ? $instance['title'] : '',
 			'number' => !empty( $instance['number'] ) ? $instance['number'] : '',
 			'cats' => !empty( $instance['cats'] ) ? $instance['cats'] : '',
			'post_title' => 1,
   			'excerpt' => !empty( $instance['excerpt'] ) ? $instance['excerpt'] : '',
  			'excerpt_limit' => !empty( $instance['excerpt_limit'] ) ? $instance['excerpt_limit'] : '',
			'title_limit' => !empty( $instance['title_limit'] ) ? $instance['title_limit'] : '',
			'countdown' => !empty( $instance['countdown'] ) ? $instance['countdown'] : '', 
			'meta_category' => !empty( $instance['meta_category'] ) ? $instance['meta_category'] : '', 
 			'image_width' => !empty( $instance['image_width'] ) ? $instance['image_width'] : '', 
			'rating' => !empty( $instance['rating'] ) ? $instance['rating'] : '', 
			'addcart' => !empty( $instance['addcart'] ) ? $instance['addcart'] : '', 
			'addcart_hover' => !empty( $instance['addcart_hover'] ) ? $instance['addcart_hover'] : '', 
 			'between'=> !empty( $instance['between'] ) ? $instance['between'] : '',	
			'layout' => !empty( $instance['layout'] ) ? $instance['layout'] : 'list',
 			'image_size' => !empty( $instance['image_size'] ) ? $instance['image_size'] : 'full',
			'list_layout' 		=>  'list_'.$column,
			'grid_layout'	 	=>  'grid_'.$column,
  			'responsive_column'=> !empty( $instance['responsive_column'] ) ? $instance['responsive_column'] : '',	
			'second_image' => !empty( $instance['second_image'] ) ? $instance['second_image'] : '', 
			'alignment' => !empty( $instance['alignment'] ) ? $instance['alignment'] : '', 
			'box_layout' => !empty( $instance['box_layout'] ) ? $instance['box_layout'] : '', 
			
		) ; 
 
		global $smof_data; 
		
		echo hexwp_product_config($args, true);
  
 	}

 
}