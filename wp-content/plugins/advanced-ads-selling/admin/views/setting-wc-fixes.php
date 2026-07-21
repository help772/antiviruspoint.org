<label>
    <input name="<?php echo esc_attr( Advanced_Ads_Selling_Plugin::OPTION_KEY ); ?>[wc-fixes]" type="checkbox" value="1" <?php checked( 1, $wc_fixes ); ?>/>
    <?php esc_html_e( 'Applies the following changes to WooCommerce. Especially useful, when you don’t use WooCommerce for anything else than ads.', 'advanced-ads-selling' ); ?>
    <p><?php _e( 'Remove product images', 'advanced-ads-selling' ); ?></p>
</label>