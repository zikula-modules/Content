'use strict';

function updateVersionSelectionState() {
    var amountOfSelectedVersions;

    amountOfSelectedVersions = jQuery('.zikulacontent-toggle-checkbox:checked').length;
    if (2 < amountOfSelectedVersions) {
        jQuery(this).prop('checked', false);
        amountOfSelectedVersions--;
    }
    jQuery('#compareButton').prop('disabled', 2 != amountOfSelectedVersions);
}

jQuery(document).ready(function () {
    jQuery('.zikulacontent-toggle-checkbox').click(updateVersionSelectionState);
    updateVersionSelectionState();
});
