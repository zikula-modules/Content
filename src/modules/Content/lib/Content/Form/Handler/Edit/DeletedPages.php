<?php

class Content_Form_Handler_Edit_DeletedPages extends Form_Handler
{
    function __construct($args)
    {
        $this->args = $args;
    }
    
    function initialize($view)
    {
        $offset = (int)FormUtil::getPassedValue('offset');
        $versionscnt = ModUtil::apiFunc('Content', 'History', 'getDeletedPagesCount');
        $versions = ModUtil::apiFunc('Content', 'History', 'getDeletedPages', array('offset' => $offset));
        if ($versions === false) {
            return $view->registerError(null);
        }

        // Assign the values for the smarty plugin to produce a pager
        $view->assign('numitems', $versionscnt);
        $view->assign('versions', $versions);

        PageUtil::setVar('title', $this->__("Restore deleted pages"));

        return true;
    }

    function handleCommand($view, &$args)
    {
        $url = ModUtil::url('Content', 'Edit', 'main');

        if ($args['commandName'] == 'restore') {
            $ok = ModUtil::apiFunc('Content', 'History', 'restoreVersion', array('id' => $args['commandArgument']));
            if ($ok === false) {
                return $view->registerError(null);
            }
        }

        return $view->redirect($url);
    }
}
