<?php
/**
 * Unit tests for Australia Post satchel service matching and priority selection.
 *
 * Regression coverage for WOOASPT-13: the Australia Post API can return satchel
 * codes that are not in a service's hard-coded alternate_services list, and
 * "prioritize satchel" mode must consistently pick the cheapest available
 * satchel instead of falling back to the full parcel rate.
 *
 * @package WC_Shipping_Australia_Post
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests for WC_Shipping_Australia_Post::service_matches_quote() and
 * should_select_priority_satchel().
 */
class Satchel_Selection_Test extends TestCase {

	/**
	 * Build a WC_Shipping_Australia_Post instance without invoking its
	 * constructor (which needs the full WooCommerce environment).
	 *
	 * @return WC_Shipping_Australia_Post
	 */
	private function shipping_method() {
		return ( new ReflectionClass( WC_Shipping_Australia_Post::class ) )->newInstanceWithoutConstructor();
	}

	/**
	 * Invoke a private method via reflection.
	 *
	 * @param object $object Target object.
	 * @param string $name   Method name.
	 * @param array  $args   Arguments.
	 * @return mixed
	 */
	private function invoke( $object, string $name, array $args ) {
		$method = new ReflectionMethod( $object, $name );
		$method->setAccessible( true );
		return $method->invokeArgs( $object, $args );
	}

	/**
	 * Build a fake Australia Post API quote object.
	 *
	 * @param string $code  Service code.
	 * @param float  $price Price.
	 * @return stdClass
	 */
	private function quote( string $code, float $price = 0.0 ): stdClass {
		$quote        = new stdClass();
		$quote->code  = $code;
		$quote->price = $price;
		return $quote;
	}

	/**
	 * The Regular service config, loaded from the plugin's own service data.
	 *
	 * @return array
	 */
	private function regular_config(): array {
		$services = require WC_SHIPPING_AUSTRALIA_POST_ABSPATH . 'includes/data/data-services.php';
		return $services['AUS_PARCEL_REGULAR'];
	}

	/**
	 * Convenience: does a code match the Regular service?
	 *
	 * @param string $code Quote code.
	 * @return bool
	 */
	private function regular_matches( string $code ): bool {
		return (bool) $this->invoke(
			$this->shipping_method(),
			'service_matches_quote',
			array( 'AUS_PARCEL_REGULAR', $this->regular_config(), $this->quote( $code ) )
		);
	}

	public function test_matches_the_service_code_itself(): void {
		$this->assertTrue( $this->regular_matches( 'AUS_PARCEL_REGULAR' ) );
	}

	public function test_matches_a_listed_alternate_satchel(): void {
		// AUS_PARCEL_REGULAR_SATCHEL_500G is explicitly in alternate_services.
		$this->assertTrue( $this->regular_matches( 'AUS_PARCEL_REGULAR_SATCHEL_500G' ) );
	}

	public function test_matches_a_listed_alternate_letter(): void {
		// Letters are alternate_services but not satchels; they must still match.
		$this->assertTrue( $this->regular_matches( 'AUS_LETTER_REGULAR_SMALL' ) );
	}

	/**
	 * Core fix: satchel sizes the API returns that are NOT in alternate_services
	 * (250G / SMALL / EXTRA_SMALL) must still match the Regular service so the
	 * cheaper satchel is not ignored.
	 */
	public function test_matches_unlisted_satchel_sizes_by_prefix(): void {
		$config = $this->regular_config();

		// Guard: these codes are genuinely absent from the hard-coded list, so a
		// match can only come from the new satchel-prefix rule.
		$this->assertNotContains( 'AUS_PARCEL_REGULAR_SATCHEL_250G', $config['alternate_services'] );
		$this->assertNotContains( 'AUS_PARCEL_REGULAR_SATCHEL_SMALL', $config['alternate_services'] );
		$this->assertNotContains( 'AUS_PARCEL_REGULAR_SATCHEL_EXTRA_SMALL', $config['alternate_services'] );

		$this->assertTrue( $this->regular_matches( 'AUS_PARCEL_REGULAR_SATCHEL_250G' ) );
		$this->assertTrue( $this->regular_matches( 'AUS_PARCEL_REGULAR_SATCHEL_SMALL' ) );
		$this->assertTrue( $this->regular_matches( 'AUS_PARCEL_REGULAR_SATCHEL_EXTRA_SMALL' ) );
	}

