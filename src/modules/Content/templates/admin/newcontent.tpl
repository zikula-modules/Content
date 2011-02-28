{include file="admin/menu.tpl"}
<div class="z-admincontainer z-clearfix">
    <div class="z-adminpageicon">{icon type="view" size="large"}</div>
    <h2>{gt text="Add new content to page"}</h2>
    {contentpagepath pageId=$page.id language=$page.language assign='subheader'}
    <h3>{$subheader}</h3>

    {form cssClass='z-form z-linear'}

    {formsetinitialfocus inputId='contentType'}
    {formerrormessage id='error'}

    {contentformframe}

    <p>{gt text="Please select the type of content you want to add to your page."}</p>

    <fieldset>
        <legend>{gt text="Select content type"}</legend>
        {contentcontenttypeselector id='contentType'}
    </fieldset>

    <div class="z-buttons">
        {formbutton class="z-bt-new" commandName="create" __text="Create"}
        {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    </div>

    {/contentformframe}

    {/form}
</div>