/**
 * Content ajax script
 *
 * @copyright (C) 2007-2010, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id: pnuser.php,v 1.21 2007/09/04 15:15:22 jornlind Exp $
 * @license See license.txt
 */


var content = {};

/*=[ Drag/drop page ]============================================================*/

content.tocAddPage = function(id)
{
  var pageId = "page_" + id;
  var anchorId = "anchor_" + id;

  new Draggable(pageId,
  {
    handle: "dragable"
  });

  Droppables.add(anchorId, 
    {
      onDrop: function(drag, drop, evt) 
      {
        Event.stop(evt);
        $('contentTocDragSrcId').value = drag.id;
        $('contentTocDragDstId').value = drop.id;
        content.tocPageDrag();
      },
      hoverclass: "hover"
    });
}


/*=[ Drag/drop content ]=========================================================*/

content.items = new Array();

content.editPageOnLoad = function()
{
  // get "columns" in correct order
  columns = $$("table.content-layout-edit td").sort(function(el1,el2) { return el1.id >= el2.id; });

  // Get new portal object
  content.portal = new Xilinus.Portal(columns, { onUpdate: content.editPageHandleUpdate }) 

  // Iterate through the registered content elements and add them to the portal
  content.items.each(
    function(e) 
    { 
      // Remove XXX's added to avoid bad short URL handling in PN's short URL output filter
      e.title = e.title.replace(/ srcXXX=/g, ' src=');
      e.title = e.title.replace(/ hrefXXX=/g, ' href=');
      e.content = e.content.replace(/ srcXXX=/g, ' src=');
      e.content = e.content.replace(/ hrefXXX=/g, ' href=');

      var widget = new Xilinus.Widget("widget", "widget_"+e.contentId).setTitle(e.title).setContent(e.content).setActive(e.active);
      content.portal.add(widget, e.column); 
    }
  );
}


// Called by portal when a content item has been moved
content.editPageHandleUpdate = function(portal, widget)
{
  var contentArea = widget.parentNode;

  // Figure out position inside this content item
  var position = 0;
  for (var i=0; i<contentArea.childNodes.length; ++i)
  {
    // Found the passed widget?
    if (contentArea.childNodes[i] == widget)
      break;

    // Found any widget?
    if (contentArea.childNodes[i].nodeType == 1 && contentArea.childNodes[i].className == 'widget')
      ++position;
  }

  // Convert "widget_col_X" to X
  var contentAreaIndex = contentArea.id.substr(11);

  // Convert "widget_X" to X
  var contentId = widget.id.substr(7);

  //alert("New position for " + contentId + " = (" + contentAreaIndex + "," + position + ")");

  // Start AJAX request
  var pars = "pid=" + content.pageId + "&cid=" + contentId + "&cai=" + contentAreaIndex + "&pos=" + position;
  var url = "ajax.php?module=content&func=dragcontent";

  new Ajax.Request(url, { method: "post", 
                          parameters: pars, 
                          onSuccess: function(response) { content.handleDragContentOk(response); },
                          onFailure: content.handleAjaxError});
}


content.handleDragContentOk = function(response)
{
  var result = pndejsonize(response.responseText);
  if (!result.ok)
    alert(result.message);
}


content.handleAjaxError = function(response)
{
  alert(response.responseText);
}

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


content.pageInfo.mouseover = function()
{
  clearTimeout(content.pageInfo.clearTimer);
}


content.pageInfo.mouseout = function()
{
  content.pageInfo.clearTimer = setTimeout(function() { $('contentPageInfo').hide(); } , 500);
}


/**
 * activate all buttons to (de-)activate blocks
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
 *@author Erik Spaan & Sven Strickroth
 */
function togglepagestate(id)
{
    var pars = "module=content&func=togglepagestate&id=" + id + "&active=" + $('inactive_' + id).visible();
    var myAjax = new Ajax.Request(
        "ajax.php",
        {
            method: 'get',
            parameters: pars,
            onComplete: togglepagestate_response
        });
}

/**
 * Ajax response function for updating block status: cleanup
 *
 *@params none;
 *@return none;
 *@author Erik Spaan
 */
function togglepagestate_response(req)
{
    if (req.status != 200 ) {
        pnshowajaxerror(req.responseText);
        return;
    }

    var json = pndejsonize(req.responseText);

    $('active_' + json.id).toggle();
    $('inactive_' + json.id).toggle();
    $('activity_' + json.id).update((($('activity_' + json.id).innerHTML == msgPageStatusOffline) ? msgPageStatusOnline : msgPageStatusOffline));
}

/**
 * Toggle a page inmenu status
 *
 *@params page id;
 *@return none;
 *@author Erik Spaan & Sven Strickroth
 */
function togglepageinmenu(id)
{
    var pars = "module=content&func=togglepageinmenu&id=" + id + "&inmenu=" + $('outmenu_' + id).visible();
    var myAjax = new Ajax.Request(
        "ajax.php",
        {
            method: 'get',
            parameters: pars,
            onComplete: togglepageinmenu_response
        });
}

/**
 * Ajax response function for updating block status: cleanup
 *
 *@params none;
 *@return none;
 *@author Erik Spaan
 */
function togglepageinmenu_response(req)
{
    if (req.status != 200 ) {
        pnshowajaxerror(req.responseText);
        return;
    }

    var json = pndejsonize(req.responseText);

    $('inmenu_' + json.id).toggle();
    $('outmenu_' + json.id).toggle();
    $('menustatus_' + json.id).update((($('menustatus_' + json.id).innerHTML == msgPageOutMenu) ? msgPageInMenu : msgPageOutMenu));
}
