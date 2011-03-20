{include file="admin/menu.tpl"}

{contentpagepath pageId=$page.id language=$page.language assign='subheader'}
<div class="z-admincontainer z-clearfix">
    <div class="z-adminpageicon">{icon type="view" size="large"}</div>
    <h2>{gt text="Edit page"}</h2>
    <h3>{$subheader}</h3>

    {if empty($modvars.Content.enableVersioning)}
    <p class="z-warningmsg">{gt text="Version history is currently disabled and no new entries will be recorded."}</p>
    {/if}

    {form cssClass='z-form'}

    <table class="z-datatable">
        <thead>
            <tr>
                <th>{gt text="Revision #"}</th>
                <th>{gt text="Date"}</th>
                <th>{gt text="Event"}</th>
                <th>{gt text="User"}</th>
                <th>{gt text="IP.No."}</th>
                <th>{gt text="Action"}</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$versions item=version}
            <tr class="{cycle values="z-odd,z-even"}">
                <td>{$version.revisionNo}</td>
                <td class="z-nowrap"><a href="{modurl modname='Content' type='user' func='view' vid=$version.id}">{$version.date|date_format:datetimebrief}</a></td>
                <td>{$version.action}</td>
                <td>{$version.userName|userprofilelink}</td>
                <td>{$version.ipno}</td>
                <td>{formbutton __text="Restore" commandName="restore" commandArgument=$version.id __confirmMessage='Restore'}</td>
            </tr>
            {foreachelse}
            <tr class="z-datatableempty"><td colspan="6">{gt text="No history data recorded."}</td></tr>
            {/foreach}
        </tbody>
    </table>

    {/form}

    {pager rowcount=$numitems limit="20" posvar="offset"}
</div>