<?php // phpcs:ignore WordPress.Files.FileName

use AdvancedAds\Abstracts\Ad;
use AdvancedAds\Interfaces\Ad_Interface;
use AdvancedAds\Framework\Utilities\Params;
use AdvancedAds\Utilities\Conditional;

/**
 * Google Ad Manager Ad Type
 */
class Advanced_Ads_Gam_Ad extends Ad implements Ad_Interface {

	/**
	 * Responsive form class
	 *
	 * @var string
	 */
	public $responsive_form = 'Advanced_Ads_Gam_Admin_Responsive_Form';

	/**
	 * Get ad sizes from the ad object
	 *
	 * @param Ad $ad Ad instance.
	 *
	 * @return array
	 */
	public static function get_ad_unit_sizes( $ad ) {
		if ( empty( $ad->get_content() ) ) {
			return [];
		}

		return self::get_ad_unit_sizes_from_ad_content( Advanced_Ads_Network_Gam::post_content_to_adunit( $ad->get_content() ) );
	}

	/**
	 * Generate a json string for the ad sizes selected for this ad so that we can load it using JS in the frontend.
	 *
	 * @param Ad $ad Ad instance.
	 *
	 * @return string
	 */
	public static function get_ad_sizes_json_string( $ad ) {

		$sizes = $ad->get_prop( 'ad-sizes' );
		if ( ! $sizes ) {
			return '{}';
		}

		return wp_json_encode( $sizes );
	}

	/**
	 * Get ad sizes from the ad unit data ($ad->content)
	 *
	 * @param array $ad_unit ad unit content.
	 * @return array
	 */
	public static function get_ad_unit_sizes_from_ad_content( $ad_unit ) {
		if ( empty( $ad_unit ) ) {
			return [];
		}

		$ad_unit = self::append_fluid_to_sizes( $ad_unit );

		/**
		 * Ads with just one size have a simple array in `adUnitSizes` while those with multiple sizes store them in a multidimensional array
		 * we convert the version with just one size to a multidimensional array to later be able to handle them equally
		 */
		$ad_unit_sizes = [];
		if ( ! empty( $ad_unit['adUnitSizes'] ) && is_array( $ad_unit['adUnitSizes'] ) ) {
			if ( isset( $ad_unit['adUnitSizes']['size'] ) ) {
				$ad_unit_sizes = [ $ad_unit['adUnitSizes'] ];
			} else {
				$ad_unit_sizes = $ad_unit['adUnitSizes'];
			}
		}

		// we are using the `fullDisplayString` value as an index to be able to identify the sizes.
		$ad_unit_with_keys = [];
		foreach ( $ad_unit_sizes as $_ad_unit ) {
			if ( isset( $_ad_unit['fullDisplayString'] ) ) {
				$ad_unit_with_keys[ $_ad_unit['fullDisplayString'] ] = $_ad_unit;
			}
		}

		return $ad_unit_with_keys;
	}

	/**
	 * Build ad path using the parent Path
	 *
	 * @param array $ad_unit The ad unit data.
	 * @return string the path string.
	 */
	public function build_slot_path( $ad_unit ) {
		$path = '/' . $ad_unit['networkCode'] . '/';

		if ( ! isset( $ad_unit['parentPath']['adUnitCode'] ) ) {
			// another parent except the effective root ad unit.
			foreach ( $ad_unit['parentPath'] as $parent ) {
				if ( $parent['id'] === $ad_unit['effectiveRootAdUnitId'] ) {
					continue;
				}
				$path .= $parent['adUnitCode'] . '/';
			}
		}

		$path .= $ad_unit['adUnitCode'];
		return $path;
	}

