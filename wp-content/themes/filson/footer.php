<?php global $smof_data;?>
<?php
	$hexwp_page_top_footer = hexwp_page_top_footer('output');
	if(!empty($hexwp_page_top_footer)){
	echo hexwp_page_top_footer('output');
	}
 ?>

 
</section> 
<div id="hw-footer">

	<?php 
	$hexwp_page_footer = hexwp_page_footer('output');
	if(!empty($hexwp_page_footer)){
		echo hexwp_page_footer('output');
	}else{
	?>  
		<div class="hw-middle-footer">
   			<?php hexwp_footer_content();?>
  		</div>
        
 		<?php hexwp_footer_bottom();?>
        
	<?php } ?> 

</div> 
 



</div>
  <footer><?php wp_footer(); ?></footer>
    

</body>
</html>
