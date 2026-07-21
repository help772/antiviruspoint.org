<?php
/**
 * The template for displaying Comments
 *
 * The area of the page that contains comments and the comment form.
 *
 * @package WordPress
 * @subpackage Twenty_Thirteen
 * @since Twenty Thirteen 1.0
 */

/*
 * If the current post is protected by a password and the visitor has not yet
 * entered the password we will return early without loading the comments.
 */
global  $smof_data;
if( hexwp_option('hide_comments') !== 'hide' ){
$hexwp_comments_layout_type = isset( $smof_data['comments_layout_type']) ? $smof_data['comments_layout_type'] : 'hw-list';

if ($hexwp_comments_layout_type =='hw-thread'){
	$hexwp_comments_type = 'hw-thread';
	
 } else {
	$hexwp_comments_type = 'hw-list';
}
 
 $box_layout_single = hexwp_box_layout_single('blog');
 
   
   	?>
 
 	
	<?php
 
if ( post_password_required() )
	return;
   
     
	$get_post = get_post( get_the_ID() );
	$comment_status= !empty($get_post->comment_status)? $get_post->comment_status :'';
 ?>
  
  	<?php 
 	if($box_layout_single!=='hw-single-boxed' && $comment_status!=='closed' ){ ?>
  		<div class="hw-el-line "></div>
	<?php }?> 
    
    
	<aside class="hw-el-comments hw-comments  <?php echo esc_attr($box_layout_single);?>  <?php echo 'hw-comment-status-'.esc_attr($comment_status);?>  <?php echo esc_attr($hexwp_comments_type) ?> hw-aw <?php if ( have_comments() ) echo esc_attr('hw-have-comments');?>">
 	<div id="comments" class="comments-area   ">

	<?php if ( have_comments() ) { ?>
		<h2 class="comments-title">
	 	<span><?php comments_number(esc_html(hexwp_t('nocommentsyet')), hexwp_t('1').' '.hexwp_t('commentalready') , esc_html('% '.hexwp_t('commentsalready'))); ?></span>

		</h2>

		<ol class="comment-list <?php  echo esc_attr($hexwp_comments_layout_type)?>">
 			  <?php wp_list_comments( array( 'callback' => 'hexwp_custom_comments', 'short_ping'  => true ) ); ?>
		</ol><!-- .comment-list -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {?>
		<nav class="hw-comment-navi">
			<?php  paginate_comments_links(); ?> 
		</nav><!-- .comment-navigation -->
		<?php } ?>

		<?php if ( ! comments_open() && get_comments_number() ){ ?>
		<p class="no-comments"><?php echo esc_html( hexwp_t('commentsclosed')); ?></p>
		<?php }?>

	<?php  } ?>

	<?php comment_form(); ?>

 	</div>
	</aside>
<?php  


if( is_singular() && comments_open() && ( get_option( 'thread_comments' ) == 1) ) {
        wp_enqueue_script( 'comment-reply', 'wp-includes/js/comment-reply', array(), false, true );
}
}
?>