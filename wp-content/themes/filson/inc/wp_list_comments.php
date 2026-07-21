<?php
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Custom Comments
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_comments( $comment, $args, $depth ) {
			global $smof_data,$post;

		$time_format = hexwp_option('time_format');	
	$GLOBALS['comment'] = $comment ;
 switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
 	?>
	<li class="hw-pingback"><?php echo esc_html( hexwp_t('pingback')) ; ?> <?php comment_author_link(); ?><?php edit_comment_link( '('.hexwp_t('edit').')', '<span class="edit-link">', '</span>' ); ?>
	</li>
	<?php
	 	break;
		 default :
	?>
	<li id="comment-<?php comment_ID(); ?>">
		<div  <?php comment_class('comment-wrap'); ?> >
			<div class="comment-avatar"><div class="avater"><?php echo get_avatar( $comment, 70 ); ?></div></div>
			<div class="author-comment">
            			<div class="author-link">

				<?php printf(  '%s ', sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) );  
			 
				if (  $depth =='1'){}else {?>
 		 <div class="author-link-reply"><?php $pcom = get_comment($comment->comment_parent);?><a href="<?php echo get_comment_link($comment->comment_parent)?>">: @<?php echo esc_html($pcom->comment_author); ?></a></div><?php }?>
 
 	</div>
				<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>"><?php  	if(  $time_format == 'traditional'  ){	 
					printf( '%1$s '.esc_html(hexwp_t('at')).' %2$s', get_comment_date(),  get_comment_time() ); 

 				}else{
 						echo hexwp_number_replace(hexwp_elapsed_string( strtotime("{$comment->comment_date_gmt} GMT"))) ;
 
 				}
				?></a><?php edit_comment_link(  '('.hexwp_t('edit').')', ' ' ); ?></div><!-- .comment-meta .commentmetadata -->
			</div>
			<div class="clear"></div>
			<div class="comment-content hw-single-content">
				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php echo esc_html( hexwp_t('hexwp_t_yourcomment')); ?></em>
					<br />
				<?php endif; ?>
					
				<?php comment_text(); ?>
			</div>
			<div class="reply"><?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></div><!-- .reply -->
		</div><!-- #comment-##  -->

	<?php
	 break;
	 endswitch;
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		Custom Pings
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_custom_pings($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment; ?>
	<li class="comment pingback">
		<p><?php echo esc_html( hexwp_t('hexwp_t_yourcomment')); ?><?php comment_author_link(); ?><?php edit_comment_link(   '('.hexwp_t('edit').')', ' ' ); ?></p>
<?php	
}
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Pings Back Header
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'hexwp_pingback_header' );
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																		 Elapesd Stings
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return __('0 Second','hexwp');
    }

    $a = array( 365 * 24 * 60 * 60  => 'year' ,
                 30 * 24 * 60 * 60  => 'month' ,
                      24 * 60 * 60  => 'day' ,
                           60 * 60  => 'hour' ,
                                60  => 'minute' ,
                                 1  => 'second' ,
                );
    $a_dates = array( 'year'   => esc_html(hexwp_t('years')),
                       'month'  => esc_html(hexwp_t('months')),
                       'day'    => esc_html(hexwp_t('days')),
                       'hour'   => esc_html(hexwp_t('hours')),
                       'minute' => esc_html(hexwp_t('minutes' )),
                       'second' => esc_html(hexwp_t('seconds')),
                );
    $a_date = array( 'year'   => esc_html(hexwp_t('year')),
                       'month'  => esc_html(hexwp_t('month')),
                       'day'    => esc_html(hexwp_t('day')),
                       'hour'   => esc_html(hexwp_t('hour')),
                       'minute' => esc_html(hexwp_t('minute' )),
                       'second' => esc_html(hexwp_t('second')),
                );


    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_dates[$str] : $a_date[$str]) . " ". esc_html(hexwp_t('ago'));
        }
    }
}  