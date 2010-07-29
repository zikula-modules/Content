<?php

class Content_Form_Plugin_LayoutSelector extends Form_Plugin_DropdownList
{
    function load($view, &$params)
    {
        // get all layouts if needed
        if (array_key_exists('layouts', $params)) {
            $layouts = $params['layouts'];
        } else {
            $layouts = ModUtil::apiFunc('Content', 'layout', 'getLayouts');
            if ($layouts === false) {
                return false;
            }
        }
        foreach ($layouts as $layout) {
            $this->addItem($layout['title'], $layout['name'], $layout['image']);
        }
        parent::load($view, $params);
    }
    
    /**
     * Add item to list.
     *
     * @param string $text  The text of the item.
     * @param string $value The value of the item.
     * @param string $image The image of the item.
     *
     * @return void
     */
    function addItem($text, $value, $image = null)
    {
        $item = array(
            'text' => $text,
            'value' => $value,
        	'image'	=> $image);

        $this->items[] = $item;
    }
}
