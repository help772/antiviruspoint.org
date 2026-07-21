<?php
/*
 * Copyright (c) 2014 OptimalPayments
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

namespace Paysafe;

class AlternatePaymentService {
	
	/**
	 * @var PaysafeApiClient
	 */
	private $client;
	/**
	 * The uri for the card payment api.
	 * @var string
	 */
	private $uri = "alternatepayments/v1";
	private $accountsPath = "/accounts";
	private $paymentsPath = "/payments";
	private $verificationsPath = '/verifications';
	private $voidauthsPath = '/voidauths';
	private $settlementsPath = '/settlements';
	private $refundsPath = '/refunds';
	private $returnreversalsPath = '/returnreversals';
	private $standalonecreditsPath = '/standalonecredits';
	private $uriseparator = "/";
	
	/**
	 * Initialize the card payment service.
	 *
	 * @param \Paysafe\PaysafeApiClient $client
	 */
	public function __construct( PaysafeApiClient $client ) {
		$this->client = $client;
	}
	
	/**
	 * Monitor.
	 *
	 * @return bool true if successful
	 * @throws PaysafeException
	 */
	public function monitor() {
		$request = new Request( array(
			'method' => Request::GET,
			'uri'    => 'alternatepayments/monitor'
		) );
		
		$response = $this->client->processRequest( $request );
		
		return ( $response['status'] == 'READY' );
	}
	
	/**
	 * Authorize.
	 *
	 * @param AlternatePayments\Payment $auth
	 *
	 * @return AlternatePayments\Payment
	 * @throws PaysafeException
	 */
	public function createPayment( AlternatePayments\Payment $payment ) {
		$payment->setRequiredFields( array(
			'merchantRefNum',
			'amount',
			'paymentType',
		) );
		$payment->setOptionalFields( array(
			'paymentType',
			'currencyCode',
			'settleWithAuth',
			'profile',
			'customerIp',
			'dupCheck',
			'billingDetails',
			'shippingDetails',
		) );
		
		// Note: We are only supporting Interac payments, so there are no additional checks to this method.
		//      If we add additional payment types, we will need to check and submit a payment type specific request
		
		$request  = new Request( array(
			'method' => Request::POST,
			'uri'    => $this->prepareURI( $this->paymentsPath ),
			'body'   => $payment
		) );
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Payment( $response );
	}
	
	/**
	 * Returns the payment corresponding to the ID.
	 *
	 * @param AlternatePayments\Payment $payment
	 *
	 * @return AlternatePayments\Payment
	 * @throws PaysafeException
	 */
	public function getPayment( AlternatePayments\Payment $payment ) {
		$payment->setRequiredFields( array( 'id' ) );
		$payment->checkRequiredFields();
		
		$request = new Request( array(
			'method' => Request::GET,
			'uri'    => $this->prepareURI( $this->paymentsPath . $this->uriseparator . $payment->id )
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Payment( $response );
	}
	
	/**
	 * Returns the matching payments for the provided merchantRefNum
	 *
	 * @param AlternatePayments\Payment $payment
	 *
	 * @return AlternatePayments\Pagerator
	 * @throws PaysafeException
	 */
	public function getPaymentsByMerchantRefNumber( AlternatePayments\Payment $payment ) {
		$payment->setRequiredFields( array( 'merchantRefNum' ) );
		$payment->checkRequiredFields();
		
		$request = new Request( array(
			'method'   => Request::GET,
			'uri'      => $this->prepareURI( $this->paymentsPath . $this->uriseparator ),
			'queryStr' => array( 'merchantRefNum' => $payment->merchantRefNum ),
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Pagerator( $this->client, $response, '\Paysafe\AlternativePayments\Payment' );
	}
	
	/**
	 * Verify.
	 *
	 * @param \Paysafe\AlternatePayments\Verification $verify
	 *
	 * @return \Paysafe\AlternatePayments\Verification
	 * @throws PaysafeException
	 */
	public function verify( AlternatePayments\Verification $verify ) {
		$verify->setRequiredFields( array(
			'merchantRefNum',
			'amount',
			'profile',
			'paymentType',
		) );
		$verify->setOptionalFields( array(
			'dupCheck',
			'currencyCode',
			'billingDetails',
			'shippingDetails',
			'payolution',
		) );
		
		$request = new Request( array(
			'method' => Request::POST,
			'uri'    => $this->prepareURI( $this->verificationsPath ),
			'body'   => $verify
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Verification( $response );
	}
	
	/**
	 * Get the verification.
	 *
	 * @param AlternatePayments\Verification $verify
	 *
	 * @return AlternatePayments\Verification
	 * @throws PaysafeException
	 */
	public function getVerification( AlternatePayments\Verification $verify ) {
		$verify->setRequiredFields( array( 'id' ) );
		$verify->checkRequiredFields();
		
		$request = new Request( array(
			'method' => Request::GET,
			'uri'    => $this->prepareURI( $this->verificationsPath . $this->uriseparator . $verify->id )
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Verification( $response );
	}
	
	/**
	 * Voids an authorized payment.
	 *
	 * @param \Paysafe\AlternatePayments\VoidAuth $voidAuth
	 *
	 * @return \Paysafe\AlternatePayments\VoidAuth
	 * @throws PaysafeException
	 */
	public function voidPayment( AlternatePayments\VoidAuth $voidAuth ) {
		$voidAuth->setRequiredFields( array( 'paymentId', 'merchantRefNum' ) );
		$voidAuth->checkRequiredFields();
		
		$request = new Request( array(
			'method' => Request::POST,
			'uri'    => $this->prepareURI( $this->paymentsPath . $this->uriseparator . $voidAuth->paymentId . $this->voidauthsPath ),
			'body'   => $voidAuth
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\VoidAuth( $response );
	}
	
	/**
	 * Returns the voided payment matching the ID
	 *
	 * @param AlternatePayments\VoidAuth $voidAuth
	 *
	 * @return AlternatePayments\VoidAuth
	 * @throws PaysafeException
	 */
	public function getVoidedPayment( AlternatePayments\VoidAuth $voidAuth ) {
		$voidAuth->setRequiredFields( array( 'id', 'paymentId' ) );
		$voidAuth->checkRequiredFields();
		
		$request = new Request( array(
			'method' => Request::GET,
			'uri'    => $this->prepareURI( $this->paymentsPath . $this->uriseparator . $voidAuth->paymentId . $this->voidauthsPath . $this->uriseparator . $voidAuth->id ),
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\VoidAuth( $response );
	}
	
	/**
	 * Returns the voided payments matching account and payment ID
	 *
	 * @param AlternatePayments\VoidAuth $voidAuth
	 *
	 * @return AlternatePayments\Pagerator
	 * @throws PaysafeException
	 */
	public function getVoidedPayments( AlternatePayments\VoidAuth $voidAuth ) {
		$voidAuth->setRequiredFields( array( 'paymentId' ) );
		$voidAuth->checkRequiredFields();
		
		$request = new Request( array(
			'method' => Request::GET,
			'uri'    => $this->prepareURI( $this->paymentsPath . $this->uriseparator . $voidAuth->paymentId . $this->voidauthsPath . $this->uriseparator . $voidAuth->id ),
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Pagerator( $this->client, $response, '\Paysafe\AlternativePayments\VoidAuth' );
	}
	
	/**
	 * Settlement.
	 *
	 * @param \Paysafe\AlternatePayments\Settlement $settlement
	 *
	 * @return \Paysafe\AlternatePayments\Settlement
	 * @throws PaysafeException
	 */
	public function settlement( AlternatePayments\Settlement $settlement ) {
		$settlement->setRequiredFields( array(
				'paymentId',
				'merchantRefNum',
				'amount',
				'currencyCode',
			)
		);
		$settlement->checkRequiredFields();
		$settlement->setOptionalFields( array(
			'dupCheck',
		) );
		
		$request = new Request( array(
			'method' => Request::POST,
			'uri'    => $this->prepareURI( $this->paymentsPath . $this->uriseparator . $settlement->paymentId . $this->settlementsPath ),
			'body'   => $settlement
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Settlement( $response );
	}
	
	/**
	 * Get the settlement.
	 *
	 * @param AlternatePayments\Settlement $settlement
	 *
	 * @return AlternatePayments\Settlement
	 * @throws PaysafeException
	 */
	public function getSettlement( AlternatePayments\Settlement $settlement ) {
		$settlement->setRequiredFields( array( 'id' ) );
		$settlement->checkRequiredFields();
		
		$request = new Request( array(
			'method' => Request::GET,
			'uri'    => $this->prepareURI( $this->settlementsPath . $this->uriseparator . $settlement->id )
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Settlement( $response );
	}
	
	/**
	 * Get matching settlements.
	 *
	 * @param AlternatePayments\Settlement $settlement
	 * @param AlternatePayments\Filter     $filter
	 *
	 * @return AlternatePayments\Settlement[]|AlternatePayments\Pagerator iterator
	 * @throws PaysafeException
	 */
	public function getSettlements( AlternatePayments\Settlement $settlement = null, AlternatePayments\Filter $filter = null ) {
		$queryStr = array();
		if ( $settlement && $settlement->merchantRefNum ) {
			$queryStr['merchantRefNum'] = $settlement->merchantRefNum;
		}
		if ( $filter ) {
			if ( isset( $filter->limit ) ) {
				$queryStr['limit'] = $filter->limit;
			}
			if ( isset( $filter->offset ) ) {
				$queryStr['offset'] = $filter->offset;
			}
			if ( isset( $filter->startDate ) ) {
				$queryStr['startDate'] = $filter->startDate;
			}
			if ( isset( $filter->endDate ) ) {
				$queryStr['endDate'] = $filter->endDate;
			}
		}
		$request = new Request( array(
			'method'   => Request::GET,
			'uri'      => $this->prepareURI( $this->settlementsPath ),
			'queryStr' => $queryStr
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Pagerator( $this->client, $response, '\Paysafe\AlternativePayments\Settlement' );
	}
	
	/**
	 * Refund.
	 *
	 * @param \Paysafe\AlternatePayments\Refund $refund
	 *
	 * @return \Paysafe\AlternatePayments\Refund
	 * @throws PaysafeException
	 */
	public function refund( AlternatePayments\Refund $refund ) {
		$refund->setRequiredFields( array(
				'paymentId',
				'merchantRefNum',
				'amount',
				'currencyCode',
			)
		);
		$refund->checkRequiredFields();
		$refund->setOptionalFields( array(
			'dupCheck',
		) );
		
		$request = new Request( array(
			'method' => Request::POST,
			'uri'    => $this->prepareURI( $this->paymentsPath . $this->uriseparator . $refund->paymentId . $this->refundsPath ),
			'body'   => $refund
		) );
		
		$response = $this->client->processRequest( $request );
		
		return new AlternatePayments\Refund( $response );
	}
	
	/**
	 * Prepare the uri for submission to the api.
	 *
	 * @param string $path
	 *
	 * @return string uri
	 * @throws PaysafeException
	 */
	private function prepareURI( $path ) {
		if ( ! $this->client->getAccount() ) {
			throw new PaysafeException( 'Missing or invalid account', 500 );
		}
		
		return $this->uri . $this->accountsPath . $this->client->getAccount() . $path;
	}
}
