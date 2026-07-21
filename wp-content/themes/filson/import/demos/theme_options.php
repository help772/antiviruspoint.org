<?php
  global $options_machine;
 
      // Get file contents and decode
 		$new_data= $options_machine->Defaults;
 		$old_data =	get_option('theme_mods_filson');
		$array =array();

		if(!empty($old_data)){
		foreach($old_data as $old_key => $old_value) :
				$array[$old_key] = $old_value;
  		endforeach;
		}
		
		
		if(!empty($new_data)){
		foreach($new_data as $new_key => $new_value) :
			if(isset($new_key)){
 			$array[$new_key] = $new_value;
			}
		endforeach;
		}
$type_import = !empty($_POST['type_import'])? $_POST['type_import']:'';
$a=$array;
/******************************************************************************************************************************************************
																		Theme Options Homepage 1
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($homepage_import=='theme_options_homepage_1'){
	include_once hexwp_DI_PATH . 'demos/theme_options/theme_options_homepage_1.php';
 	update_option( 'theme_mods_filson', $a );
}
/******************************************************************************************************************************************************
																		Theme Options Homepage 2
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($homepage_import=='theme_options_homepage_2'){
	include_once hexwp_DI_PATH . 'demos/theme_options/theme_options_homepage_2.php';
  	update_option( 'theme_mods_filson', $a );
} 
 if($homepage_import=='theme_options_homepage_3'){
	include_once hexwp_DI_PATH . 'demos/theme_options/theme_options_homepage_3.php';
  	update_option( 'theme_mods_filson', $a );
} 