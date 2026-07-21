<?php
if( ! function_exists( 'hexwpdemoimport_menu_import_json' ) ) {

 function hexwpdemoimport_menu_import_json( $file, $mode = 'append', $missing = 'skip', $default = null ) {
		$string      = json_encode($file) ;

		
		$json_menus = json_decode( $string );

		// $json object may contain a single menu definition object or array of menu objects
		if ( ! is_array( $json_menus ) ) {
			$json_menus = array( $json_menus );
		}

		$locations = get_nav_menu_locations();

		foreach ( $json_menus as $menu ) :
 			 if ( isset( $menu->name ) ) :
				// If we can't find a menu by this name, create one.
				if ( $menu_object = wp_get_nav_menu_object( $menu->name ) ) :
					$menu_id = $menu_object->term_id;
				else :
					$menu_object = wp_create_nav_menu( $menu->name );
					if ( isset( $menu_object->term_id ) ) {
						$menu_id = $menu_object->term_id;
					} else {
						continue;
					}
				endif;
			else : // if no location or name is supplied, we have nowhere to put any additional info in this object.
				continue;
			endif;

			$new_menu = array();

			if ( isset ( $menu->items ) && is_array( $menu->items ) ) : foreach ( $menu->items as $item ) :

				// merge in existing items here

				// Build $item_array from supplied data
				$item_array = array(
					'menu-item-title' => ( isset( $item->title ) ? $item->title : false ),
					'menu-item-status' => 'publish'
				);

				if ( isset( $item->page ) && $page = get_page_by_path( $item->page ) ) { // @todo support lookup by title
					$item_array['menu-item-type']      = 'post_type';
					$item_array['menu-item-object']    = 'page';
					$item_array['menu-item-object-id'] = $page->ID;
					$item_array['menu-item-title']     = ( $item_array['menu-item-title'] ) ?: $page->post_title;
				} elseif ( isset ( $item->taxonomy ) && isset( $item->term ) && $term = get_term_by( 'name', $item->term, $item->taxonomy ) ) {
					$item_array['menu-item-type']      = 'taxonomy';
					$item_array['menu-item-object']    = $term->taxonomy;
					$item_array['menu-item-object-id'] = $term->term_id;
					$item_array['menu-item-title'] = ( $item_array['menu-item-title'] ) ?: $term->name;
				} elseif ( isset( $item->url ) ) {
					$item_array['menu-item-url']   = ( 'http' == substr( $item->url, 0, 4 ) ) ? esc_url( $item->url ) : home_url( $item->url );
					$item_array['menu-item-title'] = ( $item_array['menu-item-title'] ) ?: $item->url;
				} else {
					continue;
				}

				$slug  = isset( $item->slug ) ? $item->slug : sanitize_title_with_dashes( $item_array['menu-item-title'] );
				$new_menu[$slug] = array();

				if ( isset( $item->parent ) ) {
					$new_menu[$slug]['parent']         = $item->parent;
					$item_array['menu-item-parent-id'] = isset( $new_menu[ $item->parent ]['id'] ) ? $new_menu[ $item->parent ]['id'] : 0 ;
				}

				$new_menu[$slug]['id'] = wp_update_nav_menu_item($menu_id, 0, $item_array );

				// if current user doesn't have caps to insert term (because we are doing cli) then we need to handle that here
				wp_set_object_terms( $new_menu[$slug]['id'], array( (int) $menu_id ), 'nav_menu' );

			endforeach; endif;


		endforeach;
		
 }
}
if( ! function_exists( 'hexwpdemoimport_menu_export_json' ) ) {

function hexwpdemoimport_menu_export_json( ) {

	$locations = get_nav_menu_locations();
		$menus     = wp_get_nav_menus();
		$exporter  = array();

		foreach ( $menus as $menu ) :
			$export_menu = array(
				'location' => array_search( $menu->term_id, $locations ),
				'name'     => $menu->name,
				'slug'     => $menu->slug
			);

			$items = wp_get_nav_menu_items( $menu );
			foreach ( $items as $item ) :
				$export_item = array(
					'slug'   => $item->ID,
					'parent' => $item->menu_item_parent,
					'title'  => $item->title,
				);

				switch ( $item->type ) :
					case 'custom':
						$export_item['url'] = $item->url;
						break;
					case 'post_type':
						if ( 'page' == $item->object ) {
							$page = get_post( $item->object_id );
							$export_item['page'] = $page->post_name;
						}
						break;
					case 'taxonomy':
						$term = get_term( $item->object_id, $item->object );
						$export_item['taxonomy'] = $term->taxonomy;
						$export_item['term']     = $term->name;
						break;
				endswitch;

				$export_menu['items'][] = $export_item;
			endforeach;

			$exporter[] = $export_menu;
		endforeach;

		$json_menus = json_encode( $exporter );

 

		return $exporter;
	}
}