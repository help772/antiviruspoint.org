<?php
$res_css='';
$header_option = !empty($header_builder['header_option'])?hexwp_json_decode($header_builder['header_option']):'';
 if(hexwp_option('body_layout') =='enable'){
	$header_width=hexwp_option('body_width');
}else{
	$header_width= !empty(hexwp_isset($header_option,'header_width'))?hexwp_isset($header_option,'header_width'):hexwp_option('body_width');
}
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Full Width
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($header_width == '1800px' || $header_width == '1920px' ||$header_width == '100%' ){
	$res_css.='
	.hb-1600px-1799px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:17px !important;
			--hw-nav-pd:17px !important;
			--hw-srh-dv:50px;
		
 	} 
	.hb-1500px-1599px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
			--hw-srh-dv:100px;
 	} 
	.hb-1400px-1499px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important;
			--hw-srh-dv:200px;
 	} 
	.hb-1300px-1399px	[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px !important;
			--hw-nav-pd:11px !important;
			--hw-srh-dv:300px;
   	} 
	 .hb-1200px-1299px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:9px !important;
			--hw-nav-pd:9px !important;
			--hw-srh-dv:400px;
  
	} 
	 .hb-1100px-1199px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:7px !important;
			--hw-nav-pd:7px !important;
			--hw-srh-dv:500px;
			--hw-nav-fn-sz:11px!important; 
  	} 
	.hb-1024px-1099px [class*="hw-toolbar-"]{
 			--hw-nav-menu-pd:5px !important;
			--hw-nav-pd:5px !important;
			--hw-srh-dv:600px;
			--hw-nav-fn-sz:11px!important; 
 	}
 	';
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1600px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif( $header_width == '1600px'){
	$res_css.='
 	.hb-1500px-1599px  [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:17px !important;
			--hw-nav-pd:17px !important;
 			--hw-srh-dv:50px;
   	}
 
 	.hb-1400px-1499px 	[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
 			--hw-srh-dv:100px;
  	}
	 
  	.hb-1300px-1399px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important; 
			--hw-srh-dv:200px;
  	}
 
  	.hb-1200px-1299px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px  !important;
			--hw-nav-pd:11px  !important; 
				--hw-srh-dv:300px;
  	}
 
 	.hb-1100px-1199px [class*="hw-toolbar-"]{
  			--hw-nav-menu-pd:9px  !important;
			--hw-nav-pd:9px  !important;
			--hw-srh-dv:400px;
  		 
  	}
 	.hb-1025px-1099px	[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:7px  !important;
			--hw-nav-pd:7px  !important;
			--hw-srh-dv:500px;
  			--hw-nav-fn-sz:11px !important; 
  	}
 	';


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1500px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif( $header_width == '1500px'){
$res_css.=
	'
 	.hb-1400px-1499px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
 			--hw-srh-dv:50px;
  		 }
		
  	.hb-1300px-1399px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important;
 			--hw-srh-dv:100px;
  
	}
 
	.hb-1200px-1299px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px !important;
			--hw-nav-pd:11px !important;
			--hw-srh-dv:200px;
 
	}
  
	.hb-1100px-1199px [class*="hw-toolbar-"]{
  			--hw-nav-menu-pd:9px !important;
			--hw-nav-pd:9px !important;
			--hw-srh-dv:300px;
			--hw-nav-fn-sz:12px;
  	}		
 	.hb-1025px-1099px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:7px !important;
			--hw-nav-pd:7px !important;
			--hw-srh-dv:400px;
			--hw-nav-fn-sz:11px;
 	}
	 ';

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1400px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif( $header_width == '1400px'){
	
	$res_css.=
	'
 	.hb-1300px-1399px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
 			--hw-srh-dv:50px;
  	}
  	.hb-1200px-1299px [class*="hw-toolbar-"]{
 			--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important;
 			--hw-srh-dv:100px;
  	}

 	.hb-1100px-1199px [class*="hw-toolbar-"] {
			--hw-nav-menu-pd:11px !important;
			--hw-nav-pd:11px !important;
			--hw-srh-dv:200px;
			--hw-nav-fn-sz:12px;
 	}
	.hb-1025px-1099px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:9px !important;
			--hw-nav-pd:9px !important;
			--hw-srh-dv:300px;
			--hw-nav-fn-sz:11px;
   		 }
		
	}
	 
	';
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1300px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif( $header_width == '1300px'){
$res_css.=
	'
 	.hb-1200px-1299px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px  !important;
 			--hw-srh-dv:50px;
   		 }	
		
 	}
 	.hb-1100px-1199px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px  !important;
			--hw-nav-pd:13px  !important;
 			--hw-nav-fn-sz:12px;
			--hw-srh-dv:100px;
   		 }
		
	}
 	.hb-1025px-1099px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px  !important;
			--hw-nav-pd:11px  !important;
 			--hw-nav-fn-sz:11px;	
			--hw-srh-dv:200px;
   		 }
		
	}
	';
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1200px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif( $header_width== '1200px'){
	$res_css.=
	'
	.hb-1100px-1199px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px  !important;
 			--hw-nav-fn-sz:13px;
			--hw-srh-dv:50px;
   		 }
	}
 	.hb-1100px-1199px [class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px  !important;;
			--hw-nav-pd:13px  !important;
 			--hw-nav-fn-sz:12px  !important;	
			--hw-srh-dv:100px;
   		 }
		
	}
	';
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1100px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		
}elseif( $header_width == '1100px'){
$res_css.=
 	'.hb-1100px-1199px  [class*="hw-toolbar-"]{
		--hw-nav-menu-pd:15px;
 		--hw-nav-fn-sz:12px;
		--hw-srh-dv:50px;
   	}';
 
}
 
 
 