<div class="content-flickr">
    {foreach from=$photos item=photo}
    <a href="{$photo.url}" class="content-flickr-link"><img src="{$photo.src}" title="{$photo.title}" alt="{$photo.title}"/></a>
    {/foreach}
</div>