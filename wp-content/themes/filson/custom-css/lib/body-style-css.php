<?php
	
 	$item_css=
	hexwp_custom_var('--hw-fn-fm',hexwp_option('body_font_family')).
	hexwp_custom_var('--hw-body-wt',hexwp_option('body_width')).
	hexwp_custom_var('--hw-body-bg-cr',hexwp_option('body_background_color')).
 	hexwp_custom_var_background().
	hexwp_custom_var('--hw-body-bx-bg',hexwp_option('body_boxed_background')).
	hexwp_custom_var_shadow('--hw-body-bx','body_boxed_shadow');
	
	
	$item_css.=
	hexwp_custom_var_column('--hw-side-rt',hexwp_option('column_right')).
	 hexwp_custom_var_column('--hw-side-lt',hexwp_option('column_left'));

	$item_css.=
	hexwp_custom_var_column('--hw-product-side-rt',hexwp_option('product_column_right')).
	 hexwp_custom_var_column('--hw-product-side-lt',hexwp_option('product_column_left'));


  $breadcrumbs = hexwp_option('breadcrumbs_show'  ) ? hexwp_option('breadcrumbs_show'  ): hexwp_option('breadcrumbs_dsiplay'  );
	if($breadcrumbs  == 'show'){
	 $item_css.=hexwp_custom_var('--hw-brmb-txt',hexwp_option_2('breadcrumbs_color','text')).

	hexwp_custom_var('--hw-brmb-bg',hexwp_option_2('breadcrumbs_color','background'));
	}
	
	
		$item_css.=
	hexwp_custom_var_gradient_background_color('--hw-primary-bg',hexwp_option_2('primary_color','background'),hexwp_option_2('primary_color','background_2'),'135deg').

	hexwp_custom_var('--hw-primary-txt',hexwp_option_2('primary_color','text')).
	hexwp_custom_var('--hw-second-bg',hexwp_option_2('second_color','background')).
	hexwp_custom_var('--hw-second-txt',hexwp_option_2('second_color','text')).
		
	 
	hexwp_custom_var('--hw-primary-hv-bg',hexwp_rgba2hex(hexwp_option_2('primary_color','background'))).
 	hexwp_custom_var('--hw-main-lk',hexwp_option_2('main_link_color','link')).
	hexwp_custom_var('--hw-main-hv-lk',hexwp_option_2('main_link_color','hover')).
 	 hexwp_custom_var('--hw-main-lk-sd',hexwp_hex2rgbacolor(hexwp_option_2('main_link_color','link'),0.1)).
	 hexwp_custom_var('--hw-main-hv-lk-sd',hexwp_hex2rgbacolor(hexwp_option_2('main_link_color','hover'),0.2)).	
 	hexwp_custom_var('--hw-main-txt',hexwp_option('main_text_color')).
 
	hexwp_custom_var('--hw-main-hl',hexwp_option('main_highlight_color')).
	hexwp_custom_var('--hw-main-gry',hexwp_option('main_grey_color')).
	hexwp_custom_var('--hw-main-br-cr',hexwp_option('main_border_color')).
 	hexwp_custom_var('--hw-main-rd',hexwp_option('main_radius')).
	hexwp_custom_var('--hw-btn-rd',hexwp_option('button_radius')).
	 hexwp_custom_var_font_typo_mini('--hw-btn','button_typo').	
	hexwp_custom_var('--hw-scl-rd',hexwp_option('social_radius'));	
