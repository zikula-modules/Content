<br class="z-clearer" />
{if !$page.nohooks}
{modurl modname="Content" type='user' func="view" pid=$pid assign="viewUrl"}
<div class="content-hooks">
{notifydisplayhooks eventname='content.hook.pages.ui.view' id=$pid}
</div>
{/if}
