{* template for editing per content area *}
{contentareatitle page=$page contentArea=$contentAreaIndex}
{contentinsertlink pageId=$pageId contentAreaIndex=$contentAreaIndex}<br />

{foreach from=$content[$contentAreaIndex] item=c}
{modurl modname='Content' type='admin' func='editcontent' cid=$c.id fqurl=1 assign='editUrl'}
{formcontextmenureference menuId="contentEditMenu" commandArgument=$c.id imageURL="modules/Content/images/contextarrow.png" assign='menuHandle'}
<div class="content-portlet sortable{if !$c.active} content-item-inactive{/if}" id="content-item-{$c.id}">
	<div class="content-portlet-header">
		{* extrainfo here *}
		<a href="{$editUrl}">{$c.title|safetext} [{gt text="ID %d" tag1=$c.id}]</a> {$menuHandle} 
		<span style="float:right">
		{if $c.active}
		<span id="activitycid_{$c.id}"></span>&nbsp;<a class="content_activationbutton" href="javascript:void(0);" onclick="togglecontentstate({$c.id})">{img src="page-greenled.gif" modname="Content" title=$deactivate alt=$deactivate id="activecid_`$c.id`"}{img src="page-redled.gif" modname="Content" title=$activate alt=$activate style="display:none;" id="inactivecid_`$c.id`"}</a>
		<noscript>{img src=greenled.png modname=core set=icons/extrasmall __title="Active" __alt="Active"}</noscript>
		{else}
		<span id="activitycid_{$c.id}">{gt text="Inactive"}</span>&nbsp;<a class="content_activationbutton" href="javascript:void(0);" onclick="togglecontentstate({$c.id})">{img src="page-greenled.gif" modname="Content" title=$deactivate alt=$deactivate style="display:none;" id="activecid_`$c.id`"}{img src="page-redled.gif" modname="Content" title=$activate alt=$activate id="inactivecid_`$c.id`"}</a>
		<noscript>{img src=redled.png modname=core set=icons/extrasmall __title="Inactive" __alt="Inactive" }</noscript>
		{/if}
		</span>
	</div>
	<div class="content-portlet-content">
		{$c.output|escape:'quotes'}
		{if $c.visiblefor==0 || $c.visiblefor==2}
		<div class="content-item-extrainfo">
		{if $c.visiblefor==0}
		{gt text='only visible when Logged In'}
		{elseif $c.visiblefor==2}
		{gt text='only visible when Not Logged In'}
		{else}
		{* show nothing *}
		{/if}
		</div>
		{/if}
	</div>
</div>
{/foreach}