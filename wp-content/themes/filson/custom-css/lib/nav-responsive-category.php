<?php
 $body_width= '1600px';

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Full Width
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if($body_width == '1800px' || $body_width == '1920px' ||$body_width == '100%' ){
$css.=
	' @media (min-width: 1700px) and (max-width: 1799px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.91  !important;
  		 }
 	}
	
	 @media (min-width: 1600px) and (max-width: 1699px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.75  !important;
  		 }
 	}
	@media (min-width: 1500px) and (max-width: 1599px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.78  !important;
			--hw-menu-fn-sz: 13px  !important;
  		 }
	}
	@media (min-width: 1400px) and (max-width: 1499px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.68  !important;
			--hw-menu-fn-sz: 13px  !important;
		 }

		
	}
 
 	@media (min-width: 1300px) and (max-width: 1399px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.65 !important;
			--hw-menu-fn-sz: 12px  !important;
 		 }
	}
	@media (min-width: 1200px) and (max-width: 1299px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.55  !important;
			--hw-menu-fn-sz:12px !important; 

		 }
		
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.51  !important;
			--hw-menu-fn-sz:11px !important; 
 		 }
	}
	@media (min-width: 1025px) and (max-width: 1099px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.43  !important;
			--hw-menu-fn-sz:11px !important; 
 		 }
		
 	}
 	
	
	';
	
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1600px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($body_width == '1600px'){
$css.=
	'
	@media (min-width: 1500px) and (max-width: 1599px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.88  !important;
  		 }
	}
	@media (min-width: 1400px) and (max-width: 1499px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.82  !important;
			--hw-menu-fn-sz: 13px  !important;
		 }

		
	}
 
 	@media (min-width: 1300px) and (max-width: 1399px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.74 !important;
			--hw-menu-fn-sz: 13px  !important;
 		 }
	}
	@media (min-width: 1200px) and (max-width: 1299px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.69  !important;
			--hw-menu-fn-sz:12px !important; 

		 }
		
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.60  !important;
			--hw-menu-fn-sz:12px !important; 
 		 }
	}
	@media (min-width: 1025px) and (max-width: 1099px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.56  !important;
			--hw-menu-fn-sz:11px !important; 
 		 }
		
 	}
	 
	';


/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1500px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($body_width  == '1500px'){
$css.=
	'
	@media (min-width: 1400px) and (max-width: 1499px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.95  !important;
			--hw-menu-fn-sz:13px !important; 

		 }
		
	}
 	@media (min-width: 1300px) and (max-width: 1399px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.82  !important;
			--hw-menu-fn-sz:13px !important; 

		 }
		
	}
	
 	@media (min-width: 1200px) and (max-width: 1299px){
 		.hw-cat-drop{
			--hw-menu-pd: 0.77 !important;
			--hw-menu-fn-sz:12px !important; 

		 }
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
 		.hw-cat-drop{
			--hw-menu-pd: 0.66  !important;
			--hw-menu-fn-sz:12px !important; 
 		 }		
		
	}
	@media (min-width: 1025px) and (max-width: 1099px){
 		.hw-cat-drop{
			--hw-menu-pd: 0.63  !important;
			--hw-menu-fn-sz:11px !important; 
 		 }
 	}

	';

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1400px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($body_width  == '1400px'){
$css.=
	'
	@media (min-width: 1300px) and (max-width: 1399px){
 		.hw-cat-drop{
			--hw-menu-pd: 0.93  !important;
			--hw-menu-fn-sz:13px !important; 
 		 }
		
	}
	@media (min-width: 1200px) and (max-width: 1299px){
 
	.hw-cat-drop{
			--hw-menu-pd: 0.87  !important;
			--hw-menu-fn-sz:12px !important; 
 		 }
		
 	}
  	@media (min-width: 1100px) and (max-width: 1199px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.75  !important;
			--hw-menu-fn-sz:12px !important; 
 		 }
		
	}
	@media (min-width: 1025px) and (max-width: 1099px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.71  !important;
			--hw-menu-fn-sz: 11px !important;
 		 }
		
	}

	';
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1300px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($body_width  == '1300px'){
$css.=
	'
	@media (min-width: 1200px) and (max-width: 1299px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.93  !important;
			--hw-menu-fn-sz: 13px !important;
 		 }	
		
 	}
	@media (min-width: 1100px) and (max-width: 1199px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.85  !important;
			--hw-menu-fn-sz: 12px !important;
 		 }
		
	}
	@media (min-width: 1025px) and (max-width: 1099px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.80  !important;
			--hw-menu-fn-sz: 11px !important;
 		 }
		
	}
	';
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1200px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
}elseif($body_width  == '1200px'){
$css.=
	'
	@media (min-width: 1100px) and (max-width: 1199px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.92  !important;
			--hw-menu-fn-sz: 13px !important;
 		 }
	}
	@media (min-width: 1025px) and (max-width: 1099px){
 
		.hw-cat-drop{
			--hw-menu-pd: 0.86  !important;
			--hw-menu-fn-sz: 12px !important;
 		 }
		
	}
	';
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	1100px
 
*/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		
}elseif($body_width  == '1100px'){
$css.=
	'@media (min-width: 1025px) and (max-width: 1100px){
 			.hw-cat-drop{
			--hw-menu-pd: 0.94  !important;
			--hw-menu-fn-sz: 13px !important;
 		 }
 	}';
 
}
 
 
 