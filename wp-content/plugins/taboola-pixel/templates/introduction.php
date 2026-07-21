<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<div class="taboola-pixel-plugin">
    <div class="installation-form-wrapper">
        <div class="installation-form">
            <div class="header">
                <div class="taboola-banner" style="background-image: url('<?php echo esc_url(plugin_dir_url(__DIR__ . '/../../') . 'assets/images/banner.jpg'); ?>');" aria-label="Taboola banner"></div>
            </div>
            <div class="installation-form-body">
                <!-- Step 1: Connect -->
                <div id="step-connect" class="step-container">
                    <div class="content-section">
                        <div class="title">
                            <h2>Supercharge your campaigns with Smart Tracking</h2>
                        </div>
                        <div class="form">
                            <p>Click "Connect" and follow our step-by-step guide.</p>
                            <button id="select_account_button" class="button button-secondary">Connect Realize Account
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Step 2: Install Pixel -->
                <div id="install-pixel-step" class="step-container">
                    <div class="content-section">
                        <div class="title">
                            <h2>1. Install Realize Pixel App</h2>
                        </div>
                        <div class="install-pixel-component">
                            <div class="account-info-box">
                            <span>
                                <img src="<?php echo esc_js(plugin_dir_url(__DIR__ . '/../../')) . 'assets/svg/globe.svg' ?>"
                                     width="24" height="24" alt="Account"/>
                            </span>
                                <div class="account-details">
                                    <div class="account-label" id="account-label"></div>
                                    <div class="account-id" id="account-id"></div>
                                </div>
                            </div>
                            <button id="install_pixel_button" class="taboola-pixel-button">Install Pixel</button>
                        </div>
                    </div>
                </div>
                <!-- Step 3: Success -->
                <div id="step-success" class="step-container">
                    <div class="content-section">
                        <div class="title">
                            <h2>1. Install Realize Pixel App</h2>
                            <span>
                                <img src="<?php echo esc_js(plugin_dir_url(__DIR__ . '/../../')) . 'assets/svg/circle.svg' ?>" width="24" height="24" alt="Success" />
                            </span>
                        </div>
                        <div class="install-pixel-component">
                            <div class="account-info-box">
                                <span>
                                    <img src="<?php echo esc_js(plugin_dir_url(__DIR__ . '/../../')) . 'assets/svg/globe.svg' ?>" width="24" height="24" alt="Account" />
                                </span>
                                <div class="account-details">
                                    <div class="account-label" id="account-label-success"></div>
                                    <div class="account-id" id="account-id-success"></div>
                                </div>
                            </div>
                            <button id="change_account_button" class="taboola-pixel-button">Change Account</button>
                        </div>
                        <div class="notice-success">
                            <img src="<?php echo esc_js(plugin_dir_url(__DIR__ . '/../../')) . 'assets/svg/checkmark.svg' ?>" width="24" height="24" alt="Success" />
                            <span>Your Realize Pixel Is Ready!</span>
                        </div>
                    </div>
                    <div class="content-section">
                        <div class="setup-process-box">
                            <div class="title"><h2>2. Finish the setup process in Realize</h2></div>
                            <div class="step-content">Create your conversions using our <a id="codeless-link" href="https://ads.realizeperformance.com/tracking/codeless-event-tool/new?accountId=ACCOUNT_ID_PLACEHOLDER" target="_blank">codeless solution</a>.</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Hidden form for settings submission -->
            <form id="taboola_pixel_form" method="post" action="options.php" style="display:none;">
                <div class="input-wrapper">
                    <label for="tbla_account_id"></label>
                    <input type="text" id="tbla_account_id" name="taboola_pixel_settings[account_id]"
                           value="<?php echo esc_attr($tabpx_account_id); ?>" <?php if ($is_pixel_installed) echo "disabled" ?> />
                    <input type="hidden" id="tbla_account_id_action" name="action"
                           value="<?php echo $is_pixel_installed ? "tabpx_uninstall_account_id" : "tabpx_install_account_id" ?>">
                    <?php
                    settings_fields('taboola_pixel_settings_group');
                    do_settings_sections('taboola-pixel-plugin');
                    ?>
                </div>
                <button type="submit" id="submit_tbla_account_id"></button>
            </form>
        </div>
    </div>
</div>
