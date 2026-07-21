<?php get_header() ;
 
if( hexwp_single()==  '6' || hexwp_single() ==  '7' ){
	 get_template_part('inc/blog/head-single-template'); 
}?>

<?php hexwp_breadcrumbs();?>

 <div class="hw-middle-content">
 
	<?php 
	if (  hexwp_single() == '5'){
		get_template_part('inc/blog/head-single-template');
	} ?>
            
 	<?php hexwp_above_content();?>
	
    <div class="hw-content <?php echo esc_attr(hexwp_column('column'));?>">
                  
		<?php if(!empty(hexwp_column(1))){get_template_part('sidebar-'.hexwp_column(1));} ?>
		<?php if(!empty(hexwp_column(2))){get_template_part('sidebar-'.hexwp_column(2));} ?>
 		
        <div class="hw-column-main">
			<?php hexwp_above_center();?>
			<?php get_template_part('inc/blog/single-template');?>
			<?php hexwp_below_center(); ?>
		</div>
		
		<?php if(!empty(hexwp_column(3))) get_template_part('sidebar-'.hexwp_column(3)); ?>
		<?php if(!empty(hexwp_column(4))) get_template_part('sidebar-'.hexwp_column(4)); ?>   
                      	 
	</div>
 	
	<?php hexwp_below_content();?>

</div>
 
<?php get_footer();?>