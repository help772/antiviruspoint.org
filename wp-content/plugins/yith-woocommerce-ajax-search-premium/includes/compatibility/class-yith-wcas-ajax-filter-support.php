<?php
/**
 * YITH_WCAS_Ajax_Filter_Support class
 *
 * @since      2.0.0
 * @author     YITH
 * @package    YITH/Search
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCAS_Ajax_Filter_Support' ) ) {
	/**
	 * YITH WooCommerce Ajax Filter support class
	 *
	 * @since 2.0.0
	 */
	class YITH_WCAS_Ajax_Filter_Support {
		use YITH_WCAS_Trait_Singleton;


		/**
		 * Constructor
		 *
		 * @since 2.0.0
		 */
		protected function __construct() {
			
			add_filter( 'ywcas_should_filter_query', array( $this, 'filter_query' ), 10, 3 );
			add_filter( 'yith_wcan_query_vars_for_cache_index', array( $this, 'query_vars_for_cache_index' ) );
		}

		/**
		 * Filter pre_get_post query
		 *
		 * @param   boolean           $should_filter  Should filter query.
		 * @param   string            $query          Query object.
		 * @param   YITH_WCAS_Search  $search         Search object.
		 *
		 * @return bool
		 */
		public function filter_query( $should_filter, $query, $search ) {
			return
				( ! empty( $_GET['s'] ) || isset( $_GET['ywcas_filter'] ) ) &&
				! is_admin() &&
				! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) &&
				( $query->is_main_query() || $query->get( 'yith_wcan_prefetch_cache' ) );

		}

		/**
         * Filter query vars for cache index.
         *
		 * @param array $query_vars Query vars.
		 *
		 * @return array
		 */
		public function query_vars_for_cache_index( $query_vars ) {
			if ( isset( $_GET['ywcas_filter'] ) ) {
				$query_vars['ywcas_filter'] = sanitize_key( wp_unslash( $_GET['ywcas_filter'] ) );
			}
			return $query_vars;
		}

	}

}