	/**
	 * Append 'fluid' size into 'adUnitSizes' field in a way that resemble to Google's data. Create the field if needed
	 *
	 * @param [array] $ad_unit the original ad unit array.
	 * @return [array] the modified unit
	 */
	public static function append_fluid_to_sizes( $ad_unit ) {
		if ( ! isset( $ad_unit['isFluid'] ) || ! $ad_unit['isFluid'] || isset( $ad_unit['hasFluidSize'] ) ) {
			// Not a fluid ad, or fluid field already added.
			return $ad_unit;
		}

		$ad_unit['hasFluidSize'] = true;

		if ( isset( $ad_unit['adUnitSizes'], $ad_unit['adUnitSizes']['fullDisplayString'] ) ) {
			// Single size, reformat to the multiple size style then append the fluid size.
			$single_size              = $ad_unit['adUnitSizes'];
			$ad_unit['adUnitSizes']   = [];
			$ad_unit['adUnitSizes'][] = $single_size;
			$ad_unit['adUnitSizes'][] = [
				'size'              => [],
				'fullDisplayString' => 'fluid',
			];

			return $ad_unit;
		}

		if ( isset( $ad_unit['adUnitSizes'] ) ) {
			// Multiple fixed size, just append "fluid".
			$ad_unit['adUnitSizes'][] = [
				'size'              => [],
				'fullDisplayString' => 'fluid',
			];

			return $ad_unit;
		}

		// No fixed size, just fluid.
		$ad_unit['adUnitSizes'] = [
			'size'              => [],
			'fullDisplayString' => 'fluid',
		];

		return $ad_unit;
	}

	/**
	 * Build size string parameter for an ad unit
	 *
	 * @param array $ad_unit   The ad unit data.
	 * @param array $ad_sizes The ad options.
	 *
	 * @return string the size string.
	 */
	public function build_size_string( $ad_unit, $ad_sizes = [] ) {
		$ad_unit    = self::append_fluid_to_sizes( $ad_unit );
		$size_array = [];

		if ( isset( $ad_unit['adUnitSizes'] ) ) {
			// At least one size in the ad data.
			if ( isset( $ad_unit['adUnitSizes']['size'] ) ) {
				// Ad unit with more than one size. Reformat it to match the structure of multiple ad unit sizes.
				$ad_unit['adUnitSizes'] = [ $ad_unit['adUnitSizes'] ];
			}

			foreach ( $ad_unit['adUnitSizes'] as $size ) {
				$size_array[] = ! empty( $size['size'] ) ? $size['size']['width'] . ',' . $size['size']['height'] : "'fluid'";
			}
		}

		// Add sizes added manually.
		if ( is_array( $ad_sizes ) ) {
			foreach ( $ad_sizes as $screen_width ) {
				if ( isset( $screen_width['sizes'] ) ) {
					foreach ( $screen_width['sizes'] as $size ) {
						$size_string = str_replace( [ 'x', 'fluid' ], [ ',', "'fluid'" ], $size );
						if ( ! in_array( $size_string, $size_array, true ) ) {
							$size_array[] = $size_string;
						}
					}
				}
			}
		}

		if ( empty( $size_array ) ) {
			// No ad size, return empty JavaScript array string.
			return '[]';
		}

		sort( $size_array );
		// Put 'fluid' at the end of the array if it is present.
		$fluid_index = array_search( "'fluid'", $size_array, true );
		if ( false !== $fluid_index ) {
			array_splice( $size_array, $fluid_index, 1 );
			$size_array[] = "'fluid'";
		}

		return count( $size_array ) > 1 ? '[[' . implode( '],[', $size_array ) . ']]' : '[' . $size_array[0] . ']';
	}

