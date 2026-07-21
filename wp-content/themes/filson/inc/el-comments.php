<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																El Comments
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ( post_passwohw_required() )
	return;
	global $smof_data;


?>
  
  	<div id="comments" class="comments-area     ">

	<?php if ( have_comments() ) : ;
 ?>
		<h2 class="comments-title">
	 	<span><?php comments_number(esc_html(hexwp_t('nocommentsyet')), hexwp_t('1').' '.hexwp_t('commentalready') , esc_html('% '.hexwp_t('commentsalready'))); ?></span>

		</h2>

		<ol class="comment-list ">
 			  <?php wp_list_comments( array( 'callback' => 'hexwp_custom_comments', 'short_ping'  => true ) ); ?>
		</ol><!-- .comment-list -->

		<?php
			// Are there comments to navigate through?
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
		<nav class="hw-comment-navi">
			<?php  paginate_comments_links(); ?> 
		</nav><!-- .comment-navigation -->
		<?php endif; // Check for comment navigation ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php echo esc_html( hexwp_t('commentsclosed')); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php comment_form(); ?>

 	</div>
 <?php
	wp_enqueue_script( 'comment-reply', 'wp-includes/js/comment-reply', array(), false, true );
?>