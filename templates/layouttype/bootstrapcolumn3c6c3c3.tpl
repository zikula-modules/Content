{include file="layouttype/header.tpl" pid=$page.id}
<div class="content-layout column3">
    <div class="content-area-top col-md-12">
        {if !empty($page.content[0])}
        {foreach from=$page.content[0] item=c}
        {contenteditthis data=$c access=$access type='content'}
        {$c.output}
        {/foreach}
        {/if}
    </div>

    <div class="col-md-6">
        {if !empty($page.content[1])}
        {foreach from=$page.content[1] item=c}
        {contenteditthis data=$c access=$access type='content'}
        {$c.output}
        {/foreach}
        {/if}
    </div>

    <div class="col-md-3">
        {if !empty($page.content[2])}
        {foreach from=$page.content[2] item=c}
        {contenteditthis data=$c access=$access type='content'}
        {$c.output}
        {/foreach}
        {/if}
    </div>

    <div class="col-md-3">
        {if !empty($page.content[3])}
        {foreach from=$page.content[3] item=c}
        {contenteditthis data=$c access=$access type='content'}
        {$c.output}
        {/foreach}
        {/if}
    </div>

    <div class="content-area-bottom col-md-12">
        {if !empty($page.content[4])}
        {foreach from=$page.content[4] item=c}
        {contenteditthis data=$c access=$access type='content'}
        {$c.output}
        {/foreach}
        {/if}
    </div>
</div>
{include file="layouttype/footer.tpl" pid=$page.id}