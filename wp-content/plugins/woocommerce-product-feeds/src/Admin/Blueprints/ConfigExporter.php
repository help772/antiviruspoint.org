<?php

namespace Ademti\WoocommerceProductFeeds\Admin\Blueprints;

use Automattic\WooCommerce\Blueprint\Exporters\HasAlias;
use Automattic\WooCommerce\Blueprint\Exporters\StepExporter;
use Automattic\WooCommerce\Blueprint\Steps\SetSiteOptions;
use Automattic\WooCommerce\Blueprint\Steps\Step;
use function current_user_can;

class ConfigExporter implements StepExporter, HasAlias {

	public function get_step_name(): string {
		return 'setSiteOptions';
	}

	public function get_alias(): string {
		return 'setGpfConfig';
	}

	public function get_label(): string {
		return __( 'Google Product Feed configuration', 'woocommerce_gpf' );
	}

	public function get_description(): string {
		return __( 'Google Product Feed configuration & field mappings as per WooCommerce | Settings | Product Feeds', 'woocommerce_gpf' );
	}

	public function check_step_capabilities(): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * @return Step
	 */
	public function export(): Step {
		$data = [
			'woocommerce_gpf_config' => get_option( 'woocommerce_gpf_config', [] ),
		];
		return new SetSiteOptions( $data );
	}
}
