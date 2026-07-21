function logEvent(eventName, eventData) {
  console.log(eventName, eventData);
}

jQuery(document).ready(function ($) {
  $('#submit_tbla_account_id').on('click', function (e) {
    e.preventDefault();
    logEvent('Clicked Install Pixel');
    var accountID = $('#tbla_account_id').val();
    var action = $('#tbla_account_id_action').val();
    var nonce = taboolaPixelAjax.nonce;

    $.ajax({
      type: 'POST',
      url: taboolaPixelAjax.ajax_url,
      data: {
        action,
        account_id: accountID,
        nonce: nonce
      },
      success: function (response) {
        location.reload();
      }
    });
  });

  $('#select_account_button').on('click', function (e) {
    e.preventDefault();
    logEvent('Clicked Connect Account');

    // Open in a popup window with specific dimensions and no URL bar
    const width = 580;
    const height = 800;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;

    let thirdPartyConnectHost = localStorage.getItem('thirdPartyConnectHost');
    if (!thirdPartyConnectHost || thirdPartyConnectHost === 'null') {
      thirdPartyConnectHost = taboolaPixelAjax.third_party_connect_host;
    }

    // Combine all possible parameters to hide chrome/UI elements
    const windowFeatures =
      'width=' + width +
      ',height=' + height +
      ',left=' + left +
      ',top=' + top +
      ',resizable=yes' +
      ',scrollbars=yes' +
      ',status=no' +
      ',location=no' +
      ',menubar=no' +
      ',toolbar=no' +
      ',personalbar=no' +
      ',directories=no' +
      ',titlebar=no' +
      ',chrome=no' +
      ',dependent=yes' +
      ',minimizable=no' +
      ',fullscreen=no';

    const popupUrl = thirdPartyConnectHost + '3p-connect?platform=' + taboolaPixelAjax.platform + '&referrer=' + encodeURIComponent(window.location.href);

    // Use loading.html to bypass popup blockers
    // It will wait 100ms and then redirect to the actual URL
    const loadingUrl = taboolaPixelAjax.loading_url + '?url=' + encodeURIComponent(popupUrl);
    const popup = window.open(loadingUrl, 'Taboola Connect', windowFeatures);

    if (popup) {
      logEvent('3p Connect Started');
    }

    window.addEventListener('message', function handler(event) {
      // Check origin for security
      if (event.origin !== thirdPartyConnectHost.replace(/\/$/, '')) return;

      const data = event.data;
      if (data.type === 'ACCOUNT_SELECTED') {
        // Store sensitive details in localStorage
        localStorage.setItem('taboolaAccountId', data.accountId);
        localStorage.setItem('taboolaAccountName', data.accountName);
        localStorage.setItem('taboolaAccountLabel', data.accountLabel);
        localStorage.setItem('taboolaToken', data.profiledToken);

        // Hide the original step and show the install pixel step
        $('#step-connect').hide();
        $('#install-pixel-step').show();

        // Populate the account info box
        $('#account-label').text(data.accountLabel || data.accountName || '');
        $('#account-id').text('Account ID: ' + (data.accountId || ''));

        // Remove any previous click handlers to avoid duplicates
        $('#install_pixel_button').off('click').on('click', async function () {
          if (taboolaPixelAjax.platform === 'wc') {
            // Make the API call
            try {
              await fetch(taboolaPixelAjax.api_host + 'rule/create-ecomm?platformName=Woocommerce&accountId=' + encodeURIComponent(data.accountName), {
                headers: {
                  'Authorization': 'Bearer ' + data.profiledToken,
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                method: 'POST'
              });
              logEvent('Ecommerce Rules Created');
            } catch (error) {
              // No error handling for now, continue anyway
              console.error('Error creating rules:', error);
              logEvent('Failed Creating Ecommerce Rules', {error: error.message});
            }
          }
          // Update the hidden input field with the account ID
          $('#tbla_account_id').val(data.accountId);
          // Update codeless link
          updateCodelessLink(data.accountId);
          // Submit the form to save the account ID
          $('#submit_tbla_account_id').click();
        });

        // Remove this event listener after handling
        window.removeEventListener('message', handler);
      }
    });
  });

  // On page load, if pixel is already installed, show only the success step
  if ($('#tbla_account_id').val()) {
    $('#step-connect').hide();
    $('#install-pixel-step').hide();
    $('#step-success').show();
  }

  // Populate account info in the success step if present
  if ($('#tbla_account_id').val()) {
    var label = localStorage.getItem('taboolaAccountLabel') || localStorage.getItem('taboolaAccountName') || '';
    var id = $('#tbla_account_id').val() || localStorage.getItem('taboolaAccountId') || '';
    $('#account-label-success').text(label);
    $('#account-id-success').text('Account ID: ' + id);
  }

  // Change Account button handler
  $('#change_account_button').on('click', function (e) {
    e.preventDefault();
    if (!confirm('Are you sure you want to change the account? This will disconnect your current account.')) {
      return;
    }
    logEvent('Clicked Change Account');
    $.post(taboolaPixelAjax.ajax_url, {
      action: 'tabpx_uninstall_account_id',
      nonce: taboolaPixelAjax.nonce
    }, function (response) {
      // Clear localStorage
      localStorage.removeItem('taboolaAccountId');
      localStorage.removeItem('taboolaAccountName');
      localStorage.removeItem('taboolaAccountLabel');
      localStorage.removeItem('taboolaToken');
      // Refresh the page to sync state
      location.reload();
    });
  });

  function updateCodelessLink(accountId) {
    $('#codeless-link').each(function () {
      var href = $(this).attr('href');
      if (href && href.indexOf('ACCOUNT_ID_PLACEHOLDER') !== -1) {
        $(this).attr('href', href.replace('ACCOUNT_ID_PLACEHOLDER', encodeURIComponent(accountId)));
      }
    });
  }

  // On page load, or after install/account select:
  var accountId = $('#tbla_account_id').val() || localStorage.getItem('taboolaAccountId');
  if (accountId) {
    updateCodelessLink(accountId);
  }
});
