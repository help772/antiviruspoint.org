<?php

namespace Ademti\WoocommerceProductFeeds\Admin;

use Ademti\WoocommerceProductFeeds\Admin\Blueprints\ConfigExporter;
use Ademti\WoocommerceProductFeeds\Admin\Blueprints\FeedConfigExporter;

class BlueprintHandler {

	/**
	 * @return void
	 */
	public function run() {
		add_filter( 'wooblueprint_exporters', [ $this, 'register_exporters' ] );
	}

	/**
	 * @param  array  $exporters
	 *
	 * @return array
	 */
	public function register_exporters( array $exporters ): array {
		$exporters[] = new ConfigExporter();
		$exporters[] = new FeedConfigExporter();

		return $exporters;
	}
}
