jQuery(document).ready(function($) {
    // Store all available countries
    let availableCountries = {};
    let debounceTimer;
    
    // Initialize available countries
    $('.wc-avatax-country-select option').each(function() {
        const $option = $(this);
        const value = $option.val();
        if (value) {
            const text = ($option.text() || '').trim() || value;
            availableCountries[value] = {
                name: value,
                text: text
            };
        }
    });

    // Validation function
    function validateHsCode($input) {
        const value = $input.val().trim();
        const $message = $input.siblings('.validation-message');
        
        if (!value) {
            $input.addClass('hs-code-invalid');
            $message.text('HS code cannot be empty');
            return false;
        } else {
            $input.removeClass('hs-code-invalid');
            $message.text('');
            return true;
        }
    }

    // Auto-save function
    function autoSave($input) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            if (validateHsCode($input)) {
                const $field = $input.closest('.hs-code-field');
                $input.prop('disabled', true);
                $field.find('.edit-hs-code').show();
            }
        }, 1000); // 1 second delay
    }

    // Handle Add HS Code button click
    $('#wc_avatax_add_hs_code').on('click', function() {
        const $select = $('.wc-avatax-country-select');
        const country = $select.val();
        
        if (country && availableCountries[country]) {
            const fieldName = "_wc_avatax_hs_" + country;
            const countryData = availableCountries[country];
            const labelText = countryData.text;

            // Check if already exists (getElementById uses literal id, not a CSS selector string)
            if (document.getElementById(fieldName)) {
                alert('HS code for this country already exists');
                return;
            }

            // Build DOM with .attr() / .text() — no HTML string interpolation (mitigates XSS; CWE-79)
            const $field = $('<div/>', {class: 'hs-code-field'})
                .attr('data-country', country)
                .attr('data-name', country);

            $('<label/>', {for: fieldName}).text(labelText).appendTo($field);

            $('<input/>', {
                type: 'text',
                class: 'short required-hs-code',
                name: fieldName,
                id: fieldName,
                placeholder: 'Enter HS code',
                required: true,
                style: 'width: 150px;'
            }).val('').appendTo($field);

            $('<button/>', {type: 'button', class: 'button edit-hs-code'})
                .css('display', 'none')
                .text('Edit')
                .appendTo($field);

            $('<button/>', {type: 'button', class: 'button remove-hs-code'})
                .text('Remove')
                .appendTo($field);

            $('<span/>', {class: 'validation-message'}).appendTo($field);

            $('.hs-codes-container').append($field);
            
            // Focus on the new input
            const $input = $field.find('input');
            $input.focus();

            // Remove matching option without embedding value in selector string
            $select.find('option').filter(function () {
                return $(this).val() === country;
            }).remove();
            delete availableCountries[country];
            
            // Reset select
            $select.val('');

            // Add auto-save handler to new input
            $input.on('input', function() {
                autoSave($(this));
            });
        }
    });

    // Handle edit button click
    $(document).on('click', '.edit-hs-code', function() {
        const $field = $(this).closest('.hs-code-field');
        const $input = $field.find('input');
        const $editBtn = $(this);
        
        $input.prop('disabled', false).focus();
        $editBtn.hide();

        // Add auto-save handler
        $input.on('input', function() {
            autoSave($(this));
        });
    });

    // Handle remove button click
    $(document).on('click', '.remove-hs-code', function() {
        const $field = $(this).closest('.hs-code-field');
        const country = $field.data('country');
        let countryName = $field.data('name');
        if (!countryName || countryName === 'undefined') {
            countryName = country;
        }
        // Add back to dropdown
        if (country) {
            const $option = $('<option/>').val(country).text(countryName);
            $('.wc-avatax-country-select').append($option);
            availableCountries[country] = {
                name: country,
                text: countryName
            };
        }
        
        // Remove field
        $field.remove();
    });

    // Handle form submission
    $('form#post').on('submit', function(e) {
        let isValid = true;
        let hsCodeCountries = {};

        // Validate and collect all HS codes
        $('.hs-code-field').each(function() {
            const $field = $(this);
            const $input = $field.find('.required-hs-code');
            const country = $field.data('country');
            const value = $input.val().trim();

            if (!validateHsCode($input)) {
                isValid = false;
            } else {
                hsCodeCountries[country] = value;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all HS codes');
            return false;
        }

        // Add hidden field to store all HS codes
        $('<input>').attr({
            type: 'hidden',
            name: '_wc_avatax_hs_countries',
            value: JSON.stringify(hsCodeCountries)
        }).appendTo($(this));
    });

    // Add auto-save handler to existing inputs
    $('.required-hs-code').on('input', function() {
        autoSave($(this));
    });
});
