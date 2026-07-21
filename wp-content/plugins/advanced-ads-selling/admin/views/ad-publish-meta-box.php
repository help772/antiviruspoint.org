<?php
/**
 * The plugin bootstrap.
 *
 * @package AdvancedAds\SellingAds
 * @author  Advanced Ads <info@wpadvancedads.com>
 *
 * @var string $warning   any eventual warning message.
 * @var bool   $do_import The item is ready to be launched.
 * @var string $slug      The item is ready to be launched.
 */

?>
<div style="margin: 5px 0; padding: 5px; background-color: #ffdede;">
	<?php if ( $warning ) : ?>
		<?php echo wp_kses_post( $warning ); ?>
	<?php endif; ?>
	<?php if ( $do_import ) : ?>
		<input type="hidden" name="advads-selling-add-to-placement" value="<?php echo esc_attr( $slug ); ?>">
	<?php endif; ?>
</div>
