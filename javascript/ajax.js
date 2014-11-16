/**
 * Content ajax script
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://github.com/zikula-modules/Content
 * @license See license.txt
 */

var content = {};

/*=[ Drag/drop page ]============================================================*/

content.addDraggablePage = function(id)
{
    var pageId = "page_" + id;
    var droppableId = "droppable_" + id;
  
    new Draggable(pageId,
    {
        handle: 'draggable',
        revert: true,
        scroll: window
    });
  
    Droppables.add(droppableId, 
    {
        hoverclass: 'hoverdrop',
        onDrop: function(dragged, dropped, event) 
        {
            Event.stop(event);
            $('contentTocDragSrcId').value = dragged.id;
            $('contentTocDragDstId').value = dropped.id;
            content.pageListDrag();
        }
     });
}

/*=[ preview a page ]============================================================*/
content.popupPreviewWindow = function(commandArgument)
{
    url = content.previewUrl.replace('__PID__', commandArgument);
    window.open(url);
}


/*=[ Select content type ]=======================================================*/
content.handleContenTypeSelected = function(id)
{
    var dropdownElement = $(id);
    var descrElement = $(id+"_descr");
    descrElement.innerHTML = contentDescriptions[dropdownElement.value];
}


/*=[ Page info ]=================================================================*/
content.pageInfo = {};
content.pageInfo.clearTimer = null;

content.pageInfo.toggle = function(id)
{
    $('contentPageInfo-'+id).toggle();
    return false;
}
content.pageInfo.mouseover = function(id)
{
    clearTimeout(content.pageInfo.clearTimer);
}
content.pageInfo.mouseout = function(id)
{
    content.pageInfo.clearTimer = setTimeout(function() { $('contentPageInfo-'+id).hide(); } , 500);
}


/**
 * activate the icon leds for active/inmenu status of pages
 *
 */
function initcontentactivationbuttons()
{
    $$('a.content_activationbutton').each(function(item) {
        item.removeClassName('content_activationbutton');
    });
}

/**
 * Toggle a page active/inactive status
 *
 *@params page id;
 *@return none;
 */
function togglepagestate(id)
{
    var pars = {
        id: id,
        active: $('active_' + id).visible()
    };
    // Zikula 14x needs index.php?module=Content&type=ajax&func=.. , but doesn't work in 13x
    new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=Content&type=ajax&func=togglePageState',
        {
            parameters: pars,
            onComplete: togglepagestate_response
        });
}

/**
 * Ajax response function for updating page status: cleanup
 *
 *@params none;
 *@return none;
 */
function togglepagestate_response(req)
{
    if (!req.isSuccess()) {
        Zikula.showajaxerror(req.getMessage());
        return;
    }
    var data = req.getData();
	
    // switch the leds and adapt the text
    $('active_' + data.id).toggle();
    $('inactive_' + data.id).toggle();
    $('activity_' + data.id).update((($('activity_' + data.id).innerHTML == Zikula.__('Inactive','module_Content')) ? Zikula.__('Active','module_Content') : Zikula.__('Inactive','module_Content')));
}

/**
 * Toggle a page inmenu status
 *
 *@params page id;
 *@return none;
 */
function togglepageinmenu(id)
{
    var pars = {
        id: id,
        inMenu:  $('inmenu_' + id).visible()
    };
    // Zikula 14x needs index.php?module=Content&type=ajax&func=.. , but doesn't work in 13x
    new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=Content&type=ajax&func=togglePageInMenu',
        {
            parameters: pars,
            onComplete: togglepageinmenu_response
        });
}

/**
 * Ajax response function for updating page inmenu status: cleanup
 *
 *@params none;
 *@return none;
 */
function togglepageinmenu_response(req)
{
    if (!req.isSuccess()) {
        Zikula.showajaxerror(req.getMessage());
        return;
    }
    var data = req.getData();

    // switch the leds and adapt the text
    $('inmenu_' + data.id).toggle();
    $('outmenu_' + data.id).toggle();
    $('menustatus_' + data.id).update((($('menustatus_' + data.id).innerHTML == Zikula.__('Out','module_Content')) ? Zikula.__('In','module_Content') : Zikula.__('Out','module_Content')));
}

/**
 * Toggle a content item active/inactive status
 *
 *@params content id;
 *@return none;
 */
function togglecontentstate(id)
{
    var pars = {
        id: id,
        active: $('activecid_' + id).visible()
    };
    // Zikula 14x needs index.php?module=Content&type=ajax&func=.. , but doesn't work in 13x
    new Zikula.Ajax.Request(
        Zikula.Config.baseURL + 'ajax.php?module=Content&type=ajax&func=toggleContentState',
        {
            parameters: pars,
            onComplete: togglecontentstate_response
        });
}

/**
 * Ajax response function for updating content item status: cleanup
 *
 *@params none;
 *@return none;
 */
function togglecontentstate_response(req)
{
    if (!req.isSuccess()) {
        Zikula.showajaxerror(req.getMessage());
        return;
    }
    var data = req.getData();
	
    // switch the leds and adapt the text
    $('activecid_' + data.id).toggle();
    $('inactivecid_' + data.id).toggle();
    $('activitycid_' + data.id).update((($('activitycid_' + data.id).innerHTML == Zikula.__('Inactive','module_Content')) ? '' : Zikula.__('Inactive','module_Content')));
    
	// toggle the content item to inactive slowly, effect is using jQuery UI
	jQuery("#content-item-" + data.id).toggleClass('content-item-inactive', 500);

	// toggle the widget class to inactive
	//$('content_widget_' + data.id).toggleClassName('widget_inactive');
}

