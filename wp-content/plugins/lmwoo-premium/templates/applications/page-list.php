<?php

use LicenseManagerForWooCommerce\Lists\ApplicationsList;

defined( 'ABSPATH' ) || exit;

/**
 * Application Object
 * 
 * @var Application $application
**/

?>

<h1 class="wp-heading-inline"><?php echo esc_html__( 'Applications', 'license-manager-for-woocommerce' ); ?></h1>
<a href="<?php echo esc_url($addApplicationUrl); ?>" class="page-title-action">
	<span><?php echo esc_html__('Add new', 'license-manager-for-woocommerce'); ?></span>
</a>

<hr class="wp-header-end">

<form method="post">
	<?php
	$applications->prepare_items();
	$applications->display();
	?>
</form>
