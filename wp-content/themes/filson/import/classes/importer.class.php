<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
/**
 *
 * Framework Class
 *
 * @since 1.0.0
 * @version 10001.0.0
 *
 */
  if ( !class_exists( 'hexwpdemoimport_Importer' ) ) {
class hexwpdemoimport_Importer extends hexwpdemoimport_Importer_Abstract {
  /**
   *
   * option database/data name
   * @access public
   * @var string
   *
   */
  public $opt_id = '_dt_importer';
  /**
   *
   * framework option database/data name
   * @access public
   * @var string
   *
   */

   /**
   *
   * demo items
   * @access public
   * @var array
   *
   */
    public $settings;
  public $items = array();
  /**
   *
   * instance
   * @access private
   * @var class
   *
   */
  private static $instance = null;
  // run framework construct
  public function __construct( $settings, $items ) {
    $this->settings = apply_filters( 'hexwpdemoimport_importer_settings', $settings );
    $this->items    = apply_filters( 'hexwpdemoimport_importer_items', $items );
    if( ! empty( $this->items ) ) {
      $this->addAction( 'admin_menu', 'admin_menu' );
      $this->addAction( 'wp_ajax_hexwpdemoimport_Importer', 'import_process' );
    }
  }
  // instance
  public static function instance( $settings = array(), $items = array() ) {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self( $settings, $items );
    }
    return self::$instance;
  }

  // adding option page
  public function admin_menu() {
    $defaults_menu_args = array(
      'menu_parent'     => '',
      'menu_title'      => '',
      'menu_type'       => '',
      'menu_slug'       => '',
      'menu_icon'       => '',
      'menu_capability' => 'manage_options',
      'menu_position'   => null,
    );
    $args = wp_parse_args( $this->settings, $defaults_menu_args );
    if( $args['menu_type'] == 'add_submenu_page' ) {
      call_user_func( $args['menu_type'],'hexwp_theme', __('Import Data Demo', 'hexwp'), __('Import Data Demo', 'hexwp'), $args['menu_capability'], $args['menu_slug'], array( &$this, 'admin_page' ) );
    } else {
      call_user_func( $args['menu_type'], __('Import Data Demo', 'hexwp'), __('Import Data Demo', 'hexwp'), $args['menu_capability'], $args['menu_slug'], array( &$this, 'admin_page' ), $args['menu_icon'], $args['menu_position'] );
    } 
  }
  // output demo items
  public function admin_page() {
    $nonce = wp_create_nonce('hexwpdemoimport_importer');
  ?>
  <div class="wrap dt-importer">
    <h2><?php _e( 'filson Theme Demo Importer', 'hexwp'); ?></h2>
    <div class="dt-demo-browser">
      <?php
        foreach ($this->items as $item => $value ) :
          $opt = get_option($this->opt_id);

          $imported_class = '';
           $status = '';
       
      
       
	  
	  if($item=='upload'){?>
			<div class="dt-demo-item dt-demo-item-upload <?php echo esc_attr($imported_class); ?>" data-dt-importer>
 
              <div class="dt-demo-screenshot">
              	<label for="image-data-name" >name</label>
 				<input name="image-data-name"  id="image-data-name" type="text" value="">
                <br>
              	<label for="image-data-id">id</label>
 				<input name="image-data-id"  id="image-data-id" type="text" value="">
				<br>
               	<label for="image-data-slug">slug</label>
 				<input name="image-data-slug"  id="image-data-slug" type="text" value="">
				<br>



				<label for="image-data-url">url</label>
 				<input name="image-data-url"  id="image-data-url" type="text" value="">
                
                 
               </div>
              <h2 class="dt-demo-name"><?php echo esc_html($value['title']); ?></h2>
                <div class="dt-demo-actions">
                <a class="button button-secondary button-import" href="#" data-import="<?php echo esc_attr($item); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" data-url="" data-name="" data-id="" >
				<?php echo  esc_html__( 'Import', 'hexwp'); ?></a>
               </div>
               
               
               
               <div class="dt-importer-response"><span class="dismiss" title="Dismis this messages.">X</span></div>
       	 </div>
            
        <?php }else if($item=='custom'){?>
			<div class="dt-demo-item dt-demo-item-custom <?php echo esc_attr($imported_class); ?>"  data-dt-importer>
 
              <div class="dt-demo-screenshot">
                 <?php foreach($value['name'] as $option_key=> $option_value){?>
                 <div class="dt-option-item" data-id="<?php echo $option_key?>" data-total="<?php echo esc_attr(count($value['options'][$option_key]));?>">
                <?php echo $option_value;?>
                </div>

                <?php }?>
               </div>
              <h2 class="dt-demo-name"><?php echo esc_html($value['title']); ?></h2>
                <div class="dt-demo-actions">
                <a class="button button-secondary button-import" href="#" data-import="<?php echo esc_attr($item); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>"><?php echo  esc_html__( 'Import', 'hexwp'); ?></a>
               </div>
               
               
               
      
				<div class="dt-importer-response ">
              		 <span class="dismiss" title="Dismis this messages.">X</span>
					<div class="dt-load-warp">
					<div class="dt-load-wait"><?php esc_html_e('Please Wait...','hexwp');?></div>
					<div class="dt-load-complate"><?php esc_html_e('Complate','hexwp');?></div>
					<div class="dt-load-text">0%</div>
					<div class="dt-load">
                    	<div class="dt-loading" style=" width:00%;"></div>
					</div>
					<div class="dt-load-import"></div>
                    </div>
					<div class="dt-response"></div>

               </div> 
               <div class="dt-option-json"><?php echo  json_encode($value['options']);?></div>
               
               
       	 </div>
            
        <?php }else{?>
	  
	  
	   
        <div class="dt-demo-item dt-demo-item-homepage <?php echo esc_attr($imported_class); ?>" data-all-count="<?php echo esc_attr(count($value['options']));?>"  data-dt-importer>
 
          <div class="dt-demo-screenshot">
  
            <?php
            
            ?>
            <img src="<?php echo esc_url( hexwp_DI_DIR . 'demos/image/'.$item.'.jpg' ); ?>" alt="<?php echo esc_attr($value['title']); ?>">
          </div>
          <h2 class="dt-demo-name"><?php echo esc_html($value['title']); ?></h2>
         	<div class="dt-demo-actions">
			<a class="button button-secondary button-import" href="#" data-import="<?php echo esc_attr($item); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>"><?php echo  esc_html__( 'Import', 'hexwp'); ?></a>
           </div>
           
           
           
               
				<div class="dt-importer-response ">
              		 <span class="dismiss" title="Dismis this messages.">X</span>
					<div class="dt-load-warp">
					<div class="dt-load-wait"><?php esc_html_e('Please Wait...','hexwp');?></div>
					<div class="dt-load-complate"><?php esc_html_e('Complate','hexwp');?></div>
                    
					<div class="dt-load-text">00%</div>
					<div class="dt-load">
                   		 <div class="dt-loading" style=" width:00%;"></div>
                     </div>
					<div class="dt-load-import"></div>
					</div>
					<div class="dt-response"></div>

               </div> 
               <div class="dt-option-json"><?php echo  json_encode($value['options']);?></div>
               
               
        </div><!-- /.dt-demo-item -->
      <?php 
		}
		endforeach; ?>
      <div class="clear"></div>
    
    </div><!-- /.dt-demo-browser -->
  </div><!-- /.wrap -->
  <?php
  }
   public function import_process() {
    
	deactivate_plugins( '/wordpress-importer/wordpress-importer.php' );
	 $homepage_import= !empty($_POST['homepage_import'])?$_POST['homepage_import']:''; 
	include_once hexwp_DI_PATH . 'demos/pages_delect.php';

    // Import XML Data
	
    $this->import_xml_data();
 	include_once hexwp_DI_PATH . 'demos/theme_options.php';
	include_once hexwp_DI_PATH . 'demos/menu.php';
	include_once hexwp_DI_PATH . 'demos/pages_for_reading.php';
	include_once hexwp_DI_PATH . 'demos/widgets.php';
	include_once hexwp_DI_PATH . 'demos/header.php';
    
    die();
  }


  /**
   * Import XML data by WordPress Importer
   */
  public function import_xml_data() {

    if ( ! wp_verify_nonce( $_POST['nonce'], 'hexwpdemoimport_importer' ) )
	echo die( 'Authentication Error!!!' );
    $id = $_POST['id']; 
	
	$homepage_import= !empty($_POST['homepage_import'])?$_POST['homepage_import']:''; 
	include_once hexwp_DI_PATH . 'demos/dont_xml.php';

 	include_once hexwp_DI_PATH . 'demos/xml.php';   
	
	if($dont_xml==false){
 
    if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);
      require_once ABSPATH . 'wp-admin/includes/import.php';
      $importer_error = false;
      if ( !class_exists( 'WP_Importer' ) ) {
          $class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
          if ( file_exists( $class_wp_importer ) ){
              require_once($class_wp_importer);
          } else {
              $importer_error = true;
          }
      }
      if ( !class_exists( 'WP_Import' ) ) {
          $class_wp_import = dirname( __FILE__ ) .'/wordpress-importer.php';
          if ( file_exists( $class_wp_import ) )
              require_once($class_wp_import);
          else
              $importer_error = true;
      }
      if($importer_error){
          die(__("Error on import", 'hexwp'));
      } else {
        if(!is_file( $file )){
             esc_html_e("Import done", 'hexwp');
        } else {
          $wp_import = new WP_Import();
          $wp_import->fetch_attachments = true;
          $wp_import->import( $file );
          $options = get_option($this->opt_id);
          $options[$id] = true;
          update_option( $this->opt_id, $options );
      }
    }
	}

  }
 
}
  }
  
  