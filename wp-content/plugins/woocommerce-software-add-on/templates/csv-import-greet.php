<div>
	<p><?php _e( 'Hi there! Upload a CSV file containing license keys to import the licenses into your shop.', 'woocommerce-software-add-on' ); ?></p>
	<p><?php _e( 'Choose a CSV (.csv) file to upload, then click Upload file and import.', 'woocommerce-software-add-on' ); ?></p>

	<?php wp_import_upload_form( 'admin.php?import=woocommerce_software_keys_csv&amp;step=1' ); ?>
</div>
