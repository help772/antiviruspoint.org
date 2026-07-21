<?php /*
Plugin >> Name: WP-PageNavi
Description: Adds a more advanced paging navigation to your WordPress blog.
Version: 2.50
Author: Lester 'GaMerZ' Chan
 */
### Function: Page Navigation: Boxed Style Paging

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																Get Pagenavi
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 function hexwp_get_pagenavi( $args = array() ) {
	if ( !is_array( $args ) ) {
		$argv = func_get_args();

		$args = array();
		foreach ( array( 'before', 'after', 'options' ) as $i => $key ) {
			$args[ $key ] = isset( $argv[ $i ]) ? $argv[ $i ] : '';
		}
	}

	$args = wp_parse_args( $args, array(
		'before' => '',
		'after' => '',
		'wrapper_tag' => 'div',
		'wrapper_class' => 'wp-pagenavi',
		'options' => array(),
		'query' => $GLOBALS['wp_query'],
		'type' => 'posts',
		'echo' => true
	) );

	extract( $args, EXTR_SKIP );


    $t_page = hexwp_t('page');
    $t_of = hexwp_t('of');
    $t_first = hexwp_t('first') ;
    $t_last = hexwp_t('last') ;
	

	$options =    array(
		'pages_text'    => esc_html($t_page.' %CURRENT_PAGE% ' .$t_of .' %TOTAL_PAGES%'),
		'current_text'  => '%PAGE_NUMBER%',
		'page_text'     => '%PAGE_NUMBER%',
		'first_text'    => esc_html('&laquo; '.$t_first),
		'last_text'     =>  esc_html($t_last.' &raquo;'),
		'prev_text'     =>  '&laquo;' ,
		'next_text'     =>  '&raquo;' ,
		'dotleft_text'  =>  '...'   ,
		'dotright_text' =>   '...'  ,
		'num_pages' => 5,
		'num_larger_page_numbers' => 3,
		'larger_page_numbers_multiple' => 10,
		'always_show' => false,
		'use_pagenavi_css' => true,
		'style' => 1,
	) ;
	
	
 	$instance = new hexwp_PageNavi_Call( $args );

	list( $posts_per_page, $paged, $total_pages ) = $instance->get_pagination_args();

	if ( 1 == $total_pages && !$options['always_show'] )
		return;

	$pages_to_show = absint( $options['num_pages'] );
	$larger_page_to_show =!empty($page_options['num_larger_page_numbers'] )? absint( $page_options['num_larger_page_numbers'] ):'';
	$larger_page_multiple = absint( $options['larger_page_numbers_multiple'] );
	$pages_to_show_minus_1 = $pages_to_show - 1;
	$half_page_start = floor( $pages_to_show_minus_1/2 );
	$half_page_end = ceil( $pages_to_show_minus_1/2 );
	$start_page = $paged - $half_page_start;

	if ( $start_page <= 0 )
		$start_page = 1;

	$end_page = $paged + $half_page_end;

	if ( ( $end_page - $start_page ) != $pages_to_show_minus_1 )
		$end_page = $start_page + $pages_to_show_minus_1;

	if ( $end_page > $total_pages ) {
		$start_page = $total_pages - $pages_to_show_minus_1;
		$end_page = $total_pages;
	}

	if ( $start_page < 1 )
		$start_page = 1;

	// Support for filters to change class names
	$class_names = array(
		'pages' => apply_filters( 'wp_pagenavi_class_pages', 'pages'),
		'first' => apply_filters( 'wp_pagenavi_class_first', 'first' ),
		'previouspostslink' => apply_filters( 'wp_pagenavi_class_previouspostslink', 'previouspostslink' ),
		'extend' => apply_filters( 'wp_pagenavi_class_extend', 'extend' ),
		'smaller' => apply_filters( 'wp_pagenavi_class_smaller', 'smaller' ),
		'page' => apply_filters( 'wp_pagenavi_class_page', 'page' ),
		'current' => apply_filters( 'wp_pagenavi_class_current', 'current'),
		'larger' => apply_filters( 'wp_pagenavi_class_larger', 'larger' ),
		'nextpostslink' => apply_filters( 'wp_pagenavi_class_nextpostslink', 'nextpostslink'),
		'last' => apply_filters( 'wp_pagenavi_class_last', 'last'),
	);

	$out = '';
	switch ( intval( $options['style'] ) ) {
		// Normal
		case 1:
			// Text
			if ( !empty( $options['pages_text'] ) ) {
				$pages_text = str_replace(
					array( "%CURRENT_PAGE%", "%TOTAL_PAGES%" ),
					array( number_format_i18n( $paged ), number_format_i18n( $total_pages ) ),
					__( $options['pages_text'], 'hexwp' ) );
				$out .= "<span class='{$class_names['pages']}'>".hexwp_number_replace($pages_text)."</span>";
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				// First
				$first_text = str_replace( '%TOTAL_PAGES%', number_format_i18n( $total_pages ), esc_html__( $options['first_text'], 'hexwp' ) );
				$out .= $instance->get_single( 1, $first_text, array(
					'class' => $class_names['first'],
					'data-page' =>  1
					
				), '%TOTAL_PAGES%' );
			}

			// Previous
			if ( $paged > 1 && !empty( $options['prev_text'] ) ) {
				$out .= $instance->get_single( $paged - 1, $options['prev_text'], array(
					'class' => $class_names['previouspostslink'],
					'data-page' =>   $paged - 1,
					'rel'   => 'prev'
				) );
			}

			if ( $start_page >= 2 && $pages_to_show < $total_pages ) {
				if ( !empty( $options['dotleft_text'] ) )
					$out .= "<span class='{$class_names['extend']}'>{$options['dotleft_text']}</span>";
			}

			// Smaller pages
			$larger_pages_array = array();
			if ( $larger_page_multiple )
				for ( $i = $larger_page_multiple; $i <= $total_pages; $i+= $larger_page_multiple )
					$larger_pages_array[] = $i;

			$larger_page_start = 0;
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page < ($start_page - $half_page_start) && $larger_page_start < $larger_page_to_show ) {
					$out .= $instance->get_single( $larger_page, $options['page_text'], array(
						'class' => "{$class_names['smaller']} {$class_names['page']}",
					'data-page' =>   $larger_page,
						'title' => sprintf( esc_html__( 'Page %s', 'hexwp' ), number_format_i18n( $larger_page ) ),
					) );
					$larger_page_start++;
				}
			}

			if ( $larger_page_start )
				$out .= "<span class='{$class_names['extend']}'>{$options['dotleft_text']}</span>";

			// Page numbers
			$timeline = 'smaller';
			foreach ( range( $start_page, $end_page ) as $i ) {
				if ( $i == $paged && !empty( $options['current_text'] ) ) {
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out .= "<span aria-current='page' class='{$class_names['current']}'>".hexwp_number_replace($current_page_text)."</span>";
					$timeline = 'larger';
				} else {
					$out .= $instance->get_single( $i, $options['page_text'], array(
						'class' => "{$class_names['page']} {$class_names[$timeline]}",
					'data-page' =>   $i,
						'title' => sprintf( esc_html__( 'Page %s', 'hexwp' ), number_format_i18n( $i ) ),
					) );
				}
			}

			// Large pages
			$larger_page_end = 0;
			$larger_page_out = '';
			foreach ( $larger_pages_array as $larger_page ) {
				if ( $larger_page > ($end_page + $half_page_end) && $larger_page_end < $larger_page_to_show ) {
					$larger_page_out .= $instance->get_single( $larger_page, $options['page_text'], array(
						'class' => "{$class_names['larger']} {$class_names['page']}",
						'title' => sprintf( esc_html__( 'Page %s', 'hexwp' ), number_format_i18n( $larger_page ) ),
					'data-page' =>  $larger_page,
					) );
					$larger_page_end++;
				}
			}

			if ( $larger_page_out ) {
				$out .= "<span class='{$class_names['extend']}'>{$options['dotright_text']}</span>";
			}
			$out .= $larger_page_out;

			if ( $end_page < $total_pages ) {
				if ( !empty( $options['dotright_text'] ) )
					$out .= "<span class='{$class_names['extend']}'>{$options['dotright_text']}</span>";
			}

			// Next
			if ( $paged < $total_pages && !empty( $options['next_text'] ) ) {
				$out .= $instance->get_single( $paged + 1, $options['next_text'], array(
					'class' => $class_names['nextpostslink'] .'',
					'data-page' =>  $paged + 1,
					'rel'   => 'next'
				) );
			}

			if ( $end_page < $total_pages ) {
				// Last
				$out .= $instance->get_single( $total_pages, esc_html__( $options['last_text'], 'hexwp' ), array(
					'class' => $class_names['last'],
					'data-page' =>  $total_pages,
				), '%TOTAL_PAGES%' );
			}
			break;

		// Dropdown
		case 2:
			$out .= '<form action="" method="get">'."\n";
			$out .= '<select size="1" onchange="document.location.href = this.options[this.selectedIndex].value;">'."\n";

			foreach ( range( 1, $total_pages ) as $i ) {
				$page_num = $i;
				if ( $page_num == 1 )
					$page_num = 0;

				if ( $i == $paged ) {
					$current_page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['current_text'] );
					$out .= '<option value="'.esc_url( $instance->get_url( $page_num ) ).'" selected="selected" class="'.$class_names['current'].'">'.$current_page_text."</option>\n";
				} else {
					$page_text = str_replace( '%PAGE_NUMBER%', number_format_i18n( $i ), $options['page_text'] );
					$out .= '<option value="'.esc_url( $instance->get_url( $page_num ) ).'">'.$page_text."</option>\n";
				}
			}

			$out .= "</select>\n";
			$out .= "</form>\n";
			break;
	}
	$out = $before . "<" . $wrapper_tag . " class='" . esc_attr($wrapper_class) . "' role='navigation'>\n$out\n</" . $wrapper_tag . ">" . $after;

	$out = apply_filters( 'wp_pagenavi', $out );

	if ( !$echo )
		return $out;

	echo wp_kses_post($out);
}


