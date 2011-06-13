{include file="admin/menu.tpl"}

<div class="z-admincontainer z-clearfix">
    <div class="z-adminpageicon">{icon type="view" size="large"}</div>
    <h2>{gt text="Migrate Data to Content"}</h2>
    <form class='z-form' action="{modurl modname='Content' type='admin' func='migrate'}" method="post" enctype="application/x-www-form-urlencoded">
        <div>
            <input type="hidden" name="csrftoken" value="{insert name="csrftoken"}" />
            <fieldset>
                <legend>{gt text='Module selection'}</legend>
                <div class="z-formrow">
                    <label for="migratemodule">{gt text='Select a module'}</label>
                    <select id="migratemodule" name="migratemodule">
                        <option label="ContentExpress" value="ContentExpress">ContentExpress</option>
                    </select>

                </div>
            </fieldset>

            <div class="z-formbuttons z-buttons">
                {button src='button_ok.png' set='icons/extrasmall' __alt='Migrate' __title='Migrate' __text='Migrate'}
                <a href="{modurl modname='Content' type='admin' func='view'}">{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Cancel'  __title='Cancel'} {gt text='Cancel'}</a>
            </div>
        </div>
    </form>
    
</div>