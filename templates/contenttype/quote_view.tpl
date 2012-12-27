<div class="content-quote">
    {if $source}
    <blockquote cite="{$source}"><p>&raquo;{$text|nl2br}&laquo;</p></blockquote>
    {if $desc}
    <p class="source">-- <a href="{$source}">{$desc}</a></p>
    {else}
    <p class="source">-- {$source|activatelinks}</p>
    {/if}
    {else}
    <blockquote><p>&raquo;{$text|nl2br|notifyfilters:'content.hook.contentitem.ui.filter'}&laquo;</p></blockquote>
    {if $desc}
    <p class="source">-- {$desc}</p>
    {/if}
    {/if}
</div>