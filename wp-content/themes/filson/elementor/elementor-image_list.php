<?php
 
class hexwp_element_image_list extends \Elementor\Widget_Base {

 
	public function get_name() {
		return hexwp_slug().'_image_list';
	}

 
	public function get_title() {
		return __( 'Image List', 'hexwp' );
	}

 
	public function get_icon() {
		return 'eicon-post-list';
	}
	public function get_categories() {
		return [ 'hexwp' ];
	}


protected function register_controls() {
  		$this->register_controls_general();
		$this->register_controls_layout();
		 
  		 
 $this->register_controls_post_style();
  $this->register_controls_typography(); 
 
		//hover animation
 		 //end style tab
	}
    


 
	 
	protected function register_controls_general(){
 		include hexwp_PATH . '/elementor/image/elementor-image-general.php';  
 	}
	
	protected function register_controls_layout(){
 		include hexwp_PATH  . '/elementor/image/elementor-image-layout.php';  
 	}
 
  

 	
	protected function register_controls_post_style(){
 		include hexwp_PATH . '/elementor/image/elementor-image-style.php';  
 	}	
 
	
 
	protected function register_controls_typography(){
 		include  hexwp_PATH . '/elementor/image/elementor-image-typography.php';  
 	}	  
	protected function render() {
 		$option = $this->get_settings_for_display();
 		$args=array();
		$key= $this->get_id();
		
 		$option['layout']='list';
		$layout =!empty($option['layout'])?$option['layout']:'list';
 		$between_class =!empty($option['between'])? $option['between']:hexwp_option('blog_between');
  		$image_width =!empty($option['image_width'])? $option['image_width']:hexwp_option('blog_image_width');
		$box_layout =!empty($option['box_layout'])? $option['box_layout']:'boxed-all';
	
		$list_layout =!empty($option['list_layout'])?$option['list_layout']:'list_4';
	
		if($list_layout=='list_5'){
 			$option['list_layout']='list_5c';
		}elseif($list_layout=='list_6'){
 			$option['list_layout']='list_6c';
		}
	
		$layout_class='';
		$layout_class ='hw_img_width_'.$image_width;
		$layout_class.= ' hw-'.$box_layout; 	
		 
		
		$classes = array(
			'hw-el-'.$key,
			hexwp_between_border($option,$box_layout),
			'hw-el-image-list',
  			'hw-gap-'.$between_class,
  			$layout_class,
 
		); 
 	?>
	<div class="hw-elementor-<?php echo esc_attr($this->get_id());?>">      
	<aside <?php  hexwp_el_id($option);?> class="<?php echo esc_attr(join( ' ', $classes ));?> " >
	
			
		<div class="hw-gap-content">
		<div class="hw-gap-warp">
		<div class="hw-item-list hw-flex hw-aw <?php  echo esc_attr(hexwp_post_list_class($option));?>">
				
			<?php 
			if(!empty($option['item'])):
			foreach($option['item'] as $keys => $value):
					
				$thumbnail = !empty($value['image']['id'])? wp_get_attachment_image_src($value['image']['id'], 'full'):'';
				  	$post_thumbnail =!empty($thumbnail[0])?'':'hw-not-thumb';

				$the_permalink = !empty($value['url'])?$value['url']:'';
					
				?>
					<div class="hw-item hw-module-1 <?php echo esc_attr($post_thumbnail);?>">
					<div class="hw-post-blog" >
                                        
						<?php if(!empty($thumbnail[0])){ ?>
							<div class="hw-thumb"> 
								<a <?php if(!empty($the_permalink)){?>href="<?php echo esc_url($the_permalink) ?>"  <?php } ?> ><img src="<?php echo esc_url($thumbnail[0]); ?> "></a>
 							</div>
						<?php }?>   
                                          
                            <div class="hw-details">
                                <?php if(!empty($value['title'])){ ?>
                            	<h3 class="hw-title"><a <?php if(!empty($the_permalink)){?>href="<?php echo esc_url($the_permalink) ?>"  <?php } ?> ><?php echo esc_html($value['title']);?></a></h3>
								<?php }?>     
                                <?php if(!empty($value['content'])){ ?>
                            	<h3 class="hw-excerpt"><?php echo esc_html($value['content']);?></h3>
								<?php }?>  
                            </div>
 					</div>
					</div>
                    
				<?php 
				endforeach; 
				endif; 
				?>
					
		</div>
		</div> 
		</div> 
			
	</aside>   

  
  		 
			<?php if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {?>
                 <div class="hw-elementor-script">     
                        <script type="text/javascript">
                          (function($) {
                            'use strict';
                            jQuery(document).ready(function() {				
                                $('.hw-elementor-<?php echo esc_html($this->get_id());?>').hexwp_elementor();
        
                             });
                            })(jQuery);
                       </script>
            
                </div>
			<?php }?>	
	
    	</div>
   
		 <?php
 	}
	
} 
