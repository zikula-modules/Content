{formsetinitialfocus inputId='userName'}

{if empty($flickrApiKey)}
<p class="z-errormsg">{gt text='No Flickr API key available! You must specify a Flickr API key to use this feature. You can get a key from %s.' tag1='<a href="http://www.flickr.com/api">flickr.com</a>'}</p>
{/if}

<div class="z-formrow">
    {formlabel for='userName' __text='Display photos from this user'}
    {formtextinput id='userName' group='data' maxLength='50'}
</div>

<div class="z-formrow">
    {formlabel for='tags' __text='Display photos tagged with these tags (comma separated)'}
    {formtextinput id='tags' group='data' maxLength='1000'}
</div>

<div class="z-formrow">
    {formlabel for='photoCount' __text='Number of photos to show'}
    {formintinput id='photoCount' group='data'}
</div>

