<div class="z-formrow">
    {formlabel for='pid' __text='Page ID'}
    {formdropdownlist id="pid" items=$pidItems group="data"}
</div>

<div class="z-formrow">
    {formlabel for='includeHeading' __text='Include headings into the table'}
    {formcheckbox id="includeHeading" group="data"}
</div>

<div class="z-formrow">
    {formlabel for='includeSubpage' __text='Include subpages into the table'}
    {formcheckbox id="includeSubpage" group="data"}
</div>

<div class="z-formrow">
    {formlabel for='includeNotInMenu' __text='Include subpages that are not in the menus'}
    {formcheckbox id="includeNotInMenu" group="data"}
</div>
