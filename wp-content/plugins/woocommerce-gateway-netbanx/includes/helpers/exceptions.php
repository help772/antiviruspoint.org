<?php

namespace WcPaysafe\Helpers;

use Paysafe\PaysafeException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Exceptions {
	
	protected $exception;
	
	public function __construct( \Exception $exception ) {
		$this->exception = $exception;
	}
	
	public function is_paysafe_exception() {
		return $this->exception instanceof PaysafeException;
	}
	
	public function get_errors() {
		
		if ( ! $this->is_paysafe_exception() ) {
			return $this->exception->getMessage();
		}
		
		$errors = array();
		if ( $this->exception->fieldErrors ) {
			foreach ( $this->exception->fieldErrors as $error ) {
				$errors[] = $error['field'] . ': ' . ucfirst( $error['error'] );
			}
		}
		
		if ( empty( $errors ) ) {
			$errors[] = $this->exception->getCode() . ': ' . ucfirst( $this->exception->getMessage() );
		}
		
		return implode( ', ', $errors );
	}
	
	public function get_error() {
		if ( ! $this->is_paysafe_exception() ) {
			return $this->exception->getMessage();
		}
		
		$errors = $this->exception->fieldErrors;
		
		if ( empty( $errors ) ) {
			$errors[] = array(
				'field' => $this->exception->getCode(),
				'error' => ucfirst( $this->exception->getMessage() ),
			);
		}
		
		$error = array_shift( $errors );
		
		return $error['field'] . ': ' . ucfirst( $error['error'] );
	}
	
	public function get_details() {
		return $this->exception->getMessage();
	}
	
	public function get_line() {
		return $this->exception->getFile() . ':' . $this->exception->getLine();
	}
	
	public function get_trace() {
		return $this->exception->getTraceAsString();
	}
}