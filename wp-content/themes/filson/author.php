<?php get_header();?>
<?php hexwp_breadcrumbs();?>

<div class="hw-middle-content">

 	<?php hexwp_above_content();?>
	
    <div class="hw-content <?php echo esc_attr(hexwp_column('column'));?>">
                  
		<?php if(!empty(hexwp_column(1))){get_template_part('sidebar-'.hexwp_column(1));} ?>
		<?php if(!empty(hexwp_column(2))){get_template_part('sidebar-'.hexwp_column(2));} ?>
 		
        <div class="hw-column-main">
			<?php hexwp_above_center();?>
			<?php hexwp_blog_archive(get_the_archive_title()); ?>
			<?php hexwp_below_center(); ?>
		</div>
		
		<?php if(!empty(hexwp_column(3))) get_template_part('sidebar-'.hexwp_column(3)); ?>
		<?php if(!empty(hexwp_column(4))) get_template_part('sidebar-'.hexwp_column(4)); ?>   
                      	 
	</div>
 	
	<?php hexwp_below_content();?>

</div>
 
<?php get_footer();?>