	/**
	 * Build a string that returns an array with the ad unit sizes that match the screen width
	 * if the appropriate option is enabled in the ad settings
	 *
	 * If the Ad sizes option that makes the ad fully responsive is enabled, our size string is
	 * a dynamic function filtering the available sizes by available width
	 *
	 * @param string $ad_size_string JS array with the ad sizes.
	 * @param Ad     $ad             Ad instance.
	 * @param string $container_id   ID of the container in the frontend.
	 *
	 * @return string the string that filters the size or the original string if the option was not selected
	 */
	public function maybe_build_responsive_size_string( $ad_size_string, $ad, $container_id ) {

		// Do not use it on AMP pages.
		if ( Conditional::is_amp() ) {
			return $ad_size_string;
		}

		if ( ! $ad->get_prop( 'responsive-sizes' ) || '[]' === $ad_size_string ) {
			return $ad_size_string;
		}

		/**
		 * Manipulate the string to become an array, if there is just one element
		 * e.g., [300,250] => [[300,250]]
		 * we are just checking if the string has only a single comma
		 */
		if ( 1 === substr_count( $ad_size_string, ',' ) ) {
			$ad_size_string = "[$ad_size_string]";
		}

		return $ad_size_string . ".filter( el => el[0] <= document.querySelector( '#$container_id').clientWidth || 'fluid' == el )";
	}

	/**
	 * Build sizeMapping object.
	 *
	 * @link https://developers.google.com/doubleclick-gpt/guides/ad-sizes#responsive_ads
	 *
	 * @param Ad     $ad           Ad instance.
	 * @param string $container_id ID of the container in the frontend.
	 *
	 * @return string the sizeMapping array. returns an empty string if not set
	 */
	public function build_size_mapping_object( $ad, $container_id ) {
		$ad_sizes = $ad->get_prop( 'ad-sizes' );

		/**
		 * Load the sizes set up with the ad in the GAM account.
		 */
		$ad_unit_sizes = self::get_ad_unit_sizes( $ad );

		if ( ! $ad_sizes ) {
			return '';
		}

		// Add manual sizes.
		foreach ( $ad_sizes as $ad_size ) {
			if ( isset( $ad_size['sizes'] ) ) {
				foreach ( $ad_size['sizes'] as $single_ad_size ) {
					if ( ! array_key_exists( $single_ad_size, $ad_unit_sizes ) ) {
						if ( ! is_array( $ad_unit_sizes ) ) {
							$ad_unit_sizes = [];
						}
						if ( 'fluid' === $single_ad_size ) {
							$ad_unit_sizes['fluid'] = [
								'size'              => [],
								'fullDisplayString' => 'fluid',
							];
						} else {
							$exploded_size                    = explode( 'x', $single_ad_size );
							$ad_unit_sizes[ $single_ad_size ] = [
								'size'              => [
									'width'         => $exploded_size[0],
									'height'        => $exploded_size[1],
									'isAspectRatio' => 'false',
								],
								'environmentType'   => 'BROWSER',
								'fullDisplayString' => $single_ad_size,
							];
						}
					}
				}
			}
		}

		if ( ! $ad_unit_sizes ) {
			return '';
		}

		/**
		 * Sanitize Ad sizes option on output
		 * remove the options when none or all of the possible options are selected
		 * this prevents the output in the code, which is not needed in this case
		 */
		if ( is_array( $ad_sizes ) && count( $ad_sizes ) && $ad_unit_sizes ) {
			$rows           = count( $ad_sizes );
			$max_checkboxes = $rows * count( $ad_unit_sizes );

			// collect all selected options.
			$selected_checkboxes = 0;
			foreach ( $ad_sizes as $_option ) {
				if ( isset( $_option['sizes'] ) && is_array( $_option['sizes'] ) ) {
					$selected_checkboxes += count( $_option['sizes'] );
				}
			}

			if ( ! $selected_checkboxes || $max_checkboxes === $selected_checkboxes ) {
				return '';
			}
		}

		/**
		 * We are directly iterating through the sizeMapping options (ad sizes) from the backend.
		 * (may include sizes added manually that are not in the original GAM ad data).
		 */
		$size_strings = [];
		// order options starting with highest minimum width.
		krsort( $ad_sizes );

		foreach ( $ad_sizes as $_min_width => $_saved_sizes ) {
			$ad_unit_sizes_for_output = [];
			// if the sizes option is missing then it might mean that the ad code should stay empty.
			if ( ! isset( $_saved_sizes['sizes'] ) ) {
				$ad_unit_sizes_for_output[] = '[]';
			} else {
				foreach ( $_saved_sizes['sizes'] as $size ) {
					if ( 'fluid' === $size ) {
						$ad_unit_sizes_for_output[] = "'fluid'";
					} elseif ( false !== strpos( $size, 'x' ) ) {
						$ad_unit_sizes_for_output[] = '[' . str_ireplace( 'x', ', ', $size ) . ']';
					}
				}
			}
			if ( count( $ad_unit_sizes_for_output ) ) {
				$min_width            = absint( $_min_width );
				$ad_unit_sizes_string = 1 === count( $ad_unit_sizes_for_output ) ? $ad_unit_sizes_for_output[0] : '[' . implode( ', ', $ad_unit_sizes_for_output ) . ']';
				$ad_unit_sizes_string = $this->maybe_build_responsive_size_string( $ad_unit_sizes_string, $ad, $container_id );
				$size_strings[]       = "addSize([{$min_width}, 0], {$ad_unit_sizes_string}).";
			}
		}

		// build the output string if the array is not empty.
		if ( empty( $size_strings ) ) {
			return '';
		}

		return 'var mapping = googletag.sizeMapping().'
			. "\n"
			. implode( "\n", $size_strings )
			. "\n"
			. 'build();'
			. "\n";
	}

