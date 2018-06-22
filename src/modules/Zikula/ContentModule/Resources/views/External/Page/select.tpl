{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='page'}
<div id="itemSelectorInfo" class="hidden" data-base-id="{$baseID}" data-selected-id="{$selectedId|default:0}"></div>
<div class="row">
    <div class="col-sm-8">

        {if $properties ne null && is_array($properties)}
            {gt text='All' assign='lblDefault'}
            {nocache}
            {foreach key='propertyName' item='propertyId' from=$properties}
                <div class="form-group">
                    {assign var='hasMultiSelection' value=$categoryHelper->hasMultipleSelection('page', $propertyName)}
                    {gt text='Category' assign='categoryLabel'}
                    {assign var='categorySelectorId' value='catid'}
                    {assign var='categorySelectorName' value='catid'}
                    {assign var='categorySelectorSize' value='1'}
                    {if $hasMultiSelection eq true}
                        {gt text='Categories' assign='categoryLabel'}
                        {assign var='categorySelectorName' value='catids'}
                        {assign var='categorySelectorId' value='catids__'}
                        {assign var='categorySelectorSize' value='8'}
                    {/if}
                    <label for="{$baseID}_{$categorySelectorId}{$propertyName}" class="col-sm-3 control-label">{$categoryLabel}:</label>
                    <div class="col-sm-9">
                        {selector_category name="`$baseID`_`$categorySelectorName``$propertyName`" field='id' selectedValue=$catIds.$propertyName|default:null categoryRegistryModule='ZikulaContentModule' categoryRegistryTable="`$objectType`Entity" categoryRegistryProperty=$propertyName defaultText=$lblDefault editLink=false multipleSize=$categorySelectorSize cssClass='form-control'}
                    </div>
                </div>
            {/foreach}
            {/nocache}
        {/if}
        <div class="form-group">
            <label for="{$baseID}Id" class="col-sm-3 control-label">{gt text='Page'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Id" name="id" class="form-control">
                    {foreach item='page' from=$items}
                        <option value="{$page->getKey()}"{if $selectedId eq $page->getKey()} selected="selected"{/if}>{$page->getTitle()}</option>
                    {foreachelse}
                        <option value="0">{gt text='No entries found.'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}Sort" class="col-sm-3 control-label">{gt text='Sort by'}:</label>
            <div class="col-sm-9">
                <select id="{$baseID}Sort" name="sort" class="form-control">
                    <option value="title"{if $sort eq 'title'} selected="selected"{/if}>{gt text='Title'}</option>
                    <option value="showTitle"{if $sort eq 'showTitle'} selected="selected"{/if}>{gt text='Show title'}</option>
                    <option value="views"{if $sort eq 'views'} selected="selected"{/if}>{gt text='Views'}</option>
                    <option value="active"{if $sort eq 'active'} selected="selected"{/if}>{gt text='Active'}</option>
                    <option value="activeFrom"{if $sort eq 'activeFrom'} selected="selected"{/if}>{gt text='Active from'}</option>
                    <option value="activeTo"{if $sort eq 'activeTo'} selected="selected"{/if}>{gt text='Active to'}</option>
                    <option value="inMenu"{if $sort eq 'inMenu'} selected="selected"{/if}>{gt text='In menu'}</option>
                    <option value="pageLanguage"{if $sort eq 'pageLanguage'} selected="selected"{/if}>{gt text='Page language'}</option>
                    <option value="optionalString1"{if $sort eq 'optionalString1'} selected="selected"{/if}>{gt text='Optional string 1'}</option>
                    <option value="optionalString2"{if $sort eq 'optionalString2'} selected="selected"{/if}>{gt text='Optional string 2'}</option>
                    <option value="currentVersion"{if $sort eq 'currentVersion'} selected="selected"{/if}>{gt text='Current version'}</option>
                    <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
                    <option value="createdBy"{if $sort eq 'createdBy'} selected="selected"{/if}>{gt text='Creator'}</option>
                    <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
                    <option value="updatedBy"{if $sort eq 'updatedBy'} selected="selected"{/if}>{gt text='Updater'}</option>
                </select>
                <select id="{$baseID}SortDir" name="sortdir" class="form-control">
                    <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
                    <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="{$baseID}SearchTerm" class="col-sm-3 control-label">{gt text='Search for'}:</label>
            <div class="col-sm-9">
                <div class="input-group">
                    <input type="text" id="{$baseID}SearchTerm" name="q" class="form-control" />
                    <span class="input-group-btn">
                        <input type="button" id="zikulaContentModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div id="{$baseID}Preview" style="border: 1px dotted #a3a3a3; padding: .2em .5em">
            <p><strong>{gt text='Page information'}</strong></p>
            {img id='ajaxIndicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
            <div id="{$baseID}PreviewContainer">&nbsp;</div>
        </div>
    </div>
</div>
