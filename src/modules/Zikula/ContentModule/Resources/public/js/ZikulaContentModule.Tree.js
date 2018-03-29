'use strict';

var nodeEntityId = 0;

/**
 * Helper function to start several different ajax actions
 * performing tree related amendments and operations.
 */
function zikulaContentPerformTreeOperation(objectType, rootId, op) {
    var opParam, params;

    opParam = ((op === 'moveNodeTop' || op === 'moveNodeUp' || op === 'moveNodeDown' || op === 'moveNodeBottom') ? 'moveNode' : op);
    params = {
        ot: objectType,
        op: opParam
    };

    if (op !== 'addRootNode') {
        if (!nodeEntityId) {
            zikulaContentSimpleAlert(jQuery('.tree-container'), Translator.__('Error'), Translator.__('Invalid node id'), 'treeInvalidNodeAlert', 'danger');
            return;
        }
        params['root'] = rootId;
        params[op === 'addChildNode' ? 'pid' : 'id'] = nodeEntityId;

        if (op === 'moveNodeTop') {
            params['direction'] = 'top';
        } else if (op === 'moveNodeUp') {
            params['direction'] = 'up';
        } else if (op === 'moveNodeDown') {
            params['direction'] = 'down';
        } else if (op === 'moveNodeBottom') {
            params['direction'] = 'bottom';
        }
    }

    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('zikulacontentmodule_ajax_handletreeoperation'),
        data: params
    }).done(function (response) {
        if (response.result == 'success') {
            /*zikulaContentSimpleAlert(jQuery('.tree-container'), Translator.__('Success'), response.message, 'treeAjaxDoneAlert', 'success');*/

            if (typeof response.returnUrl != 'undefined') {
                window.location = response.returnUrl;
            } else {
                window.location.reload();
            }
        } else {
            zikulaContentSimpleAlert(jQuery('.tree-container'), Translator.__('Error'), response.message != '' ? response.message : Translator.__('Could not persist your change.'), 'treeAjaxFailedAlert', 'danger');
        }
    }).fail(function (jqXHR, textStatus) {
        zikulaContentSimpleAlert(jQuery('.tree-container'), Translator.__('Error'), Translator.__('Could not persist your change.'), 'treeAjaxFailedAlert', 'danger');
    });
}

var tree;
var objectType;
var rootId;
var hasDisplay;
var hasEdit;

/**
 * Initialise a tree.
 */
function zikulaContentInitTree(idPrefix, theObjectType, theRootId, hasDisplayAction, hasEditAction) {
    objectType = theObjectType;
    rootId = theRootId;
    hasDisplay = hasDisplayAction;
    hasEdit = hasEditAction;

    tree = jQuery('#' + idPrefix).jstree({
        'core': {
            'multiple': false,
            'check_callback': true
        },
        'contextmenu': {
            'items': zikulaContentTreeContextMenuActions
        },
        'dnd': {
            'copy': false,
            'is_draggable': function (node) {
                // disable drag and drop for root category
                return !jQuery(node).hasClass('lvl0');
            }
        },
        'state': {
            'key': idPrefix
        },
        'plugins': [ 'contextmenu', 'dnd', 'search', 'state', 'wholerow' ]
    });
    

    // Drag n drop
    tree.on('move_node.jstree', function (event, data) {
        var node = data.node;
        var parentId = data.parent;
        var parentNode = tree.jstree('get_node', parentId, false);

        zikulaContentTreeSave(node, parentNode, 'bottom');
    });

    // Expand and collapse
    jQuery('#' + idPrefix + 'Expand').click(function (event) {
        event.preventDefault();
        tree.jstree(true).open_all(null, 500);
    });
    jQuery('#' + idPrefix + 'Collapse').click(function (event) {
        event.preventDefault();
        tree.jstree(true).close_all(null, 500);
    });

    // Search
    var searchStartDelay = false;
    jQuery('#' + idPrefix + 'SearchTerm').keyup(function () {
        if (searchStartDelay) {
            clearTimeout(searchStartDelay);
        }
        searchStartDelay = setTimeout(function () {
            var v = jQuery('#' + idPrefix + 'SearchTerm').val();
            tree.jstree(true).search(v);
        }, 250);
    });

    // allow redirecting if a link has been clicked
    tree.find('ul').on('click', 'li.jstree-node a', function (event) {
        tree.jstree('save_state');
        document.location.href = jQuery(this).attr('href');
    });
}

/**
 * Initialise context menu actions for a given tree node.
 */