	/**
	 * Return the key value targeting output (if any)
	 *
	 * @param Ad $ad Ad instance.
	 *
	 * @return [string] the front end output (JS code).
	 */
	private function get_key_values_output( $ad ) {
		$js = '';

		if ( is_array( $ad->get_prop( 'gam-keyval' ) ) ) {

			$frontend_vars = self::get_front_end_variables();

			// phpcs:disable WordPress.Security.NonceVerification.Missing
			$gam = Params::post( 'gam' );
			if ( wp_doing_ajax() && $gam ) {
				$frontend_vars = wp_unslash( $gam ); // phpcs:ignore
				array_walk(
					$frontend_vars['conditionals'],
					function ( &$condition ) {
						$condition = 'true' === $condition || true === $condition;
					}
				);
			}
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			$custom_key = [];
			$postmeta   = [];
			$usermeta   = [];

			foreach ( $ad->get_prop( 'gam-keyval' ) as $kv ) {
				switch ( $kv['type'] ) {
					case 'categories':
						if ( $frontend_vars['conditionals']['is_single'] || $frontend_vars['conditionals']['is_category'] ) {
							if ( $frontend_vars['conditionals']['is_category'] ) {
								$js .= ".setTargeting( '" . $kv['key'] . "', '" . $frontend_vars['vars']['categories'] . "' )";
								break;
							}
							$js .= $this->catgories_key_values( $kv['key'], $frontend_vars['vars']['categories'] );
						}
						break;
					case 'post_types':
						if ( isset( $frontend_vars['vars']['post_type'] ) ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $frontend_vars['vars']['post_type'] . "' )";
						}
						break;
					case 'page_slug':
						if ( isset( $frontend_vars['vars']['page_slug'] ) ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $frontend_vars['vars']['page_slug'] . "' )";
						}
						break;
					case 'page_type':
						$front = get_option( 'show_on_front' );
						if ( 'posts' === $front && $frontend_vars['conditionals']['is_front_page'] ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'home' )";
							break;
						}
						if ( $frontend_vars['conditionals']['is_home'] ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'blog' )";
							break;
						}
						if ( $frontend_vars['conditionals']['is_archive'] ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'archive' )";
							break;
						}
						if ( $frontend_vars['conditionals']['is_singular'] ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', 'single' )";
						}
						break;
					case 'placement_id':
						if ( $ad->get_root_placement() ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $ad->get_root_placement()->get_slug() . "' )";
						}
						break;
					case 'page_id':
						if ( $frontend_vars['conditionals']['is_singular'] && $frontend_vars['vars']['post_id'] ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $frontend_vars['vars']['post_id'] . "' )";
						}
						break;
					case 'usermeta':
						if ( ! empty( $frontend_vars['vars']['user_id'] ) ) {
							$usermeta = $this->stack_meta_data( $kv['key'], get_user_meta( $frontend_vars['vars']['user_id'], $kv['value'], false ), $usermeta );
						}
						break;
					case 'postmeta':
						$post_id = $frontend_vars['vars']['post_id'];
						if ( empty( $post_id ) ) {
							break;
						}
						$postmeta = $this->stack_meta_data( $kv['key'], get_post_meta( $post_id, $kv['value'], false ), $postmeta );
						break;
					case 'taxonomy':
						if ( $frontend_vars['conditionals']['is_archive'] && isset( $frontend_vars['vars']['taxonomy'] ) ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $frontend_vars['vars']['taxonomy'] . "' )";
						}
						break;
					case 'term':
						if ( $frontend_vars['is_home'] ) {
							break;
						}

						if ( $frontend_vars['is_archive'] && isset( $frontend_vars['vars']['term'] ) ) {
							$js .= ".setTargeting( '" . $kv['key'] . "', '" . $frontend_vars['vars']['term'] . "' )";
						}

						if ( $frontend_vars['is_single'] && 'post' === $frontend_vars['vars']['post_type'] ) {
							$js .= $this->catgories_key_values( $kv['key'], $frontend_vars['vars']['categories'] );
						}
						break;
					case 'terms':
						if ( $frontend_vars['conditionals']['is_home'] ) {
							break;
						}

						if ( $frontend_vars['conditionals']['is_archive'] && $kv['onarchives'] ) {
							$query_obj = get_queried_object();
							if ( ! empty( $query_obj ) && isset( $query_obj->term_taxonomy_id, $query_obj->slug ) ) {
								$js .= ".setTargeting( '" . $kv['key'] . "', '" . $query_obj->slug . "' )";
							}
							break;
						}

						if ( $frontend_vars['conditionals']['is_single'] ) {
							$js .= $this->catgories_key_values( $kv['key'], $frontend_vars['vars']['terms'] );
						}
						break;
					case 'custom':
						if ( ! isset( $custom_key[ $kv['key'] ] ) ) {
							$custom_key[ $kv['key'] ] = [];
						}
						$custom_key[ $kv['key'] ][] = $kv['value'];
						break;
					default:
				}
			}

			foreach ( $custom_key as $key => $value ) {
				if ( 1 < count( $value ) ) {
					$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $value ) . '"] )';
				} else {
					$js .= ".setTargeting( '" . $key . "', \"" . $value[0] . '" )';
				}
			}

			$js = $this->append_meta_data( $js, $postmeta );
			$js = $this->append_meta_data( $js, $usermeta );
		}

		return $js;
	}

	/**
	 * Append user|post meta data to key-values string
	 *
	 * @param string $js    key-values output.
	 * @param array  $stack metadata to read from.
	 *
	 * @return string
	 */
	private function append_meta_data( $js, $stack ) {
		foreach ( $stack as $key => $meta_value ) {
			if ( 1 < count( $meta_value ) ) {
				$meta_values = [];
				foreach ( $meta_value as $value ) {
					if ( is_array( $value ) ) {
						$meta_values += $value;
					} else {
						$meta_values[] = $value;
					}
				}
				$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $meta_values ) . '"] )';
			} elseif ( is_array( $meta_value[0] ) ) {
				$js .= ".setTargeting( '" . $key . "', [\"" . implode( '", "', $meta_value[0] ) . '"] )';
			} else {
				$js .= ".setTargeting( '" . $key . "', \"" . $meta_value[0] . '" )';
			}
		}

		return $js;
	}

	/**
	 * Stack key/user|post meta data pairs
	 *
	 * @param string $key   the key to use.
	 * @param array  $meta  meta data from DB.
	 * @param array  $stack array to store the key/meta pair.
	 *
	 * @return array
	 */
	private function stack_meta_data( $key, $meta, $stack ) {
		if ( ! is_array( $meta ) || empty( $meta ) ) {
			return $stack;
		}

		if ( ! isset( $stack[ $key ] ) ) {
			$stack[ $key ] = [];
		}

		$stack[ $key ][] = count( $meta ) === 1 ? $meta[0] : $meta;

		return $stack;
	}

	/**
	 * Get categories/taxonomies key values string
	 *
	 * @param string $key        the key to use.
	 * @param array  $categories list of categories.
	 *
	 * @return string
	 */
	private function catgories_key_values( $key, $categories ) {
		if ( empty( $categories ) ) {
			return '';
		}

		return count( $categories ) === 1
			? ".setTargeting( '" . $key . "', '" . $categories[0] . "' )"
			: ".setTargeting( '" . $key . "', ['" . implode( "', '", $categories ) . "'] )";
	}

	/**
	 * Collect front end variable for AJAX cache busting
	 *
	 * @return array
	 */
	public static function get_front_end_variables() {
		$variables = [
			'conditionals' => [
				'is_archive'           => is_archive(),
				'is_category'          => is_category(),
				'is_front_page'        => is_front_page(),
				'is_home'              => is_home(),
				'is_post_type_archive' => is_post_type_archive(),
				'is_single'            => is_single(),
				'is_singular'          => is_singular(),
			],
			'vars'         => [
				'query_obj' => [],
				'user_id'   => get_current_user_id(),
				'post_id'   => get_the_ID(),
			],
		];

		$query_obj = get_queried_object();

		if ( isset( $query_obj->slug ) ) {
			$variables['vars']['query_obj']['slug'] = $query_obj->slug;
		}

		if ( is_category() && isset( $query_obj->slug ) ) {
			$variables['vars']['categories'] = [ $query_obj->slug ];
			$variables['vars']['page_slug']  = [ $query_obj->slug ];
		}

		if ( is_single() ) {
			$categories_obj = get_the_category( get_the_ID() );

			if ( ! empty( $categories_obj ) ) {
				$categories = [];
				foreach ( $categories_obj as $category ) {
					$categories[] = $category->slug;
				}
				$variables['vars']['categories'] = $categories;
				$variables['vars']['term']       = $categories;
			}

			$post       = get_post();
			$taxonomies = get_object_taxonomies( $post, 'names' );
			$terms      = [];

			foreach ( $taxonomies as $tax ) {
				$terms_obj = get_the_terms( $post, $tax );

				if ( $terms_obj ) {
					foreach ( $terms_obj as $term ) {
						$terms[] = $term->slug;
					}
				}
			}

			$variables['vars']['terms'] = $terms;
		}

		if ( is_archive() ) {
			$variables['vars']['post_type'] = get_post_type( get_the_ID() );

			if ( isset( $query_obj->term_id ) ) {
				$term = get_term( $query_obj->term_id );

				if ( ! empty( $term ) ) {
					$variables['vars']['page_slug'] = is_array( $term ) ? $term[0] : $term->slug;
				}
			}

			if ( isset( $query_obj->taxonomy ) ) {
				$variables['vars']['taxonomy'] = $query_obj->taxonomy;
			}

			if ( isset( $query_obj->term_taxonomy_id, $query_obj->slug ) ) {
				$variables['vars']['term']  = $query_obj->slug;
				$variables['vars']['terms'] = $query_obj->slug;
			}
		}

		if ( is_singular() || is_post_type_archive() ) {
			$variables['vars']['post_type'] = get_post_type( get_the_ID() );
		}

		if ( is_singular() ) {
			$post                           = get_post();
			$variables['vars']['page_slug'] = $post->post_name;
			$variables['vars']['page_id']   = $post->ID;
		}

		return $variables;
	}

	/**
	 * Prepare output for frontend.
	 *
	 * @return string
	 */
	public function prepare_frontend_output(): string {
		$content = $this->get_content();
		if ( empty( $content ) ) {
			return '';
		}

		$ad_unit = Advanced_Ads_Network_Gam::post_content_to_adunit( $content );

		if ( ! isset( $ad_unit['networkCode'] ) || ! isset( $ad_unit['adUnitCode'] ) ) {
			return '';
		}

		// we are mimicking the container IDs that GAM builds since they are not retrievable through the API.
		$p1 = wp_rand( intval( pow( 10, 5 ) ), intval( pow( 10, 6 ) ) - 1 );
		$p2 = wp_rand( intval( pow( 10, 6 ) ), intval( pow( 10, 7 ) ) - 1 );

		// GAM seems to add `-0` to all container IDs so let’s do this as well.
		$div_id = 'gpt-ad-' . $p1 . $p2 . '-0';

		// Load general GAM plugin settings.
		$setting = Advanced_Ads_Network_Gam::get_setting();
		$path    = $this->build_slot_path( $ad_unit );
		$size    = $this->build_size_string( $ad_unit, $this->get_prop( 'ad-sizes' ) );
		$size    = $this->maybe_build_responsive_size_string( $size, $this, $div_id );

		// Output for sizeMapping (responsive ad units).
		$size_mapping_object = $this->build_size_mapping_object( $this, $div_id );
		$size_mapping        = ( $size_mapping_object ) ? '.defineSizeMapping(mapping)' : '';

		// Output for the collapse option.
		$empty_div = '';
		if ( 'collapse' === $setting['empty-div'] ) {
			$empty_div = '.setCollapseEmptyDiv(true)';
		} elseif ( 'fill' === $setting['empty-div'] ) {
			$empty_div = '.setCollapseEmptyDiv(true,true)';
		}

		$key_values = $this->get_key_values_output( $this );
		$refresh    = (int) $this->get_prop( 'gam-refresh' ) ?? 0;
		ob_start();

		if ( Conditional::is_amp() ) {
			$size = '[]';
			if ( $this->get_prop( 'amp-ad-sizes' ) ) {
				$size_array = [];
				foreach ( $this->get_prop( 'amp-ad-sizes' ) as $ad_size ) {
					$size_array [] = 'fluid' === $ad_size ? "'fluid'" : '[' . str_replace( 'x', ',', $ad_size ) . ']';
				}
				$size = '[' . implode( ',', $size_array ) . ']';
			} elseif ( ! $this->get_prop( 'amp-has-sizes' ) ) {
				$size = $this->build_size_string( $ad_unit, $ad->get_prop( 'ad-sizes' ) );
			}
			require AA_GAM_ABSPATH . 'includes/amp-output.php';
		} else {
			require AA_GAM_ABSPATH . 'includes/ad-output.php';
		}

		return ob_get_clean();
	}

	/**
	 * Sanitize ad options on save
	 * - use value for "width" as index
	 * - order lines by index/width starting with the lowest
	 * - prevent saving a completely empty row if there is only one
	 *
	 * @param array $options ad options.
	 * @return array sanitized ad options.
	 */
	public function sanitize_options( $options = [] ) {
		// Remove the option when there is just one line and no size was selected so that it is recreated with all boxes selected.
		if (
			isset( $options['output']['ad-sizes'] ) &&
			is_array( $options['output']['ad-sizes'] ) &&
			1 === count( $options['output']['ad-sizes'] )
		) {
			// get first array since we don’t have a static index.
			$first = reset( $options['output']['ad-sizes'] );

			if ( ! isset( $first['sizes'] ) ) {
				unset( $options['output']['ad-sizes'] );
			}
		}

		/**
		 * Sanitize Ad sizes
		 * - use the "width" input field as an index
		 * - order lines
		 * - removes duplicates by design
		 */
		if ( isset( $options['output']['ad-sizes'] ) && is_array( $options['output']['ad-sizes'] ) ) {
			$sanitized_codes = [];

			foreach ( $options['output']['ad-sizes'] as $_index => $_codes ) {
				// use value for "width" as the index.
				$new_index                     = isset( $_codes['width'] ) ? absint( $_codes['width'] ) : absint( $_index );
				$sanitized_codes[ $new_index ] = $_codes;
			}

			// order lines by index/width starting with the lowest.
			ksort( $sanitized_codes );

			$options['output']['ad-sizes'] = $sanitized_codes;
		}

		return $options;
	}
}
