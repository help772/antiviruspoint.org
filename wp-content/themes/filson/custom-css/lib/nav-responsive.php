<?php
 $header_option = !empty($header_builder['header'])?hexwp_json_decode($header_builder['header']):'';
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
$css.=
	'
	@media (min-width: 1600px) and (max-width: 1799px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:17px !important;
			--hw-nav-pd:17px !important;
			--hw-nav-middle-pd:3px !important;
 			--hw-srh-dv:50px;
  		}
 	}
	@media (min-width: 1500px) and (max-width: 1599px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
			--hw-srh-dv:100px;
		}
 
	}

 	@media (min-width: 1400px) and (max-width: 1499px){
		[class*="hw-toolbar-"]{
 		--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important;
			--hw-srh-dv:200px;
			--hw-nav-middle-pd:7px !important;
 
		}
		 
		
	}
	@media (min-width: 1300px) and (max-width: 1399px){
		[class*="hw-toolbar-"]{
 			--hw-nav-menu-pd:11px !important;
			--hw-nav-pd:11px !important;
			--hw-srh-dv:300px;
 		}			 
		 
	}
	@media (min-width: 1200px) and (max-width: 1299px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:9px !important;
			--hw-nav-pd:9px !important;
			--hw-srh-dv:400px;
			--hw-nav-middle-pd:11px !important;
 		}
		 
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:7px !important;
			--hw-nav-pd:7px !important;
			--hw-srh-dv:500px;
  			--hw-nav-fn-sz:12px!important; 
			--hw-nav-middle-pd:13px !important;
 		}
	 
	}
	@media (min-width: 1024px) and (max-width: 1099px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:5px !important;
			--hw-nav-pd:5px !important;
			--hw-srh-dv:600px;
			--hw-nav-fn-sz:11px!important; 
			--hw-nav-middle-pd:15px !important;
 		}
   	}	 
	';
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1600px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($header_width == '1600px'){
$css.=
	'
	@media (min-width: 1500px) and (max-width: 1599px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:17px !important;
			--hw-nav-pd:17px !important;
			--hw-srh-dv:50px;
			--hw-nav-middle-pd:3px !important;
		}
 
	}
	@media (min-width: 1400px) and (max-width: 1499px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
 			--hw-srh-dv:100px;
			--hw-nav-middle-pd:5px !important;
		}
		 

		
	}
 
 	@media (min-width: 1300px) and (max-width: 1399px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important; 
			--hw-srh-dv:200px;
			--hw-nav-middle-pd:7px !important;

			
 		}
		 
	}
	@media (min-width: 1200px) and (max-width: 1299px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px  !important;
			--hw-nav-pd:11px  !important; 
			--hw-srh-dv:300px;
			--hw-nav-middle-pd:9px !important;
 		}
	 
		
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
		[class*="hw-toolbar-"]{
  			--hw-nav-menu-pd:9px  !important;
			--hw-nav-pd:9px  !important;
			--hw-srh-dv:400px;
  			--hw-nav-fn-sz:12px!important; 
			--hw-nav-middle-pd:11px !important;
  		}
	 
	}
	@media (min-width: 1025px) and (max-width: 1099px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:7px  !important;
			--hw-nav-pd:7px  !important;
			--hw-srh-dv:500px;
  			--hw-nav-fn-sz:11px!important;
			--hw-nav-middle-pd:13px !important;
 		}
   	}
	 
	';


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1500px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($header_width == '1500px'){
$css.=
	'
	@media (min-width: 1400px) and (max-width: 1499px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
			--hw-srh-dv:50px;
			--hw-nav-middle-pd:5px !important;
		}
	 
		
	}
 	@media (min-width: 1300px) and (max-width: 1399px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px !important;
			--hw-nav-pd:13px !important;
 			--hw-srh-dv:100px;
			--hw-nav-middle-pd:7px !important;
		}
 
	}
	
 	@media (min-width: 1200px) and (max-width: 1299px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px !important;
			--hw-nav-pd:11px !important;
			--hw-srh-dv:200px;
			--hw-nav-middle-pd:9px !important;
 		}
 
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
		[class*="hw-toolbar-"]{
  			--hw-nav-menu-pd:9px !important;
			--hw-nav-pd:9px !important;
			--hw-srh-dv:300px;
			--hw-nav-fn-sz:12px!important; 
			--hw-nav-middle-pd:11px !important;
			
 		}
 
		
	}
	@media (min-width: 1025px) and (max-width: 1099px){
		[class*="hw-toolbar-"]{
 			--hw-nav-menu-pd:7px !important;
			--hw-nav-pd:7px !important;
			--hw-srh-dv:400px;
			--hw-nav-fn-sz:11px!important; 
			--hw-nav-middle-pd:13px !important;
 		}
 	}

	';

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1400px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($header_width == '1400px'){
$css.=
	'
	@media (min-width: 1300px) and (max-width: 1399px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px !important;
 			--hw-srh-dv:50px;
			--hw-nav-middle-pd:5px !important;
		}
 
		
	}
	@media (min-width: 1200px) and (max-width: 1299px){
		[class*="hw-toolbar-"]{
 		--hw-nav-menu-pd:13px !important;
		--hw-nav-pd:13px !important;
 		 --hw-srh-dv:100px;
			--hw-nav-middle-pd:7px !important;
		}
 
  	}
  	@media (min-width: 1100px) and (max-width: 1199px){
		[class*="hw-toolbar-"] {
			--hw-nav-menu-pd:11px !important;
			--hw-nav-pd:11px !important;
				--hw-srh-dv:200px;
			--hw-nav-fn-sz:12px!important;
			--hw-nav-middle-pd:9px !important;
		}
	  
		
	}
	@media (min-width: 1025px) and (max-width: 1099px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:9px !important;
			--hw-nav-pd:9px !important;
					--hw-srh-dv:300px;
			--hw-nav-fn-sz:11px!important; 
			--hw-nav-middle-pd:11px !important;
 		}
	 
		
	}

	';
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1300px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($header_width== '1300px'){
$css.=
	'
	@media (min-width: 1200px) and (max-width: 1299px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px  !important;
 			--hw-srh-dv:50px;
			--hw-nav-middle-pd:5px !important;
		}
 
		
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px  !important;
			--hw-nav-pd:13px  !important;
 			--hw-srh-dv:100px;
			--hw-nav-fn-sz:12px!important;
			--hw-nav-middle-pd:7px !important;
		}
 
		
	}
	@media (min-width: 1025px) and (max-width: 1099px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:11px  !important;
			--hw-nav-pd:11px  !important;
			--hw-nav-fn-sz:11px!important; 
			--hw-srh-dv:200px;
			--hw-nav-middle-pd:9px !important;
		}
  	}
	';
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1200px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($header_width == '1200px'){
$css.=
	'
	@media (min-width: 1100px) and (max-width: 1199px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px !important;
			--hw-nav-pd:15px  !important;
  			--hw-srh-dv:50px;
			--hw-nav-fn-sz:12px!important; 
			--hw-nav-middle-pd:5px !important;
		}
 
	}
	@media (min-width: 1025px) and (max-width: 1099px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:13px  !important;;
			--hw-nav-pd:13px  !important;
 			--hw-nav-fn-sz:12px  !important;	
			--hw-srh-dv:100px;
			--hw-nav-fn-sz:12px!important; 
			--hw-nav-middle-pd:7px !important;
		}
 	}
	';
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1100px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		
}elseif($header_width == '1100px'){
$css.=
	'@media (min-width: 1025px) and (max-width: 1100px){
		[class*="hw-toolbar-"]{
			--hw-nav-menu-pd:15px;
			--hw-nav-fn-sz:12px!important; 
			--hw-srh-dv:50px;
			--hw-nav-middle-pd:5px !important;
		}
	 
 	}';
 
}
 
 
 