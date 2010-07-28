<?php

class Content_Form_Plugin_FormFrame extends pnFormPlugin
{
  var $useTabs;
  var $cssClass = 'tabs';


  function getFilename()
  {
    return __FILE__; // FIXME: may be found in smarty's data???
  }


  function create(&$render, &$params)
  {
    $this->useTabs = (array_key_exists('useTabs', $params) ? $params['useTabs'] : false);
  }


  function renderBegin(&$render)
  {
    $tabClass = $this->useTabs ? ' '.$this->cssClass : '';
    return "<div class=\"content-form{$tabClass}\">\n";
  }


  function renderEnd(&$render)
  {
    return "</div>\n";
  }
}
