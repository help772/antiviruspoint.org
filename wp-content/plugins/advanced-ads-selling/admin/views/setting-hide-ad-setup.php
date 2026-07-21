<label>
    <input name="<?php echo esc_attr( Advanced_Ads_Selling_Plugin::OPTION_KEY ); ?>[hide-ad-setup]" type="checkbox" value="1" <?php checked( 1, $hide_ad_setup ); ?>/>
    <?php esc_html_e( 'Hide the link to the public ad setup page from the client and don‘t send out emails about the ad setup process.', 'advanced-ads-selling' ); ?>
</label>