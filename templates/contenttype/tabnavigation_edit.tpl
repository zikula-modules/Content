{formsetinitialfocus inputId='contentItemIds'}

<div class="z-formrow">
    {formlabel for='contentItemIds' __text='Content Item IDs'}
    {formtextinput id='contentItemIds' maxLength='255' group='data'}
    {contentlabelhelp __text='A list of Content Item IDs semicolon separated, e.g. "3;12". Make sure that the Content Item IDs you select already exist. You can disable the individual Content Items if you only want to display them in this Tab Navigation.'}
</div>

<div class="z-formrow">
    {formlabel for='tabTitles' __text='Tab navigation titles'}
    {formtextinput id='tabTitles' maxLength='255' group='data'}
    {contentlabelhelp __text='Titles for the tabs, semicolon separated, e.g. "Recent News;Calender".'}
</div>

<div class="z-formrow">
    {formlabel for='tabLinks' __text='Tab navigation link names'}
    {formtextinput id='tabLinks' maxLength='255' group='data'}
    {contentlabelhelp __text='Internal named links for the tabs, semicolon separated and no spaces, e.g. "news;calendar".'}
</div>

<div class="z-formrow">
    {formlabel for='tabType' __text='Tab navigation type'}
    {formdropdownlist id='tabType' items=$tabTypeOptions group='data'}
</div>

<div class="z-formrow">
    {formlabel for='tabStyle' __text='Tab navigation style'}
    {formtextinput id='tabStyle' maxLength='255' group='data'}
    {contentlabelhelp __text='A CSS class name that will be used on the tab navigation.'}
</div>