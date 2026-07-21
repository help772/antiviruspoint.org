<?php

/**
 * Menu Item Custom Fields
 *
 * @package hexwp_hexwp_Item_Custom_Fields
 * @version 10001.0.0
 * @author  Dzikri Aziz
 *
 * Plugin name: Menu Item Custom Fields
  * Description: Easily add custom fields to nav menu items.
 * Version: 1.0.0
 * Author: Dzikri Aziz
  * License: GPLv2
 * Text Domain: menu-item-custom-fields
 */

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Class Custom Fields Menu
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class hexwp_Item_Custom_Fields {

	
		public static function load() {
			add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, '_filter_walker' ), 99 );
		}

 
		public static function _filter_walker( $walker ) {
			$walker = 'hexwp_Item_Custom_Fields_Walker';
			if ( ! class_exists( $walker ) ) {
				require_once hexwp_PATH. '/admin/menu/walker-nav-menu-edit.php';
			}

			return $walker;
		}
}
add_action( 'wp_loaded', array( 'hexwp_Item_Custom_Fields', 'load' ), 9 );

 /*

 *
 * Copy this file into your wp-content/mu-plugins directory.
 *
 * @package Menu_Item_Custom_Fields_Example
 * @version 10000.2.0
 * @author Dzikri Aziz 
 *
 *
 * Plugin name: Menu Item Custom Fields Example
  * Description: Example usage of Menu Item Custom Fields in plugins/themes
 * Version: 0.2.0
 * Author: Dzikri Aziz
  * License: GPL v2
 * Text Domain: menu-item-custom-fields-example
 */
 
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Class Custom Fields Menu Options
 
*//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
 
class hexwp_Item_Custom_Fields_Options {

	protected static $fields = array();
	public static function init() {
 		add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, '_fields' ), 10, 4 );
		add_action( 'wp_update_nav_menu_item', array( __CLASS__, '_save' ), 10, 3 );
		add_filter( 'manage_nav-menus_columns', array( __CLASS__, '_columns' ), 99 );
 		 
	}
	// Save
	public static function _save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}
		$slug='hexwp';
		
		include hexwp_PATH . '/admin/menu/custom-fields/blog_grid-save.php'; 
		include hexwp_PATH . '/admin/menu/custom-fields/widget-save.php'; 
  		include hexwp_PATH . '/admin/menu/custom-fields/image_text-save.php';  
		include hexwp_PATH . '/admin/menu/custom-fields/image-save.php'; 
		include hexwp_PATH . '/admin/menu/custom-fields/page_builder-save.php'; 
 		include hexwp_PATH . '/admin/menu/custom-fields/section-save.php'; 
 		include hexwp_PATH . '/admin/menu/custom-fields/menu_item-save.php'; 
 
  	}
 	//Field
	public static function _fields( $id, $item, $depth, $args ) {
		$slug='hexwp';

		  if($item->type=='blog_grid'){	
		 	include hexwp_PATH . '/admin/menu/custom-fields/blog_grid-options.php'; 
			
		  }elseif($item->type=='widget'){	
		 	include hexwp_PATH . '/admin/menu/custom-fields/widget-options.php'; 
			
		  }elseif($item->type=='image_text'){	
		 	include hexwp_PATH. '/admin/menu/custom-fields/image_text-options.php'; 
		 }else if($item->type=='image'){	
		 	include hexwp_PATH. '/admin/menu/custom-fields/image-options.php'; 
			
		 }elseif($item->type=='page_builder'){	
		 
		 	include hexwp_PATH . '/admin/menu/custom-fields/page_builder-options.php'; 
		 }elseif($item->type=='section'){	
		 
		 	include hexwp_PATH . '/admin/menu/custom-fields/section-options.php'; 
		 }else{ 
			include  hexwp_PATH . '/admin/menu/custom-fields/menu_item-options.php';   
		 }
						 
						 
	} 
	public static function _columns( $columns ) {
		$columns = array_merge( $columns, self::$fields );

		return $columns;
	}
}
hexwp_Item_Custom_Fields_Options::init();






 
/**
 * Displays a menu metabox 
 *
 * @param string $object Not used.
 * @param array $args Parameters and arguments. If you passed custom params to add_meta_box(), 
 * they will be in $args['args']
 */
