<?php

namespace Ademti\WoocommerceProductFeeds\Admin\Blueprints;

use Automattic\WooCommerce\Blueprint\Exporters\StepExporter;
use Automattic\WooCommerce\Blueprint\Steps\SetSiteOptions;
use Automattic\WooCommerce\Blueprint\Steps\Step;
use function current_user_can;

class FeedConfigExporter implements StepExporter {

	public function get_step_name() {
		return 'setSiteOptions';
	}

	public function get_alias(): string {
		return 'setGpfFeedConfig';
	}

	public function get_label(): string {
		return __( 'Google Product Feed Feeds', 'woocommerce_gpf' );
	}

	public function get_description(): string {
		return __( 'Google Product Feed feeds as per WooCommerce | Product Feeds', 'woocommerce_gpf' );
	}

	public function check_step_capabilities(): bool {
		return current_user_can( 'manage_options' );
	}

	public function export(): Step {
		$data = [
			'woocommerce_gpf_feed_configs' => get_option( 'woocommerce_gpf_feed_configs', [] ),
		];
		return new SetSiteOptions( $data );
	}
}
