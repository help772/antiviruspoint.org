<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Header
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 add_filter('vh_header_perview', 'hexwp_header');
function hexwp_header($header_builder=false){
	
  	?>
 
<?php hexwp_mobbar($header_builder);?> 
<header class="hw-bar">

	<?php	  
 	 /****************************************************************************************
	 NavBar Start
	 *****************************************************************************************/
	if(!empty($header_builder['navbar'])){
	foreach(hexwp_header_decode($header_builder['navbar']) as $navbar_key => $navbar_value):
		$navbar_key=!empty(	$navbar_key) ? $navbar_key:'';
		if(hexwp_has_toolbar($navbar_key,$header_builder)){
			 

  		$navbar_option = !empty($navbar_value['option'])?hexwp_json_decode($navbar_value['option']):'';
   		$overlap=!empty($navbar_option['overlap'] )  && hexwp_header_overlap()=='enable'?'hw-overlap':'';
			
		 $sticky = hexwp_header_sticky($navbar_key,$navbar_option);
  		 $flex = !empty($navbar_option['layout'] )?'hw-toolbar-'.$navbar_option['layout']:'';
  		 $layout = !empty($navbar_option['layout'] )?$navbar_option['layout']:'';
		
		
  
			
		?>
        
        
 		 <div class="hw-toolbar-<?php echo esc_attr($navbar_key.' '.$sticky.' '.$overlap.' '.$flex);?> " >
			<div class="hw-middle-toolbar">
            
            
            
            	<?php
				 /****************************************************************************************
				 Column
				 *****************************************************************************************/
				foreach(hexwp_header_column() as $column_key => $column_value):
					$column_key=!empty($column_key)?$column_key:'';
					if( $column_value['child']==$navbar_key){
						
						
					if( hexwp_has_column($flex ,$column_key,$header_builder)){
					 
					 
					 if($layout!=='flex-center'){
					?>
 					<div class="hw-col-<?php echo esc_attr($column_value['side']);?>">
					
					
							<?php }
							/****************************************************************************************
							 Element Start
							 *****************************************************************************************/ 
							if(!empty($header_builder['element'])){
							foreach(hexwp_header_decode($header_builder['element']) as $element_key => $element_value):
								$element_key=!empty($element_key)?$element_key:'';
								$element_childern = !empty($element_value['childern'])?$element_value['childern']:'';
								if($element_childern==$column_key){
									
									$args = !empty($element_value['option'])?hexwp_json_decode($element_value['option']):'';
									$args['key'] = !empty($element_key)?$element_key:'';
									if($layout =='flex-center'){
 										$args['side'] = !empty($column_value['side'])? 'hw-col-'.$column_value['side']:'';
									}
									$element_id = !empty($element_value['id'])?$element_value['id']:'';
									$mobile='';
									if($navbar_key =='mobile_top'|| $navbar_key =='mobile_middle' || $navbar_key =='mobile_bottom'){
										$mobile='mobile_';
									}
									if(has_filter('hexwp_header_builder_'.$mobile.$element_id)) {
										apply_filters('hexwp_header_builder_'.$mobile.$element_id,  $args);
									}
									
								}
							endforeach;
							}
							
						if($layout!=='flex-center'){
 						   ?>
						</div>
                  <?php 
						}
					}
					
				 /****************************************************************************************
				 Column END
				 *****************************************************************************************/ 
				}
				endforeach;?>
						 
            	
  			</div>
		</div>    
     <?php 
		}
	 /****************************************************************************************
	 NavBar END
	 *****************************************************************************************/ 
	 endforeach;
	}
// }
 ?>
  
	
</header>
<?php }
