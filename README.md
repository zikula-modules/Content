# Content module for the Zikula Application Framework

![Build Status](http://guite.info:8080/buildStatus/icon?job=Applications_Content/5.0.0)


## Documentation

  1. [Introduction](#introduction)
  2. [Requirements](#requirements)
  3. [Installation](#installation)
  4. [Upgrading](#upgrading)
  5. [Integration with menu module](#menumodule)
  6. [Implementing custom content types](#contenttypes)
  7. [Changelog](#changelog)
  8. [TODO](#todo)
  9. [Questions, bugs and contributing](#contributing)


<a name="introduction" />

## Introduction

Content is a hierarchical page editing module for Zikula. With it you can insert and edit various content items, such as HTML texts, videos, maps and much more. Also content of other modules and blocks can be shown inside a Content page. 

Each page can arrange arbitrary content elements using Bootstrap grids.

It also features additional functionality, like translating content and tracking changes between different versions.


<a name="requirements" />

## Requirements

This module is intended for being used with Zikula 2.0.9+.


<a name="installation" />

## Installation

The Content module is installed like this:


1. Copy the content of `modules/` into the `modules/` directory of your Zikula installation. Afterwards you should a folder named `modules/Zikula/ContentModule/`.
2. Copy the content of _app/Resources/_ into the _/app/Resources_ folder of your Zikula site.
3. Initialize and activate ZikulaContentModule in the extensions administration.


<a name="upgrading" />

## Upgrading

The upgrade process from earlier versions to 5.0.0 has not been implemented yet.
Please see issue #198 for reference.


<a name="menumodule" />

## Integration with menu module

Content offers a dedicated menu block. But it can also be combined with menus from the menu module which is provided by Zikula core. You can add nodes with a placeholder title like `ContentPages_123` whereby `123` is the ID of a certain page. When displaying the menu this placeholder will be replaced by the corresponding pages sub tree. Note that only those pages are shown which are currently active, have the "in menu" flag enabled and visible to the current user.


<a name="contenttypes" />

## Implementing custom content types

In content each page consists of several content items. Each content item uses a certain content type. Interestingly, other modules can provide additional content types, so for example a calendar module can offer a content type for displaying a list of events for a specific category or the details of a single event. For each content type a corresponding class needs to be implemented which cares about displaying, editing and translating the managed data.

- A content type class names should be suffixed by `Type` and located in the `ModuleRoot/ContentType/` directory. This is not mandatory but a recommended convention.
- Content type classes must be registered as a service using the `zikula.content_type` tag.
- Content type classes must implement `Zikula\ContentModule\ContentTypeInterface`.
- Content type classes may extend `Zikula\ContentModule\AbstractContentType` for convenience.
- Content type classes must define a Symfony form type class to allow editing of their data fields if this is needed.
   Otherwise the `getEditFormClass` method must return `null`.
- The convention for template files of a content type with name `foo` is as follows:
  - Display: `@AcmeFooModule/ContentType/FooView.html.twig`
  - Edit subform: `@AcmeFooModule/ContentType/FooEdit.html.twig`
  - Translation original: `@AcmeFooModule/ContentType/FooTranslationView.html.twig`
  - Translation subform: `@AcmeFooModule/ContentType/FooTranslationEdit.html.twig`
  - In the sub form templates, **do not** render the `form_start(form)` or `form_end(form)` tags.


<a name="changelog" />

## Changelog

### Version 5.0.0

Structural changes:
- Entirely rewritten for Zikula 2.0.x using ModuleStudio.

New features:
- New UI for managing and arranging content elements more quickly and easily.
- Replaced old page layout types by a new concept for dynamic page layouts.
- A tree slug handler is utilised for creating hierarchical permalinks/URLs.
- Publication of content types can be restricted using start and/or end dates.
- Publication of content types can be restricted to specific user groups.
- Content types are now collected based on Symfony container using service tags.
- Content types are grouped into different categories.
- Content elements can be moved and copied to other pages.
- Provides plugins for Scribite editors (CKEditor, Quill, Sommernote, TinyMCE).
- Hooks can not only be attached to complete pages, but also to single content items.
- Beside UI hooks and filter hooks also form aware hooks are supported.
- The ModuleFunc content type has been renamed to Controller since it now supports not only modules, but all types of Symfony bundles.
- Google map and route content types support different map types instead of roadmaps only.
- OpenStreetMap content type has been replaced by a much more powerful Leaflet content type.
- Slideshare and vimeo content types fetch additional data from the corresponding APIs.
- Menu block has new options for different navigation types and sub pages handling.
- All blocks showing lists of pages have included detection of currently active page.
- Added owner permission support to allow non-admin users to manage their own pages.
- Custom (and multiple) CSS classes can now be used for pages, page sections and single content elements.
- Permalink settings for removing unwanted URL parts.

Deprecations:
- The short URL suffix can not be configured anymore.
- Removed the ability to register a page var for breadcrumbs in favour of a dedicated module for this purpose. There is still a Twig function for retrieving or displaying a page hierarchy though.
- Removed the possibility to order sub pages of a specific page by title.
- The JoinPosition content type has been removed because it is not needed anymore.
- The Camtasia content type has been removed.
- The Flickr content type has been removed. A better choice is the Flickr media type in the media module which is going to provide a generic media content type soon (see https://github.com/cmfcmf/MediaModule/issues/2 for reference).
- The FlashMovie content type has been removed. This is better handled by a media module, too.


<a name="todo" />

## TODO

- The `ComputerCodeType` needs to be updated for supporting the BBCode and LuMicuLa modules as soon as they have been migrated to Zikula 2. There are @todo markers for that.
- The `YouTubeType` could be enhanced to fetch additional data from API. It should use the `CacheHelper` like `SlideshareType` and `VimeoType`. There is a @todo marker for that.


<a name="contributing" />

## Questions, bugs and contributing

If you want to report something or help out with further development of the Content module please refer
to the corresponding GitHub project at https://github.com/zikula-modules/Content
