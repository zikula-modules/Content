{if $displayMode eq 'inline'}
<dl class="content-video content-shockwave">
    <dt>
        <object id="csSWF" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="{$width}" height="{$height}" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,115,0">
            <param name="src" value="{$videoPath}" />
            <param name="bgcolor" value="#1a1a1a"/>
            <param name="quality" value="best"/>
            <param name="allowScriptAccess" value="always"/>
            <param name="allowFullScreen" value="true"/>
            <param name="scale" value="showall"/>
            <embed name="csSWF" src="{$videoPath}" width="{$width}" height="{$height}" bgcolor="#1a1a1a" quality="best" allowScriptAccess="always" allowFullScreen="true" scale="showall"  pluginspage="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash"></embed>
        </object>
    </dt>
    <dd>{$text}</dd>
{if $author ne ''}
    <dd>{gt text='by %s' tag1=$author}</dd>
{/if}
</dl>
{else}
{pageaddvar name='javascript' value='prototype'}
{pageaddvar name='javascript' value='modules/Content/lib/vendor/lightwindow/javascript/lightwindow.js'}
{pageaddvar name='stylesheet' value='modules/Content/lib/vendor/lightwindow/css/lightwindow.css'}

<dl class="content-video content-shockwave">
    <dt>
        <a title="{$videoPath}" caption="{$text}" author="{$author}" href="{$videoPath}" class="lightwindow page-options" params="lightwindow_width={$width},lightwindow_height={$height},lightwindow_loading_animation=false">{gt text='Play Video'}</a>
    </dt>
    <dd><a title="{$videoPath}" caption="{$text}" author="{$author}" href="{$videoPath}" class="play-icon lightwindow page-options" params="lightwindow_width={$width},lightwindow_height={$height},lightwindow_loading_animation=false" title=>{gt text='Play Video'}</a></dd>
</dl>
<p><!--[$text]--></p>
{/if}