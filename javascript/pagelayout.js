/**
 * create the onload function to enable the respective functions
 *
 */
Event.observe(window, 'load', content_layoutpreview_init);

function content_layoutpreview_init()
{
    if ($('layout') && $('layout_preview_img') && $('layout_preview_desc')) {
        Event.observe('layout', 'change', content_layoutpreview_onchange);
    }
}
function content_layoutpreview_onchange()
{
    if (images && descs) {
        // change the image preview and description now.
        $('layout_preview_img').src = images[$('layout').selectedIndex];
        $('layout_preview_desc').update(descs[$('layout').selectedIndex]);
    }
}

// jQuery function called in page editing when a content item is moved
function updateContentItemPosition(event, ui) {
    // get the dragged div id
    var draggedCidDomId = ui.item[0].id;
    var draggedCidVal = jQuery(draggedCidDomId.split("-")).get(-1);

    // get all contentareas with content items in there and do matching in PHP
    var contentAreas = new Array();
    jQuery(".content-column-portlet").each(function(index) {
        contentAreas[index] = jQuery(this).sortable("toArray");
    });
    //console.log(contentAreas.toSource());

    // Make the Ajax call to store the new position
    jQuery.ajax({
        type: "POST",
        data: {
            pid: content.pageId,
            cid: draggedCidVal,
            cidDOM: draggedCidDomId,
            contentAreas: contentAreas
        },
        url: Zikula.Config.baseURL + "ajax.php?module=Content&type=ajax&func=dragContent",
            success: function(result) {
                // do nothing, succeeded
            },
            error: function(result) {
                alert(Zikula.__('Error during content item move','module_Content'));
                return;
            }
    });
}