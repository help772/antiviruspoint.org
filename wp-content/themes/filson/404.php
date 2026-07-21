<?php get_header();?>

<div class="hw-middle-content">
 	<?php hexwp_above_content();?>
	
    <div class="hw-content hw-main hw-404-page"> 
 		
        <div class="hw-column-main">
			<span class="hw-404"><?php echo esc_html(hexwp_t('404')); ?></span> 
			<p class="hw-opps"><?php echo esc_html(hexwp_t('opps404')); ?></p> 
			<p class="hw-opps-dese"><?php echo esc_html(hexwp_t('opps404_dese')); ?></p> 
			<div class="widget_search"><?php  get_search_form(); ?></div> 
 		</div>
		
                        	 
	</div>
 	
	<?php hexwp_below_content();?>

</div>
 
<?php get_footer();?>
