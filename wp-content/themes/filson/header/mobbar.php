<?php 

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Mobile Bar
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_mobbar($header_builder=false){
	
$opt = !empty($header_builder['mobbar'])?hexwp_json_decode($header_builder['mobbar']):'';
 	
	?>


 <div class="hw-mobbar">
 <div class="hw-mobbar-wrapper">
   	<a class="hw-mobbar-close"></a>
   <div class="hw-mobbar-warp">
   
   <div class="hw-mobbar-middle">
   </div>
   <div class="hw-mobbar-footer">
   <?php  if(!empty($opt['contact_us'])) hexwp_nav_mobbar_contact_us($opt);?>
   <?php if(!empty($opt['call']))  hexwp_nav_mobbar_call($opt);?>
   <?php  if(!empty($opt['text_html'])) hexwp_nav_mobbar_text_html($opt);?>
   <?php  if(!empty($opt['social'])) hexwp_nav_mobbar_social($opt);?>
   </div>
   </div>
</div>
</div>

<?php
}
