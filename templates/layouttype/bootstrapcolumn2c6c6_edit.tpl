<div class="content-column-portlet w100edit" id="content-col-0">
    {include file='content_include_contentedit.tpl' content=$page.content pageId=$page.id contentAreaIndex='0'}
</div>

<div class="z-formrow">
    {formlabel for='widthCol1' __text='Width of Column 1'}
    {formtextinput id='widthCol1' maxLength='20' group='data'}
</div>
<div class="content-column-portlet w50edit" id="content-col-1">
    {include file='content_include_contentedit.tpl' content=$page.content pageId=$page.id contentAreaIndex='1'}
</div>

<div class="z-formrow">
    {formlabel for='widthCol2' __text='Width of Column 2'}
    {formtextinput id='widthCol2' maxLength='20' group='data'}
</div>
<div class="content-column-portlet w50edit" id="content-col-2">
    {include file='content_include_contentedit.tpl' content=$page.content pageId=$page.id contentAreaIndex='2'}
</div>

<div class="content-column-portlet w100edit" id="content-col-3">
    {include file='content_include_contentedit.tpl' content=$page.content pageId=$page.id contentAreaIndex='3'}
</div>

<div class="z-clearfix">&nbsp;</div>