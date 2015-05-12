{if $tabType lt 4} {* Twitter Bootstrap styled tabs *}
<div class="row margin-bottom-20">
    <div class="col-md-12">
        <div{if !empty($tabStyle)} class="{$tabStyle}"{/if} role="tabpanel">
            {if $tabType == 1}
            <ul class="nav nav-tabs" role="tablist">
            {elseif $tabType == 2}
            <ul class="nav nav-pills" role="tablist">
            {elseif $tabType == 3}
            <div class="col-sm-3">
            <ul class="nav nav-pills nav-stacked" role="tablist">
            {/if}
            <!-- Nav tabs -->
            {foreach from=$itemsToTab item='itemToTab' name='itemToTab'}
                <li role="presentation"{if $smarty.foreach.itemToTab.first} class="active"{/if}><a href="#{$itemToTab.link}" aria-controls="{$itemToTab.link}" role="tab" data-toggle="tab">{$itemToTab.title}</a></li>
            {/foreach}
            </ul>
            {if $tabType == 3}
            </div>
            {/if}

            <!-- Tab panes -->
            {if $tabType == 3}
            <div class="col-sm-9">
            {/if}
            <div class="tab-content">
            {foreach from=$itemsToTab item='itemToTab' name='itemToTab'}
                <div role="tabpanel" class="tab-pane fade in{if $smarty.foreach.itemToTab.first} active{/if}" id="{$itemToTab.link}">{$itemToTab.display}</div>
            {/foreach}
            </div>
            {if $tabType == 3}
            </div>
            {/if}
        </div>
    </div>
</div>
{else} {* Legacy Zikula.UI styles tabs *}
{pageaddvar name="javascript" value="Zikula.UI"}
<ul id="tabs_{$contentId}" class="z-tabs{if !empty($tabStyle)} {$tabStyle}{/if}">
{foreach from=$itemsToTab item='itemToTab' name='itemToTab'}
    <li class="tab"><a href="#{$itemToTab.link}">{$itemToTab.title}</a></li>
{/foreach}
</ul>
{foreach from=$itemsToTab item='itemToTab' name='itemToTab'}
<div id="{$itemToTab.link}">{$itemToTab.display}</div>
{/foreach}
<script type="text/javascript">
var tabs = new Zikula.UI.Tabs('tabs_{{$contentId}}');
</script>
{/if}
