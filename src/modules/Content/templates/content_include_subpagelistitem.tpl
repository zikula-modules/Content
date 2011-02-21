<li><a href="{modurl modname='Content' func=view pid=$page.id}">{$page.title}</a>
    {if $page.subPages}
    <ul>
        {foreach from=$page.subPages item=subpage}
        {include file=content_include_subpagelistitem.tpl page=$subpage}
        {/foreach}
    </ul>
    {/if}
</li>