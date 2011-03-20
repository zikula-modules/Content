{gt text="Page list" assign=title}
{pagesetvar name='title' value=$title}

{if !empty($pages)}
    <ul>
        {foreach from=$pages item=page}
        <li><a href="{modurl modname='Content' func='view' pid=$page.id}">{$page.title}</a></li>
        {/foreach}
    </ul>
{else}
    {gt text="There are no pages to display."}
{/if}
