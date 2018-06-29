'use strict';

function zikulaContentToday(format) {
    var timestamp, todayDate, month, day, hours, minutes, seconds;

    timestamp = new Date();
    todayDate = '';
    if (format !== 'time') {
        month = new String((parseInt(timestamp.getMonth()) + 1));
        if (month.length === 1) {
            month = '0' + month;
        }
        day = new String(timestamp.getDate());
        if (day.length === 1) {
            day = '0' + day;
        }
        todayDate += timestamp.getFullYear() + '-' + month + '-' + day;
    }
    if (format === 'datetime') {
        todayDate += ' ';
    }
    if (format != 'date') {
        hours = new String(timestamp.getHours());
        if (hours.length === 1) {
            hours = '0' + hours;
        }
        minutes = new String(timestamp.getMinutes());
        if (minutes.length === 1) {
            minutes = '0' + minutes;
        }
        seconds = new String(timestamp.getSeconds());
        if (seconds.length === 1) {
            seconds = '0' + seconds;
        }
        todayDate += hours + ':' + minutes;// + ':' + seconds;
    }

    return todayDate;
}

// returns YYYY-MM-DD even if date is in DD.MM.YYYY
function zikulaContentReadDate(val, includeTime) {
    // look if we have YYYY-MM-DD
    if (val.substr(4, 1) === '-' && val.substr(7, 1) === '-') {
        return val;
    }

    // look if we have DD.MM.YYYY
    if (val.substr(2, 1) === '.' && val.substr(5, 1) === '.') {
        var newVal = val.substr(6, 4) + '-' + val.substr(3, 2) + '-' + val.substr(0, 2);
        if (true === includeTime) {
            newVal += ' ' + val.substr(11, 7);
        }

        return newVal;
    }
}

function zikulaContentValidateNoSpace(val) {
    var valStr;
    valStr = new String(val);

    return (valStr.indexOf(' ') === -1);
}

function zikulaContentValidateDateRangePage(val) {
    var cmpVal, cmpVal2, result;

    cmpVal = jQuery("[id$='activeFrom_date']").val() + ' ' + jQuery("[id$='activeFrom_time']").val();
    cmpVal2 = jQuery("[id$='activeTo_date']").val() + ' ' + jQuery("[id$='activeTo_time']").val();

    if (typeof cmpVal == 'undefined' && typeof cmpVal2 == 'undefined') {
        result = true;
    } else if ('' == jQuery.trim(cmpVal) || '' == jQuery.trim(cmpVal2)) {
        result = true;
    } else {
        result = (cmpVal <= cmpVal2);
    }

    return result;
}

function zikulaContentValidateDateRangeContentItem(val) {
    var cmpVal, cmpVal2, result;

    cmpVal = jQuery("[id$='activeFrom_date']").val() + ' ' + jQuery("[id$='activeFrom_time']").val();
    cmpVal2 = jQuery("[id$='activeTo_date']").val() + ' ' + jQuery("[id$='activeTo_time']").val();

    if (typeof cmpVal == 'undefined' && typeof cmpVal2 == 'undefined') {
        result = true;
    } else if ('' == jQuery.trim(cmpVal) || '' == jQuery.trim(cmpVal2)) {
        result = true;
    } else {
        result = (cmpVal <= cmpVal2);
    }

    return result;
}

/**
 * Runs special validation rules.
 */
function zikulaContentExecuteCustomValidationConstraints(objectType, currentEntityId) {
    jQuery('.validate-daterange-page').each(function () {
        if ('undefined' != typeof jQuery(this).attr('id')) {
            if ('DIV' == jQuery(this).prop('tagName')) {
                if (!zikulaContentValidateDateRangePage()) {
                    document.getElementById(jQuery(this).attr('id') + '_date').setCustomValidity(Translator.__('The start must be before the end.'));
                    document.getElementById(jQuery(this).attr('id') + '_time').setCustomValidity(Translator.__('The start must be before the end.'));
                } else {
                    document.getElementById(jQuery(this).attr('id') + '_date').setCustomValidity('');
                    document.getElementById(jQuery(this).attr('id') + '_time').setCustomValidity('');
                }
            } else {
                if (!zikulaContentValidateDateRangePage()) {
                    jQuery(this).get(0).setCustomValidity(Translator.__('The start must be before the end.'));
                } else {
                    jQuery(this).get(0).setCustomValidity('');
                }
            }
        }
    });
    jQuery('.validate-daterange-contentitem').each(function () {
        if ('undefined' != typeof jQuery(this).attr('id')) {
            if ('DIV' == jQuery(this).prop('tagName')) {
                if (!zikulaContentValidateDateRangeContentItem()) {
                    document.getElementById(jQuery(this).attr('id') + '_date').setCustomValidity(Translator.__('The start must be before the end.'));
                    document.getElementById(jQuery(this).attr('id') + '_time').setCustomValidity(Translator.__('The start must be before the end.'));
                } else {
                    document.getElementById(jQuery(this).attr('id') + '_date').setCustomValidity('');
                    document.getElementById(jQuery(this).attr('id') + '_time').setCustomValidity('');
                }
            } else {
                if (!zikulaContentValidateDateRangeContentItem()) {
                    jQuery(this).get(0).setCustomValidity(Translator.__('The start must be before the end.'));
                } else {
                    jQuery(this).get(0).setCustomValidity('');
                }
            }
        }
    });
}
