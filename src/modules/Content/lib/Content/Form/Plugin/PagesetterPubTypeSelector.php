<?php

/**
 * Pagesetter plugin generates forms select for selecting publication type
 *
 * Typical use in template file:
 * <code>
 * <!--[pagesetter_pubtypeselector id="tid"]-->
 * </code>
 */
class Content_Form_Plugin_PagesetterPubTypeSelector extends Zikula_Form_Plugin_DropdownList
{
    public function getFilename()
    {
        return __FILE__;
    }

    function load($view, &$params)
    {
        if (!ModUtil::loadApi('pagesetter', 'admin')) {
            return false;
        }
        $pubtypeslist = ModUtil::apiFunc('pagesetter', 'admin', 'getPublicationTypes');
        $this->addItem('', 0);

        foreach ($pubtypeslist as $pubtype) {
            $this->addItem($pubtype[title], $pubtype[id]);
        }

        parent::load($view, $params);
    }
}