/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Render Menu Metabox
 
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
function hexwp_render_menu_metabox( $object, $args ) {
	global $nav_menu_selected_id;
	// Create an array of objects that imitate Post objects
	$my_items = array(
		 
		(object) array(
			'ID' => 1,
			'db_id' => 0,
			'menu_item_parent' => 0,
			'object_id' => 3,
			'post_parent' => 0,
			'type' => 'section',
			'object' => 'section',
			'type_label' => __('Section','hexwp'),
			'type' => 'section',
			'title' => __('Section','hexwp'),
			'url' => home_url( '/section/' ),
			'hexwp_menu_padding_top' => '20',
			'target' => '',
			'attr_title' => '',
 			'description' => '',
			'classes' => array(),
			'xfn' => '',
		),
		
	 
		(object) array(
			'ID' => 1,
			'db_id' => 0,
			'menu_item_parent' => 0,
			'object_id' => 3,
			'post_parent' => 0,
			'type' => 'widget',
			'object' => 'widget',
			'type_label' => __('Widget','hexwp'),
			'title' => __('Widget','hexwp'),
			'url' => home_url( '#' ),
			'target' => '',
			'attr_title' => '',
			'description' => '',
			'classes' => array(),
			'xfn' => '',
		),	
		
		(object) array(
			'ID' => 1,
			'db_id' => 0,
			'menu_item_parent' => 0,
			'object_id' => 3,
			'post_parent' => 0,
			'type' => 'page_builder',
			'object' => 'page_builder',
			'type_label' => __('Page Builder','hexwp'),
			'title' => __('Page Builder','hexwp'),
			'url' => home_url( '#' ),
			'target' => '',
			'attr_title' => '',
			'description' => '',
			'classes' => array(),
			'xfn' => '',
		),	
		

		(object) array(
			'ID' => 1,
			'db_id' => 0,
			'menu_item_parent' => 0,
			'object_id' => 3,
			'post_parent' => 0,
			'type' => 'image',
			'object' => 'image',
			'type_label' =>__('Image','hexwp'),
			'title' => __('Image','hexwp'),
 			'url' => '',
 			'target' => '',
			'attr_title' => '',
			'description' => '',
			'classes' => array(),
			'xfn' => '',
		),	
		

		(object) array(
			'ID' => 1,
			'db_id' => 0,
			'menu_item_parent' => 0,
			'object_id' => 3,
			'post_parent' => 0,
			'type' => 'image_text',
			'object' => 'image_text',
			'type_label' => __('Image & Text','hexwp'),
			'title' => __('Image & Text','hexwp'),
 			'url' => '',
 			'target' => '',
			'attr_title' => '',
			'description' => '',
			'classes' => array(),
			'xfn' => '',
		),	
		(object) array(
			'ID' => 1,
			'db_id' => 0,
			'menu_item_parent' => 0,
			'object_id' => 3,
			'post_parent' => 0,
			'type' => 'blog_grid',
			'object' => 'blog_grid',
			'type_label' => __('Blog Grid','hexwp'),
			'title' => __('Blog Grid','hexwp'),
 			'url' => '',
 			'target' => '',
			'attr_title' => '',
			'description' => '',
			'classes' => array(),
			'xfn' => '',
		),			
				
		
	);
	$db_fields = false;
	if ( false ) {
		$db_fields = array( 'parent' => 'parent', 'id' => 'post_parent' );
	}
	$walker = new Walker_Nav_Menu_Checklist( $db_fields );
	$removed_args = array(
		'action',
		'customlink-tab',
		'edit-menu-item',
		'menu-item',
		'page-tab',
		'_wpnonce',
	); ?>
	<div id="my-plugin-div">
		<div id="tabs-panel-my-plugin-all" class="tabs-panel tabs-panel-active">
		<ul id="my-plugin-checklist-pop" class="categorychecklist form-no-clear" >
			<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $my_items ), 0, (object) array( 'walker' => $walker ) ); ?>
		</ul>

		<p class="button-controls">
			<span class="list-controls">
				<a href="<?php
					echo esc_url(add_query_arg(
						array(
							'my-plugin-all' => 'all',
							'selectall' => 1,
						),
						remove_query_arg( $removed_args )
					));
				?>#my-menu-test-metabox" class="select-all"><?php _e( 'Select All','hexwp'); ?></a>
			</span>

			<span class="add-to-menu">
				<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu','hexwp' ); ?>" name="add-my-plugin-menu-item" id="submit-my-plugin-div" />
				<span class="spinner"></span>
			</span>
		</p>
	</div>
	<?php
}

/*****************************************************************************************************************************************************
******************************************************************************************************************************************************
 
																	Register Menu Metabox
 
*////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 
 function hexwp_register_menu_metabox() {
	$custom_param = array( 0 => 'This param will be passed to filson_render_menu_metabox' );
	
	add_meta_box( 'my-menu-test-metabox', esc_html__('Mega Menu','hexwp'), 'hexwp_render_menu_metabox', 'nav-menus', 'side', 'default', $custom_param );
}
add_action( 'admin_head-nav-menus.php', 'hexwp_register_menu_metabox' );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
