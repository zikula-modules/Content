<div class="content-rss">
    <h3><a href="{$feed.permalink}">{$feed.title}</a></h3>

    {if count($feed.items) > 0}
    {if $includeContent}
    {foreach from=$feed.items item=item}
    <div class="content-rss-item">
        <h4><a href="{$item.permalink}">{$item.title}</a></h4>
        {if $item.description}<div class="content-rss-descr">{$item.description}</div>{/if}
    </div>
    {/foreach}
    {else}
    <ul>
        {foreach from=$feed.items item=item}
        <li><a href="{$item.permalink}">{$item.title}</a></li>
        {/foreach}
    </ul>
    {/if}
    {/if}
</div>