<?php
$preset = YITH_WCAN_Presets_Factory::get_presets( ['slug'=>'default-search-filter-preset']);
$slug_preset = $preset ? $preset[0]->get_slug() : '';
$preset_order = YITH_WCAN_Presets_Factory::get_presets( ['slug'=>'default-search-filter-orderby-preset']);
$slug_order_preset = $preset_order ? $preset_order[0]->get_slug() : '';
?>
<!-- wp:yith/overlay-search-block {"metadata":{"categories":["yith-patterns"],"patternName":"yith/search-filters","name":"Search \u0026 Filters"}} -->
<div class="wp-block-yith-overlay-search-block is-loading"><!-- wp:yith/icon-trigger-block -->
    <div class="wp-block-yith-icon-trigger-block"></div>
    <!-- /wp:yith/icon-trigger-block -->

    <!-- wp:yith/overlay-block -->
    <div class="wp-block-yith-overlay-block is-loading"><!-- wp:yith/overlay-input-block -->
        <div class="wp-block-yith-overlay-input-block"></div>
        <!-- /wp:yith/overlay-input-block -->

        <!-- wp:yith/overlay-filled-block -->
        <div class="wp-block-yith-overlay-filled-block"><!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column {"width":"100.23%","className":"ywcas-order-by-column"} -->
                <div class="wp-block-column ywcas-order-by-column" style="flex-basis:100.23%"><!-- wp:yith/yith-wcan-ajax-filters-preset {"slug":"<?php echo esc_attr( $slug_order_preset ); ?>"} -->
                    [yith_wcan_filters slug="<?php echo esc_attr( $slug_order_preset ); ?>"]
                    <!-- /wp:yith/yith-wcan-ajax-filters-preset -->

                    <!-- wp:columns -->
                    <div class="wp-block-columns"><!-- wp:column {"width":"20%"} -->
                        <div class="wp-block-column" style="flex-basis:20%"><!-- wp:yith/yith-wcan-ajax-filters-preset {"slug":"<?php echo esc_attr( $slug_preset ); ?>"} -->
                            [yith_wcan_filters slug="<?php echo esc_attr( $slug_preset ); ?>"]
                            <!-- /wp:yith/yith-wcan-ajax-filters-preset --></div>
                        <!-- /wp:column -->

                        <!-- wp:column {"width":"80%"} -->
                        <div class="wp-block-column" style="flex-basis:80%"><!-- wp:yith/overlay-product-results-block {"rows":4} -->
                            <div class="wp-block-yith-overlay-product-results-block"><!-- wp:yith/overlay-grid-block -->
                                <div class="wp-block-yith-overlay-grid-block"></div>
                                <!-- /wp:yith/overlay-grid-block -->

                                <!-- wp:yith/overlay-pagination-block -->
                                <div class="wp-block-yith-overlay-pagination-block"></div>
                                <!-- /wp:yith/overlay-pagination-block --></div>
                            <!-- /wp:yith/overlay-product-results-block --></div>
                        <!-- /wp:column --></div>
                    <!-- /wp:columns --></div>
                <!-- /wp:column --></div>
            <!-- /wp:columns --></div>
        <!-- /wp:yith/overlay-filled-block -->

        <!-- wp:yith/overlay-empty-block -->
        <div class="wp-block-yith-overlay-empty-block"><!-- wp:columns -->
            <div class="wp-block-columns"><!-- wp:column {"width":"20%"} -->
                <div class="wp-block-column" style="flex-basis:20%"><!-- wp:yith/overlay-history-block -->
                    <div class="wp-block-yith-overlay-history-block"></div>
                    <!-- /wp:yith/overlay-history-block -->

                    <!-- wp:yith/overlay-popular-block -->
                    <div class="wp-block-yith-overlay-popular-block"></div>
                    <!-- /wp:yith/overlay-popular-block --></div>
                <!-- /wp:column -->

                <!-- wp:column {"width":"80%"} -->
                <div class="wp-block-column" style="flex-basis:80%"><!-- wp:heading {"fontSize":"medium"} -->
                    <h2 class="wp-block-heading has-medium-font-size">Popular products</h2>
                    <!-- /wp:heading -->

                    <!-- wp:woocommerce/product-best-sellers {"columns":4,"rows":1} /--></div>
                <!-- /wp:column --></div>
            <!-- /wp:columns --></div>
        <!-- /wp:yith/overlay-empty-block --></div>
    <!-- /wp:yith/overlay-block --></div>
<!-- /wp:yith/overlay-search-block -->