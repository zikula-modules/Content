{if $directory.directory}
<ul class="content-directory">
    {foreach from=$directory.directory item="item"}
    <li><a href="{$item.url|safetext}">{$item.title|safetext}</a>
        {if !empty($item.directory)}
        {include file="contenttype/directory_view.tpl" directory=$item}
        {/if}
    </li>
    {/foreach}
</ul>
{/if}