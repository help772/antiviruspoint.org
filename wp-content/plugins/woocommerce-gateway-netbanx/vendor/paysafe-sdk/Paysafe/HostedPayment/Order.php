<?php
/*
 * Copyright (c) 2014 Optimal Payments
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and
 * associated documentation files (the "Software"), to deal in the Software without restriction,
 * including without limitation the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or
 * substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace Paysafe\HostedPayment;

/**
 * @property string                                 $id
 * @property string                                 $merchantRefNum
 * @property string                                 $currencyCode
 * @property int                                    $totalAmount
 * @property string                                 $customerIp
 * @property email                                  $customerNotificationEmail
 * @property email                                  $merchantNotificationEmail
 * @property string                                 $dueDate
 * @property \Paysafe\HostedPayment\Profile         $profile
 * @property \Paysafe\HostedPayment\CartItem[]      $shoppingCart
 * @property \Paysafe\HostedPayment\AncillaryFee[]  $ancillaryFees
 * @property \Paysafe\HostedPayment\BillingDetails  $billingDetails
 * @property \Paysafe\HostedPayment\ShippingDetails $shippingDetails
 * @property \Paysafe\HostedPayment\Callback[]      $callback
 * @property \Paysafe\HostedPayment\Redirect[]      $redirect
 * @property \Paysafe\HostedPayment\Link[]          $link
 * @property string[]                               $paymentMethod
 * @property \Paysafe\HostedPayment\KeyValuePair[]  $addendumData
 * @property \Paysafe\HostedPayment\Locale          $locale
 * @property \Paysafe\HostedPayment\KeyValuePair[]  $extendedOptions
 * @property \Paysafe\HostedPayment\Transaction[]   $associatedTransactions
 * @property \Paysafe\HostedPayment\Transaction     $transaction
 * @property \Paysafe\Error                         $error
 * @property \Paysafe\Link[]                        $links
 */
class Order extends \Paysafe\JSONObject implements \Paysafe\Pageable {
	
	public static function getPageableArrayKey() {
		return "records";
	}
	
	protected static $fieldTypes = array(
		'id'                        => 'string',
		'merchantRefNum'            => 'string',
		'currencyCode'              => 'string',
		'totalAmount'               => 'int',
		'customerIp'                => 'string',
		'customerNotificationEmail' => 'email',
		'merchantNotificationEmail' => 'email',
		'dueDate'                   => 'string',
		'profile'                   => '\Paysafe\HostedPayment\Profile',
		'shoppingCart'              => 'array:\Paysafe\HostedPayment\CartItem',
		'ancillaryFees'             => 'array:\Paysafe\HostedPayment\AncillaryFee',
		'billingDetails'            => '\Paysafe\HostedPayment\BillingDetails',
		'shippingDetails'           => '\Paysafe\HostedPayment\ShippingDetails',
		'callback'                  => 'array:\Paysafe\HostedPayment\Callback',
		'redirect'                  => 'array:\Paysafe\HostedPayment\Redirect',
		'link'                      => 'array:\Paysafe\HostedPayment\Link',
		'mode'                      => 'string',
		'type'                      => 'string',
		'paymentMethod'             => 'array:string',
		'addendumData'              => 'array:\Paysafe\HostedPayment\KeyValuePair',
		'locale'                    => array(
			'en_US',
			'fr_CA',
			'en_GB'
		),
		'extendedOptions'           => 'array:\Paysafe\HostedPayment\KeyValuePair',
		'associatedTransactions'    => 'array:\Paysafe\HostedPayment\Transaction',
		'transaction'               => '\Paysafe\HostedPayment\Transaction',
		'error'                     => '\Paysafe\Error',
	);
	
	/**
	 *
	 * @param string $linkName
	 *
	 * @return \Paysafe\HostedPayment\Link
	 * @throws \Paysafe\PaysafeException
	 */
	public function getLink( $linkName ) {
		if ( ! empty( $this->link ) ) {
			foreach ( $this->link as $link ) {
				if ( $link->rel == $linkName ) {
					return $link;
				}
			}
		}
		throw new \Paysafe\PaysafeException( "Link $linkName not found in order." );
	}
}
