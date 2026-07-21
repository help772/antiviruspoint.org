<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class WC_Product_Advanced_Ad extends WC_Product {

	/**
     * @var string $product_type The type of the product (advanced_ad).
     */
    protected $product_type = 'advanced_ad';

	public function __construct( $product ) {
	    
	    parent::__construct( $product );
	}
}