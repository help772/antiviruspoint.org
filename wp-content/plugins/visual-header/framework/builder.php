<?php
// Prevent direct access
defined('ABSPATH') || exit;

/**
 * AJAX handler for the Visual Header Builder
 */
if ( ! function_exists( 'vh_builder' ) ) {
	add_action('wp_ajax_vh_builder', 'vh_builder');

	function vh_builder($none_ajax = '') {
		// Only allow users with permission
		if (empty($none_ajax)) {
			if (
				!isset($_POST['_wpnonce']) ||
				!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'vh_header_nonce')
			) {
				wp_die(__('Security check failed (nonce verification).', 'visual-header'));
			}

			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have permission to access this action.', 'visual-header'));
			}
		}

		// Sanitize & Validate Inputs
		$id       = !empty($_POST['id']) ?filter_input(INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS):'';
		$json     = !empty($_POST['json']) ? filter_input(INPUT_POST, 'json', FILTER_SANITIZE_FULL_SPECIAL_CHARS):'';
		$library  = !empty($_POST['library'])? filter_input(INPUT_POST, 'library', FILTER_SANITIZE_FULL_SPECIAL_CHARS):'';
		$option   = isset($_POST['option']) ? wp_kses_post(wp_unslash($_POST['option'])) : '';

		global $post;

		$post_id =  isset($post->ID) ? intval($post->ID) : 0;
		$post_id =!empty($_POST['post_id'])? absint(wp_unslash(filter_input( INPUT_POST, 'post_id',  FILTER_SANITIZE_NUMBER_INT ))):$post_id ;


		$post_type = get_post_type($post_id);
	 

		$builder_json = get_post_meta($post_id, 'vh_builder_json', true);

		if (!empty($library)) {
			$library_data = vh_library_array();
 			$builder_json = isset($library_data[$id]['value']) ? $library_data[$id]['value'] : '';
		} elseif (!empty($json)) {
			$builder_json = !empty($option) ? wp_kses_post($option) : '';
		}

		$decoded = json_decode(stripslashes($builder_json), true);

	 

		$navbar_json = !empty($decoded['navbar']) ? urldecode($decoded['navbar']) : '';
		$navbar = vh_json_array_row($navbar_json);

		$slug = get_post_field('post_name', $post_id);
		$vh_has_default = has_filter('vh_has_default') ? apply_filters('vh_has_default', $slug) : '';
		$default_class = !empty($vh_has_default) ? 'vh_is_default' : '';

		echo '<div class="vh_builder vh_desktop_active">';

		$name = isset($decoded['name']) ? $decoded['name'] : '';

		echo '<div class="vh_builder_content">';

		echo '<div class="vh_builder_heading">';
		vh_global_options($decoded);

		if (has_filter('vh_library')) {
			echo '<a class="vh_btn vh_library">' . esc_html__('Library', 'visual-header') . '</a>';
		}

		echo '<a class="vh_btn vh_import_header">' . esc_html__('Import', 'visual-header') . '</a>';
		echo '<a class="vh_btn vh_export_header">' . esc_html__('Export', 'visual-header') . '</a>';
		echo '<a class="vh_btn vh_make_it_default ' . esc_attr($default_class) . '">' . esc_html__('Make it Default', 'visual-header') . '</a>';
		echo '<a class="vh_btn vh_desktop_layout">' . esc_html__('Desktop', 'visual-header') . '</a>';
		echo '<a class="vh_btn vh_mobile_layout">' . esc_html__('Mobile', 'visual-header') . '</a>';
		echo '<a class="vh_btn vh_full_screen">' . esc_html__('Full Screen', 'visual-header') . '</a>';
		echo '<a class="vh_btn vh_close_full_screen">' . esc_html__('Close Full Screen', 'visual-header') . '</a>';
		echo '</div>';

		echo '<ul class="vh_navbar_list">';
		if (has_filter('vh_builder_navbar')) {
			foreach (apply_filters('vh_builder_navbar', $navbar) as $navbar_key => $navbar_value) {
				vh_builder_navbar($decoded, $navbar_key, $navbar_value);
			}
		}
		echo '</ul>';

		echo '</div>'; // .vh_builder_content

		echo '<div class="vh_preview_wrap">';
		echo '<iframe class="vh_preview" src="' . esc_url(add_query_arg([
			'p' => $post_id,
			'post_type' => $post_type,
			'vh_preview' => 'true'
		], home_url('/'))) . '"></iframe>';
		echo '</div>';

		echo '<textarea name="vh_builder_json" id="vh_builder_json" hidden>' . wp_kses_post($builder_json) . '</textarea>';
		echo '</div>'; // .vh_builder

		if (empty($none_ajax)) {
			wp_die();  
		}
	}
}

/**
 * Convert nested JSON row to flat array safely
 */
if ( ! function_exists( 'vh_json_array_row' ) ) {
	function vh_json_array_row($row) {
		$options = json_decode(stripslashes($row), true);

		if (json_last_error() !== JSON_ERROR_NONE || !is_array($options)) {
			return [];
		}

		$array = [];

		foreach ($options as $group) {
			if (is_array($group)) {
				foreach ($group as $key => $value) {
					if (!array_key_exists($key, $array)) {
						$array[$key] = $value;
					}
				}
			}
		}

		return $array;
	}
}

/**
 * Output global builder options
 */
if ( ! function_exists( 'vh_global_options' ) ) {
	function vh_global_options($get_option = false) {
		$options_array = vh_global_options_array();

		if (!empty($options_array)) {
			foreach ($options_array as $id => $value) {
				echo '<a class="vh_btn vh_global_options" data-id="' . esc_attr($id) . '" data-row="global">';
				echo '<img src="' . esc_url($value['img']) . '"><span>' . esc_html($value['name']) . '</span>';
				$options = isset($get_option[$id]) ? urldecode($get_option[$id]) : '';
				echo '<vh_data_json class="vh_data_json" data-row="global">' . wp_kses_post($options) . '</vh_data_json>';
				echo '</a>';
			}
		}
	}
}

/**
 * Returns global options array with filters applied
 */
if ( ! function_exists( 'vh_global_options_array' ) ) {
	function vh_global_options_array() {
		$global = [];
		if (has_filter('vh_global_options')) {
			$global = apply_filters('vh_global_options', $global);
		}
		return $global;
	}
}
