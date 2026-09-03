/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

'use strict';

/**
 * Legacy jQuery datepicker setup for the requested delivery date.
 *
 * @deprecated Superseded by `DatePickerType` and the Gui DateTimePicker, which are configured
 *   declaratively. Kept only for installations running spryker/gui older than 5.4.0.
 */
function initLegacyDeliveryDatePicker($inputDate) {
    $inputDate
        .datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            numberOfMonths: 3,
            minDate: 0,
            defaultData: 0,
        })
        .keyup(function (event) {
            var backspaceButton = 8;
            var deleteButton = 46;

            if (event.keyCode === backspaceButton || event.keyCode === deleteButton) {
                $.datepicker._clearDate(this);
            }
        });
}

module.exports = function (trigger, target, inputDate) {
    var $inputDate = $(inputDate);

    // From spryker/gui 5.4.0 on, this field is built with `DatePickerType`, which marks it with
    // `data-spryker-picker` and lets the Gui DateTimePicker initialize it. Older Gui versions have
    // no such type, so the legacy picker above is set up instead.
    if (!$inputDate.is('[data-spryker-picker]')) {
        initLegacyDeliveryDatePicker($inputDate);
    }

    function toggleForm() {
        var selectedOptionValue = $(trigger).val();

        if (!selectedOptionValue) {
            $(target).show();

            return;
        }

        $(target).hide();
    }

    function setDisableFields() {
        var selectedOptionValue = $(trigger).val();
        var $requiredFields = $(target).find('select[required], input[required]');

        $requiredFields.each(function () {
            $(this).attr('disabled', !!selectedOptionValue);
        });
    }

    function init() {
        toggleForm();
        setDisableFields();
    }

    init();

    $(trigger).on('change', init);
};
