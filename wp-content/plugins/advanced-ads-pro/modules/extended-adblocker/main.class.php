<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName

use AdvancedAds\Modal;
use AdvancedAds\Framework\Utilities\Params;
use AdvancedAds\Utilities\Conditional;

/**
 * Adblocker
 */
class Advanced_Ads_Pro_Module_Extended_Adblocker {

	/**
	 * Setting options
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Overlay time frequency
	 *
	 * @var string
	 */
	private $show_frequency;

	/**
	 * Overlay last displayed: cookie name
	 *
	 * @var string
	 */
	private const COOKIE_NAME = AA_PRO_SLUG . '-overlay-last-display';


	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->options           = Advanced_Ads::get_instance()->get_adblocker_options();
		$this->show_frequency    = $this->options['overlay']['time_frequency'] ?? 'everytime';
		$this->options['method'] = $this->options['method'] ?? 'nothing';

		add_action( 'init', [ $this, 'print_extended_adblocker' ], 99 );
	}

	/**
	 * Print the appropriate code on frontend.
	 *
	 * @return void
	 */
	public function print_extended_adblocker(): void {
		if ( ! $this->prechecks() ) {
			return;
		}

		$method_name = '';

		if ( 'overlay' === $this->options['method'] ) {
			wp_enqueue_style( 'eab-modal', plugin_dir_url( __FILE__ ) . 'assets/css/modal.css', [], AAP_VERSION );
			if ( $this->should_show_overlay() ) {
				$this->set_last_display_time();
				$method_name = 'print_overlay';
			}
		} elseif ( 'redirect' === $this->options['method'] ) {
			$method_name = 'print_redirect';
		}

		if ( method_exists( $this, $method_name ) ) {
			add_action( 'wp_footer', [ $this, $method_name ], 99 );
		}
	}

	/**
	 * Overlay logic
	 *
	 * @return void
	 */
	public function print_overlay(): void {
		$button_text = null;

		// Show dismiss only when hide button is unchecked.
		if ( ! isset( $this->options['overlay']['hide_dismiss'] ) ) {
			$button_text = __( 'Dismiss', 'advanced-ads-pro' );
			if ( isset( $this->options['overlay']['dismiss_text'] ) && '' !== $this->options['overlay']['dismiss_text'] ) {
				$button_text = $this->options['overlay']['dismiss_text'];
			}
		}

		Modal::create(
			[
				'modal_slug'             => 'extended-adblocker',
				'modal_content'          => $this->options['overlay']['content'],
				'close_action'           => $button_text,
				'template'               => plugin_dir_path( __FILE__ ) . 'views/modal.php',
				'dismiss_button_styling' => $this->options['overlay']['dismiss_style'],
				'container_styling'      => $this->options['overlay']['container_style'],
				'background_styling'     => $this->options['overlay']['background_style'],
			]
		);
		?>
		<script>
			jQuery(function() {
				window.advanced_ads_check_adblocker( function ( is_enabled ) {
					if ( is_enabled ) {
						document.getElementById('modal-extended-adblocker').showModal();
					}
				} );
			});
		</script>
		<?php
	}

	/**
	 * Redirect logic
	 *
	 * @return void
	 */
	public function print_redirect(): void {
		$url = $this->options['redirect']['url'] ?? '';

		$current_url = ( is_ssl() ? 'https://' : 'http://' ) .
					Params::server( 'HTTP_HOST' ) .
					Params::server( 'REQUEST_URI' );

		if ( ! empty( $url ) && ! $this->compare_urls( $url, $current_url ) ) {
			?>
			<script>
				jQuery(function() {
					window.advanced_ads_check_adblocker( function ( is_enabled ) {
						if ( is_enabled ) {
							window.location.href = '<?php echo esc_html( $url ); ?>';
						}
					} );
				});
			</script>
			<?php
		}
	}

	/**
	 * Conditions which has to pass to go ahead
	 *
	 * @return bool
	 */
	private function prechecks(): bool {
		/**
		 * Don't run:
		 * - on AMP
		 * - when method is set to 'nothing'
		 */
		if ( Conditional::is_amp() || 'nothing' === $this->options['method'] ) {
			return false;
		}

		// check if exclude matches with current user.
		$hide_for_roles = isset( $this->options['exclude'] )
			? Advanced_Ads_Utils::maybe_translate_cap_to_role( $this->options['exclude'] )
			: [];
		$user           = wp_get_current_user();

		if ( is_user_logged_in() &&
			$hide_for_roles &&
			is_array( $user->roles ) &&
			array_intersect( $hide_for_roles, $user->roles ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Overlay Helpers
	 *
	 * @return int
	 */
	private function get_last_display_time(): int {
		return Params::cookie( self::COOKIE_NAME, 0 );
	}

	/**
	 * Overlay Helpers
	 *
	 * @return void
	 */
	private function set_last_display_time(): void {
		$expire_time = time() + $this->get_interval_from_frequency();
		setcookie( self::COOKIE_NAME, time(), $expire_time, '/' );
	}

	/**
	 * Overlay Helpers
	 *
	 * @return bool
	 */
	private function should_show_overlay(): bool {
		switch ( $this->show_frequency ) {
			case 'everytime':
				return true;
			case 'never':
				return false;
			default:
				$last_display_time = $this->get_last_display_time();
				$interval          = $this->get_interval_from_frequency( $this->show_frequency );
				return time() >= $last_display_time + $interval;
		}
	}

	/**
	 * Overlay Helpers
	 *
	 * @return int
	 */
	private function get_interval_from_frequency(): int {
		// Map frequency strings to seconds.
		$intervals = [
			'everytime' => 0,
			'hour'      => 3600,
			'day'       => 86400,
			'week'      => 604800,
			'month'     => 2592000, // 30 days.
		];

		return $intervals[ $this->show_frequency ];
	}

	/**
	 * Compare URLs
	 *
	 * @param string $url1 URL in database.
	 * @param string $url2 current page url.
	 *
	 * @return bool
	 */
	private function compare_urls( $url1, $url2 ): bool {
		$url1 = wp_parse_url( $url1 );
		$url2 = wp_parse_url( $url2 );

		return $url1['path'] === $url2['path'];
	}
}