class hexwp_PageNavi_Call {

	protected $args;

	function __construct( $args ) {
		$this->args = $args;
	}

	function __get( $key ) {
		return $this->args[ $key ];
	}

	function get_pagination_args() {
		global $numpages;

		$query = $this->query;

		switch( $this->type ) {
			case 'multipart':
				// Multipart page
				$posts_per_page = 1;
				$paged = max( 1, absint( get_query_var( 'page' ) ) );
				$total_pages = max( 1, $numpages );
				break;
			case 'users':
				// WP_User_Query
				$posts_per_page = $query->query_vars['number'];
				$paged = max( 1, floor( $query->query_vars['offset'] / $posts_per_page ) + 1 );
				$total_pages = max( 1, ceil( $query->total_users / $posts_per_page ) );
				break;
			default:
				// WP_Query
				$posts_per_page = intval( $query->get( 'posts_per_page' ) );
				$paged = max( 1, absint( $query->get( 'paged' ) ) );
				
				$total_pages = max( 1, absint( $query->max_num_pages ) );
				break;
		}

		return array( $posts_per_page, $paged, $total_pages );
	}

	function get_single( $page, $raw_text, $attr, $format = '%PAGE_NUMBER%' ) {
		if ( empty( $raw_text ) )
			return '';

		$text = str_replace( $format, number_format_i18n( $page ), $raw_text );

		$attr['href'] = $this->get_url( $page );

		 return hexwp_html( 'a', $attr, $text );
	}

