<?php

class Content_Form_Plugin_TypeSelector extends pnFormDropdownList
{
    function getFilename()
    {
        return __FILE__;
    }


    function load(&$render, $params)
    {
        parent::load($render, $params);

        $contentTypes = ModUtil::apiFunc('Content', 'Content', 'getContentTypes');

        foreach ($contentTypes as $type) {
            $this->addItem($type['title'], $type['module'].':'.$type['name']);
        }

        $this->attributes['onchange'] = "content.handleContenTypeSelected ('$this->id')";
        $this->attributes['onkeyup'] = "content.handleContenTypeSelected ('$this->id')";
    }


    function render(&$render)
    {
        $scripts = array('javascript/ajax/prototype.js', 'modules/Content/javascript/ajax.js');
        PageUtil::addVar('javascript', $scripts);

        $output = "<div class=\"z-formrow\">";
        $output .= parent::render($render);
        $output .= "</div>";

        $descr = array();

        $contentTypes = ModUtil::apiFunc('Content', 'Content', 'getContentTypes');

        foreach ($contentTypes as $type)
        {
            $descr[] = "\"$type[module]:$type[name]\" : \"" . htmlspecialchars($type['description']) . '"';
        }

        $descr = '<script type="text/javascript">/* <![CDATA[ */ var contentDescriptions = {' . implode(', ', $descr) . '} /* ]]> */</script>';

        $descr0 = (count($contentTypes) > 0 ? $contentTypes[0]['description'] : '');
        $descr0 = htmlspecialchars($descr0);
        $output .= "<div class=\"z-formrow\" id=\"{$this->id}_descr\">$descr0</div>";
        $output .= $descr;

        return $output;
    }
}

