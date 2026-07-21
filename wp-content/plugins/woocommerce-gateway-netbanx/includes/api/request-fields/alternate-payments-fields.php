<?php

namespace WcPaysafe\Api\Request_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Description
 *
 * @since  3.3.0
 * @author VanboDevelops
 *
 *        Copyright: (c) 2018-2019 VanboDevelops
 *        License: GNU General Public License v3.0
 *        License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */
class Alternate_Payments_Fields extends Common_Fields {
	
	public function get_return_links() {
		
		$links = array(
			array(
				'rel'  => 'default',
				'href' => $this->get_source()->return_url()
			),
			
			array(
				'rel'  => 'on_cancelled',
				'href' => $this->get_source()->get_cancel_url()
			),
			
			array(
				'rel'  => 'on_error',
				'href' => $this->get_source()->return_url()
			),
		
		);
		
		return $links;
	}
}