	function get_url( $page ) {
		return ( 'multipart' == $this->type ) ? hexwp_get_multipage_link( $page ) : get_pagenum_link( $page );
	}
}
if ( ! function_exists( 'html' ) ):
function hexwp_html( $tag ) {
	static $SELF_CLOSING_TAGS = array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' );

	$args = func_get_args();

	$tag = array_shift( $args );

	if ( is_array( $args[0] ) ) {
		$closing = $tag;
		$attributes = array_shift( $args );
		foreach ( $attributes as $key => $value ) {
			if ( false === $value ) {
				continue;
			}

			if ( true === $value ) {
				$value = $key;
			}

			$tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		}
	} else {
		list( $closing ) = explode( ' ', $tag, 2 );
	}

	if ( in_array( $closing, $SELF_CLOSING_TAGS ) ) {
		return "<{$tag} />";
	}

	$content = implode( '', $args );

	return "<{$tag}>".hexwp_number_replace($content)."</{$closing}>";
}
endif;
 if ( !function_exists( 'hexwp_get_multipage_link' ) ) :
function hexwp_get_multipage_link( $page = 1 ) {
		global $post, $wp_rewrite;

		if ( 1 == $page ) {
			$url = get_permalink();
		} else {
			if ( '' == get_option('permalink_structure') || in_array( $post->post_status, array( 'draft', 'pending') ) )
				$url = add_query_arg( 'page', $page, get_permalink() );
			elseif ( 'page' == get_option( 'show_on_front' ) && get_option('page_on_front') == $post->ID )
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $wp_rewrite->pagination_base . "/$page", 'single_paged' );
			else
				$url = trailingslashit( get_permalink() ) . user_trailingslashit( $page, 'single_paged' );
		}

		return $url;
	}
