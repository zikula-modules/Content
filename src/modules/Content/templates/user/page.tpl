{modulelinks modname='Content' type='user'}
{if $page.metadescription ne ''}
    {setmetatag name='description' value=$page.metadescription|replace:"<br />":"\n"|strip_tags|replace:"\"":""}
{/if}
{if $page.metakeywords ne ''}
    {setmetatag name='keywords' value=$page.metakeywords|replace:"<br />":"\n"|strip_tags|replace:"\"":""}
{/if}
<div id="page{$page.id}" class="z-content-page">
    {include file='user/pageinfo.tpl'}
    {include file=$page.layoutTemplate inlist=0}
</div>