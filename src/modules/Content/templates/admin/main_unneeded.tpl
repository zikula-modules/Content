{gt text="Module administration" assign=templatetitle}
{admincategorymenu}

<div class="z-adminbox">
    <h2>{$modinfo.displayname}</h2>
    {modulelinks modname='Content' type='admin'}
</div>

<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname=core src=windowlist.png set=icons/large alt=$templatetitle}</div>
    <h3>{$templatetitle}</h3>

    <p>{gt text="Welcome to your content administration. We have split the editing and administrative setup into separate systems. So please select from the list below or the menu above."}</p>

    <ul>
        <li><a href="{modurl modname='Content' type='admin' func='main'}">{gt text="Page list"}</a></li>
        {checkpermissionblock component='Content::' instance='::' level=ACCESS_ADMIN}
        <li><a href="{modurl modname='Content' type='admin' func='settings'}">{gt text="Settings"}</a></li>
        {/checkpermissionblock}
        <li><a href="{modurl modname='Content' type='user' func='sitemap'}">{gt text="Sitemap"}</a></li>
        <li><a href="{modurl modname='Content' type='user' func='extlist'}">{gt text="Extended page list (showing page headers)"}</a></li>
        <li><a href="{modurl modname='Content' type='user' func='pagelist'}">{gt text="Complete page list (showing complete pages)"}</a></li>
        <li><a href="{modurl modname='Content' type='user' func='categories'}">{gt text="Show content by category"}</a></li>
    </ul>

    <p class="z-center">
        <a href="http://code.zikula.org/content/" title="Content">{$modinfo.name} v{$modinfo.version}</a>
    </p>

</div>
