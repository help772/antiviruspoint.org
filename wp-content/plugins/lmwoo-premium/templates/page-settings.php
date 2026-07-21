<?php

defined('ABSPATH') || exit;

/**
 * Available variables
 *
 * @var string $section
 * @var string $urlGeneral
 * @var string $urlWooCommerce
 * @var string $urlRestApi
 * @var string $urlTools
 */

?>

<div class="wrap lmfwc">

	<?php settings_errors(); ?>
	<ul class="subsubsub"><li><a href="<?php echo esc_url($urlGeneral); ?>" class="<?php echo 'general' === $section   ? 'current' : ''; ?>">
		<span><?php esc_html_e('General', 'license-manager-for-woocommerce'); ?></span>
	</a> | </li><li><a href="<?php echo esc_url($urlWooCommerce); ?>" class="<?php echo 'woocommerce' === $section   ? 'current' : ''; ?>">
		<span><?php esc_html_e('WooCommerce', 'license-manager-for-woocommerce'); ?></span>
	</a> | </li><li><a href="<?php echo esc_url($urlRestApi); ?>" class="<?php echo 'rest_api' === $section   ? 'current' : ''; ?>">
		<span><?php esc_html_e('REST API', 'license-manager-for-woocommerce'); ?></span>
	</a> | </li><li><a href="<?php echo esc_url($urlTools); ?>" class="<?php echo 'tools' ===  $section ? 'current' : ''; ?>">
		<span><?php esc_html_e('Tools', 'license-manager-for-woocommerce'); ?></span>
	</a>  </li></ul>
	<br class="clear">

	<?php if ( 'general' == $section  ) : ?>

		<form action="<?php echo esc_url( admin_url('options.php') ); ?>" method="POST">
			<?php settings_fields('lmfwc_settings_group_general'); ?>
			<?php do_settings_sections('lmfwc_license_keys'); ?>
			<?php do_settings_sections('lmfwc_grace_period'); ?>
			<?php do_settings_sections('lmfwc_rest_api'); ?>
			<?php do_settings_sections('lmfwc_api_logs'); ?>
			<?php submit_button(); ?>
		</form>

	<?php elseif ( 'woocommerce' === $section  ) : ?>

		<form action="<?php echo esc_url( admin_url('options.php') ); ?>" method="POST">
			<?php settings_fields('lmfwc_settings_group_woocommerce'); ?>
			<?php do_settings_sections('lmfwc_license_key_delivery'); ?>
			<?php do_settings_sections('lmfwc_branding'); ?>
			<?php do_settings_sections('lmfwc_my_account'); ?>
			<?php submit_button(); ?>
		</form>

	<?php elseif ( 'rest_api' === $section  ) : ?>

		<?php if ('list' === $action  ) : ?>

			<?php include_once 'settings/rest-api-list.php'; ?>

		<?php elseif ( 'show' === $action  ) : ?>

			<?php include_once 'settings/rest-api-show.php'; ?>

		<?php else : ?>

			<?php include_once 'settings/rest-api-key.php'; ?>

		<?php endif; ?>

	<?php elseif ( 'tools'  === $section ) : ?>

		<form action="<?php echo esc_url( admin_url('options.php') ); ?>" method="POST">
			<?php settings_fields('lmfwc_settings_group_tools'); ?>
			<?php do_settings_sections('lmfwc_export'); ?>
			<?php submit_button(); ?>
		</form>

		 <?php include_once 'settings/data-tools.php'; ?>


	<?php endif; ?>

</div>