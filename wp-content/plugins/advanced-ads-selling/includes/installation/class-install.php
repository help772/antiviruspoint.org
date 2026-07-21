<?php
/**
 * Installation Install.
 *
 * @package AdvancedAds\SellingAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 * @since   1.5.0
 */

namespace AdvancedAds\SellingAds\Installation;

use AdvancedAds\Framework\Installation\Install as Base;

defined( 'ABSPATH' ) || exit;

/**
 * Installation Install.
 */
class Install extends Base {

	/**
	 * Flush rewrite rules option key
	 *
	 * @var string
	 */
	const FLUSH_KEY = 'advanced-ads-selling-permalinks-flushed';

	/**
	 * Runs this initializer.
	 *
	 * @return void
	 */
	public function initialize(): void {
		$this->base_file = AASA_FILE;
		parent::initialize();
	}

	/**
	 * Plugin activation callback.
	 *
	 * @return void
	 */
	protected function activate(): void {
		if ( version_compare( PHP_VERSION, '5.3.0', '<' ) === -1 ) {
			deactivate_plugins( plugin_basename( 'advanced-ads-selling/advanced-ads-selling.php' ) );
			wp_die( '<em>Advanced Ads – Selling Ads</em> requires PHP 5.3 or higher. Your server is using ' . PHP_VERSION . '. Please contact your server administrator for a PHP update. <a href="' . admin_url( 'plugins.php' ) . '">Back to Plugins</a>' ); // phpcs:ignore
		} else {
			update_option( self::FLUSH_KEY, 0 );
			add_action( 'init', [ $this, 'flush_rewrite_rules_maybe' ] );
		}
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * @return void
	 */
	protected function deactivate(): void {
		$this->flush_and_flag();
	}

	/**
	 * Plugin uninstall callback.
	 *
	 * @return void
	 */
	public static function uninstall(): void {}

	/**
	 * Flush Permalink cache and set a flag
	 *
	 * @return void
	 */
	private function flush_and_flag(): void {
		flush_rewrite_rules( false );
		update_option( self::FLUSH_KEY, 1 );
	}

	/**
	 * Flush rewrite rules if the previously added flag doesn't exist,
	 * and then set the flag
	 */
	protected function flush_rewrite_rules_maybe() {
		$check = absint( get_option( self::FLUSH_KEY ) );
		if ( ! $check || 0 === $check ) {
			$this->flush_and_flag();
		}
	}
}
