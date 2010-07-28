<?php

/**
 * Pagesetter plugin generates pnforms select for selecting publication type
 *
 * Typical use in template file:
 * <code>
 * <!--[pagesetter_pubtypeselector id="tid"]-->
 * </code>
 */
class Content_Form_Plugin_PagesetterPubTypeSelector extends pnFormDropdownList
{
    function getFilename()
    {
        return __FILE__;
    }


    function load(&$render, $params)
    {
        if (!ModUtil::loadApi('pagesetter', 'admin')) return false;
        $pubtypeslist = ModUtil::apiFunc('pagesetter', 'admin', 'getPublicationTypes');

        $this->addItem('', 0);

        foreach ($pubtypeslist as $pubtype) {
            $this->addItem($pubtype[title], $pubtype[id]);
        }

        parent::load($render, $params);
    }
}
