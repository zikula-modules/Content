<div class="z-formrow">
    {formlabel for='pid' __text='Page'}
    {formdropdownlist id="pid" items=$pidItems group="data"}
</div>

<div class="z-formrow">
    {formlabel for='includeSelf' __text='Include self into the table of contents'}
    {formcheckbox id="includeSelf" group="data"}
    <span class="z-sub z-formnote">if page isn't 'All pages'</span>
</div>

<div class="z-formrow">
    {formlabel for='includeNotInMenu' __text='Include subpages that are not in the menus'}
    {formcheckbox id="includeNotInMenu" group="data"}
</div>

<div class="z-formrow">
    {formlabel for='includeHeading' __text='Include headings'}
    {formcheckbox id="includeHeading" group="data"}
</div>

<div class="z-formrow">
    {formlabel for='includeHeadingLevel' __text='Include headings up to level'}
    {formtextinput id="includeHeadingLevel" group="data"}
    <span class="z-sub z-formnote">if headings are included; select 0 to include menu only for the selected page</span>
</div>

<div class="z-formrow">
    {formlabel for='includeSubpageLevel' __text='Include subpages into table up to level'}
    {formtextinput id="includeSubpageLevel" group="data"}
    <span class="z-sub z-formnote">select 0 to include no sublevels</span>
</div>