endif;

### Function: Page Navigation Options 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																PageNavi Core
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class hexwp_PageNavi_Core {
	static $options;
 
	 
}
### Function: Page Navigation Options 
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																PageNavi init
																		
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_pagenavi_init() {
	global $smof_data;
    $t_page = hexwp_t('page');
    $t_of = hexwp_t('of');
    $t_first = hexwp_t('first') ;
    $t_last = hexwp_t('last') ;
	
	
	$pagenavi_options = array();
	$pagenavi_options['pages_text'] = esc_html($t_page.' %CURRENT_PAGE% ' .$t_of .' %TOTAL_PAGES%');
	$pagenavi_options['current_text'] = '%PAGE_NUMBER%';
	$pagenavi_options['page_text'] = '%PAGE_NUMBER%';
	$pagenavi_options['first_text'] = esc_html('&laquo; '.$t_first);
	$pagenavi_options['last_text'] = esc_html($t_last.' &raquo;');
	$pagenavi_options['next_text'] = esc_html('&raquo;');
	$pagenavi_options['prev_text'] = esc_html('&laquo;');
	$pagenavi_options['dotright_text'] = esc_html('...');
	$pagenavi_options['dotleft_text'] = esc_html('...');
	
	
	$pagenavi_options['num_pages'] = 5;
	
	$pagenavi_options['always_show'] = 0;
	$pagenavi_options['num_larger_page_numbers'] = 3;
	$pagenavi_options['larger_page_numbers_multiple'] = 10;
	
	return $pagenavi_options;
}
 /*****************************************************************************************************************************************************
******************************************************************************************************************************************************

																PageNavi 
																	 	
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function hexwp_pagenavi( $option = false ){
	global $smof_data; 
 	$args= array('query' => hexwp_query($option,1));
	$class= hexwp_option('pagenavi_ajax',true)?'hw-page-ajax':'';
 
 	?>
 
	<div class="hw-pagenavi <?php echo esc_attr($class);?>">
 			<?php hexwp_number_replace(hexwp_get_pagenavi( $args)) ?>
     </div>

	<?php
	
}