	public function test_does_not_match_another_services_satchel(): void {
		// An Express satchel must not be attributed to the Regular service.
		$this->assertFalse( $this->regular_matches( 'AUS_PARCEL_EXPRESS_SATCHEL_SMALL' ) );
	}

	public function test_does_not_match_non_satchel_package_code(): void {
		// A "package" code (not a satchel, not listed) must not match.
		$this->assertFalse( $this->regular_matches( 'AUS_PARCEL_REGULAR_PACKAGE_EXTRA_SMALL' ) );
	}

	public function test_does_not_match_a_different_service(): void {
		$this->assertFalse( $this->regular_matches( 'AUS_PARCEL_EXPRESS' ) );
	}

	/**
	 * Reproduces the reported scenario (Book 1's request, taken from the ticket
	 * logs): the only Regular satchels returned are the unlisted 250G /
	 * EXTRA_SMALL sizes. With the fix a satchel is now selectable, so the cheapest
	 * matched Regular rate is the $10.05 satchel rather than the $19.30 parcel
	 * rate. Before the fix neither satchel matched and the cart fell back to
	 * $19.30.
	 */
	public function test_unlisted_satchels_become_selectable_for_regular(): void {
		$quotes = array(
			$this->quote( 'AUS_PARCEL_REGULAR', 19.30 ),
			$this->quote( 'AUS_PARCEL_REGULAR_SATCHEL_EXTRA_SMALL', 10.05 ),
			$this->quote( 'AUS_PARCEL_REGULAR_PACKAGE_EXTRA_SMALL', 9.70 ),
			$this->quote( 'AUS_PARCEL_REGULAR_SATCHEL_250G', 10.05 ),
		);

		$shipping_method = $this->shipping_method();
		$config          = $this->regular_config();

		$matched = array();
		foreach ( $quotes as $quote ) {
			if ( $this->invoke( $shipping_method, 'service_matches_quote', array( 'AUS_PARCEL_REGULAR', $config, $quote ) ) ) {
				$matched[] = $quote;
			}
		}

		$codes  = array_map( static fn( $quote ) => $quote->code, $matched );
		$prices = array_map( static fn( $quote ) => $quote->price, $matched );

		// Both satchels are now matched (previously neither was).
		$this->assertContains( 'AUS_PARCEL_REGULAR_SATCHEL_EXTRA_SMALL', $codes );
		$this->assertContains( 'AUS_PARCEL_REGULAR_SATCHEL_250G', $codes );
		// The non-satchel "package" code stays excluded.
		$this->assertNotContains( 'AUS_PARCEL_REGULAR_PACKAGE_EXTRA_SMALL', $codes );
		// The cheapest matched Regular rate is now the $10.05 satchel, not $19.30.
		$this->assertSame( 10.05, min( $prices ) );
	}

	/**
	 * In priority mode the first satchel seen is always selected, even before any
	 * cost has been recorded.
	 */
	public function test_priority_first_satchel_is_always_selected(): void {
		$this->assertTrue( $this->invoke( $this->shipping_method(), 'should_select_priority_satchel', array( false, null, 11.50 ) ) );
	}

	/**
	 * A non-satchel rate may have set the running cost first; the first satchel
	 * must still take over in priority mode.
	 */
	public function test_priority_first_satchel_overrides_a_non_satchel_cost(): void {
		$this->assertTrue( $this->invoke( $this->shipping_method(), 'should_select_priority_satchel', array( false, 19.30, 11.50 ) ) );
	}

	public function test_priority_cheaper_satchel_is_selected(): void {
		$this->assertTrue( $this->invoke( $this->shipping_method(), 'should_select_priority_satchel', array( true, 11.50, 10.05 ) ) );
	}

	public function test_priority_pricier_satchel_is_rejected(): void {
		// Guards against the original "last satchel wins" bug.
		$this->assertFalse( $this->invoke( $this->shipping_method(), 'should_select_priority_satchel', array( true, 10.05, 11.50 ) ) );
	}

	public function test_priority_equal_priced_satchel_is_rejected(): void {
		$this->assertFalse( $this->invoke( $this->shipping_method(), 'should_select_priority_satchel', array( true, 11.50, 11.50 ) ) );
	}
}