function zikulaContentTreeContextMenuActions(theNode) {
    var currentNode;
    var isRoot;
    
    currentNode = tree.jstree('get_node', theNode, true);
    isRoot = (currentNode.attr('id') === 'tree' + rootId + 'node_' + rootId);
    nodeEntityId = currentNode.attr('id').replace('tree' + rootId + 'node_', '');
    
    var actions = {};
    
    if (true === hasDisplay) {
        actions.display = {
            label: Translator.__('Display'),
            title: Translator.__('Show detail page'),
            action: function (node) {
                document.location.href = Routing.generate('zikulacontentmodule_' + objectType.toLowerCase() + '_display', { id: nodeEntityId }, true);
            },
            icon: 'fa fa-fw fa-eye'
        };
    }
    if (true === hasEdit) {
        actions.edit = {
            label: Translator.__('Edit'),
            title: Translator.__('Show edit form'),
            action: function (node) {
                document.location.href = Routing.generate('zikulacontentmodule_' + objectType.toLowerCase() + '_edit', { id: nodeEntityId }, true);
            },
            icon: 'fa fa-fw fa-pencil-square-o'
        };
    }
    actions.addChildNode = {
        label: Translator.__('Add child node'),
        title: Translator.__('Add child node'),
        action: function (node) {
            zikulaContentPerformTreeOperation(objectType, rootId, 'addChildNode');
        },
        icon: 'fa fa-fw fa-plus'
    };
    actions.deleteNode = {
        label: Translator.__('Delete'),
        title: Translator.__('Delete this node'),
        action: function (node) {
            var confirmQuestion;
            var amountOfChildren;
    
            confirmQuestion = Translator.__('Do you really want to remove this node?');
            amountOfChildren = node.children.length;
            if (amountOfChildren > 0) {
                confirmQuestion = Translator.__('Do you really want to remove this node including all child nodes?');
            }
            if (false !== window.confirm(confirmQuestion)) {
                zikulaContentPerformTreeOperation(objectType, rootId, 'deleteNode');
            }
        },
        icon: 'fa fa-fw fa-trash-o'
    };
    
    if (isRoot) {
        return actions;
    }
    
    if (currentNode.is(':first-child') && currentNode.is(':last-child')) {
        return actions;
    }
    
    if (!currentNode.is(':first-child')) { // has previous sibling
        actions.moveTop = {
            label: Translator.__('Move to top'),
            title: Translator.__('Move to top position'),
            action: function (node) {
                zikulaContentPerformTreeOperation(objectType, rootId, 'moveNodeTop');
            },
            icon: 'fa fa-fw fa-angle-double-up',
            separator_before: true
        };
        actions.moveUp = {
            label: Translator.__('Move up'),
            title: Translator.__('Move one position up'),
            action: function (node) {
                zikulaContentPerformTreeOperation(objectType, rootId, 'moveNodeUp');
            },
            icon: 'fa fa-fw fa-angle-up'
        };
    }
    if (!currentNode.is(':last-child')) { // has next sibling
        actions.moveDown = {
            label: Translator.__('Move down'),
            title: Translator.__('Move one position down'),
            action: function (node) {
                zikulaContentPerformTreeOperation(objectType, rootId, 'moveNodeDown');
            },
            icon: 'fa fa-fw fa-angle-down',
            separator_before: currentNode.is(':first-child')
        };
        actions.moveBottom = {
            label: Translator.__('Move to bottom'),
            title: Translator.__('Move to bottom position'),
            action: function (node) {
                zikulaContentPerformTreeOperation(objectType, rootId, 'moveNodeBottom');
            },
            icon: 'fa fa-fw fa-angle-double-down'
        };
    }
    
    return actions;
}

/**
 * Callback function for config.onSave. This function is called after each tree change.
 *
 * @param node - the node which is currently being moved
 * @param parentNode - the new parent node
 * @param position - can be "after", "before" or "bottom" and defines
 *       whether the affected node is inserted after, before or as last child of "relativenode"
 *
 * @return true on success, otherwise the change will be reverted
 */
function zikulaContentTreeSave(node, parentNode, position) {
    var nodeParts, rootId, nodeId, destId;

    // do not allow inserts on root level
    if (node.parents.length < 1) {
        return false;
    }

    nodeParts = node.id.split('node_');
    rootId = nodeParts[0].replace('tree', '');
    nodeId = nodeParts[1];
    destId = parentNode.id.replace('tree' + rootId + 'node_', '');

    jQuery.ajax({
        method: 'POST',
        url: Routing.generate('zikulacontentmodule_ajax_handletreeoperation'),
        data: {
            op: 'moveNodeTo',
            direction: position,
            root: rootId,
            id: nodeId,
            destid: destId
        }
    }).done(function (res) {
        return true;
    }).fail(function (jqXHR, textStatus) {
        var treeName = 'itemTree' + rootId;
        zikulaContentSimpleAlert(jQuery('.tree-container'), Translator.__('Error'), Translator.__('Could not persist your change.'), 'treeAjaxFailedAlert', 'danger');

        window.location.reload();
        return false;
    });

    return true;
}

jQuery(document).ready(function () {
    if (jQuery('#treeAddRoot').length > 0) {
        jQuery('#treeAddRoot').click(function (event) {
            event.preventDefault();
            zikulaContentPerformTreeOperation(jQuery(this).data('object-type'), 1, 'addRootNode');
        }).removeClass('hidden');
    }

    if (jQuery('.tree-container').length > 0) {
        var treeContainer;
        var idPrefix;
        var objectType;

        treeContainer = jQuery('.tree-container').first();
        idPrefix = treeContainer.attr('id');
        objectType = treeContainer.data('object-type');

        zikulaContentInitTree(idPrefix, objectType, treeContainer.data('root-id'), treeContainer.data('has-display'), treeContainer.data('has-edit'));
    }
});
