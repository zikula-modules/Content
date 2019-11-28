'use strict';

function updateVersionSelectionState() {
    var amountOfSelectedVersions;

    amountOfSelectedVersions = jQuery('.zikulacontent-toggle-checkbox:checked').length;
    if (amountOfSelectedVersions > 2) {
        jQuery(this).prop('checked', false);
        amountOfSelectedVersions--;
    }
    jQuery('#compareButton').prop('disabled', amountOfSelectedVersions != 2);
}

jQuery(document).ready(function () {
    jQuery('.zikulacontent-toggle-checkbox').click(updateVersionSelectionState);
    updateVersionSelectionState();
});
