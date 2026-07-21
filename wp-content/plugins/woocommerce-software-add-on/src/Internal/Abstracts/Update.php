<?php
/**
 * Abstract Update class.
 *
 * @since 1.9.0
 */

namespace Themesquad\WC_Software_Addon\Internal\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Class Update.
 */
abstract class Update {

	/**
	 * Update ID.
	 *
	 * @var string
	 */
	protected $id = '';

	/**
	 * Update args.
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Constructor.
	 *
	 * @since 1.9.0
	 *
	 * @param array $args Update args.
	 */
	public function __construct( $args = array() ) {
		$this->args = $args;

		if ( ! $this->id ) {
			$this->id = $this->generate_id();
		}
	}

	/**
	 * Gets the update ID.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Generates the update ID.
	 *
	 * @since 1.9.0
	 *
	 * @return string
	 */
	protected function generate_id() {
		$class = ( new \ReflectionClass( $this ) )->getShortName();

		return strtolower( $class );
	}

	/**
	 * Runs the update.
	 *
	 * @since 1.9.0
	 *
	 * @return bool Whether the update needs to run again.
	 */
	abstract public function run();
}
