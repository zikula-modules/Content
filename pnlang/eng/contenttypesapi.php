<?php
/**
 * Content English translate
 *
 * @copyright (C) 2007-2009, Content Development Team
 * @link http://code.zikula.org/content
 * @version $Id$
 * @license See license.txt
 */

define('_CONTENT_CONTENTENTTYPE_AUTHORTITLE', 'Author Infobox');
define('_CONTENT_CONTENTENTTYPE_AUTHORDESCR', 'Various information about the author of the page.');
define('_CONTENT_CONTENTENTTYPE_AUTHORLABEL', 'Author');

define('_CONTENT_CONTENTTYPE_BLOCKTITLE','Zikula Block');
define('_CONTENT_CONTENTTYPE_BLOCKDESCR','Show Zikula-block in Content');
define('_CONTENT_CONTENTTYPE_BLOCKEDIT','Please enter the Block-ID');

define('_CONTENT_CONTENTENTTYPE_CIRCAVIETITLE', 'Circavie timeline');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEDESCR', 'Embed a timeline from Circavie.');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEURLLABEL', 'URL to timeline');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEURLLABELHELP', 'Something like "http://www.circavie.com/timelines/a0db6c8c-b8d7-5960-9ebb-a6150f0c564c".');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIETEXTLABEL', 'Text');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIENOTVALIDURL', 'Invalid Circavie URL');

define('_CONTENT_CONTENTENTTYPE_COMPUTERCODETITLE', 'Computer Code');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODEDESCR', 'A text editor for computer code. Line numbers are added to the text and it is displayed in a monospaced font.');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODENOBBCODE', 'If you register the module BBCode as a hook for Content then you can get your computer code better formatted.');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODETEXTLABEL', 'Computer code lines');

define('_CONTENT_CONTENTENTTYPE_DIRECTORYTITLE', 'Table of contents');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYDESCR', "A table of contents of headings and subpages (build from this module's pages).");
define('_CONTENT_CONTENTENTTYPE_DIRECTORYITEMTITLE', 'Table of contents of %title%');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYPAGEID', 'PageID');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYINCLUDEHEADING', 'Include headings into table');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYINCLUDESUBPAGE', 'Include subpages into table');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYSELECTROOT', 'All pages');

define('_CONTENT_CONTENTENTTYPE_FLICKRTITLE', 'Photos from Flickr.com');
define('_CONTENT_CONTENTENTTYPE_FLICKRDESCR', 'Display thumbnails from specific Flickr user or tags.');
define('_CONTENT_CONTENTENTTYPE_FLICKRUSERNAME', 'Display photos from this user');
define('_CONTENT_CONTENTENTTYPE_FLICKRTAGS', 'Display photos tagged with these tags (comma separated)');
define('_CONTENT_CONTENTENTTYPE_FLICKRPHOTOCOUNT', 'Number of photos to show');
define('_CONTENT_CONTENTENTTYPE_FLICKREDITVIEW', 'Flickr photos from user \'%user%\' associated with tags \'%tags%\'');
define('_CONTENT_CONTENTENTTYPE_FLICKRNOKEY', 'No Flickr API key available! You must specify a Flickr API key to use this feature. You can get a key from <a href="http://www.flickr.com/api">flickr.com</a>.');

define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPLONGITUDELABEL', 'Longitude');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPLATITUDELABEL', 'Latitude');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPZOOMLABEL', 'Zoom level');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPTEXTLABEL', 'Description');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPHELP', 'Select geographic position either by point and click or writing the position directly. You can drag the map with your mouse, and double-click to zoom and select position.');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPTITLE', 'Google map');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPDESCR', 'Display Google map position.');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPNOKEY', 'No Google API key available! You must specify a Google maps API key to use this feature. Get a key from <a href="http://www.google.com/apis/maps/signup.html">google.com</a> and enter it in the admin section.');

define('_CONTENT_CONTENTENTTYPE_HEADINGTITLE', 'Heading');
define('_CONTENT_CONTENTENTTYPE_HEADINGDESCR', 'Section heading for structuring larger amounts of text.');
define('_CONTENT_CONTENTENTTYPE_HEADINGTEXTLABEL', 'Heading');
define('_CONTENT_CONTENTENTTYPE_HEADINGTEXTDEFAULT', 'Sub-Heading');

define('_CONTENT_CONTENTENTTYPE_HTMLTITLE', 'HTML text');
define('_CONTENT_CONTENTENTTYPE_HTMLDESCR', 'A rich HTML editor for adding text to your page.');
define('_CONTENT_CONTENTENTTYPE_HTMLTEXTLABEL', 'Text');
define('_CONTENT_CONTENTENTTYPE_HTMLTEXTDEFAULT', '... add text here ...');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPEHTMLLABEL', 'HTML');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPETEXTLABEL', 'Formatted text');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPERAWLABEL', 'Unformatted text');

define('_CONTENT_CONTENTENTTYPE_MODULEFUNCMODULELABEL', 'Module name');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCTYPELABEL', 'Function type');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCFUNCTIONLABEL', 'Function name');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCQUERYLABEL', 'Function parameters');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCQUERYLABELHELP', 'Separate by commas, e.g., x=5,y=2');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCTITLE', 'Module display');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCDESCR', 'Display output from any installed module.');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCHELP', 'Use this feature carefully! Some modules do not work when displayed together with other modules. DO NOT create circular module references, e.g., adding a Content page that displays itself ...');

define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTITLE', 'Pagesetter publication');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBDESCR', 'Display a Pagesetter publication.');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PID', 'Publication PID');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTPL', 'Template format for displaying the publication');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBLISTTITLE', 'Pagesetter publist');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBLISTDESCR', 'Pagesetter list of filtered, ordered, and/or formatted publications.');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTYPE', 'Publication type');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_NUMPUS', 'Number of publications (leave empty for default)');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_OFFSET', 'First publication number to show (leave empty for first in list)');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_FILTER', 'List filter as used in URL, separated by "&", but without "filter=" (e.g. "country:eq:DK")');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_ORDER', 'Orderby clause as used in URL. Should be a comma separated list of field names without "orderby=" (e.g. "core.lastUpdated:desc,title")');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_TPL', 'Template format for displaying list items');

define('_CONTENT_CONTENTENTTYPE_QUOTETITLE', 'Quote');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCR', 'A highlighted quote with source.');
define('_CONTENT_CONTENTENTTYPE_QUOTETEXTLABEL', 'Text');
define('_CONTENT_CONTENTENTTYPE_QUOTETEXTDEFAULT', '... add text here ...');
define('_CONTENT_CONTENTENTTYPE_QUOTESOURCELABEL', 'Source');
define('_CONTENT_CONTENTENTTYPE_QUOTESOURCEDEFAULT', 'http://');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCLABEL', 'Description');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCDEFAULT', 'Name of the Source');

define('_CONTENT_CONTENTENTTYPE_RSSTITLE', 'RSS feed');
define('_CONTENT_CONTENTENTTYPE_RSSDESCR', 'Display list of items in an RSS feed.');
define('_CONTENT_CONTENTENTTYPE_RSSMAXNOOFITEMS', 'Max. no. of items to display');
define('_CONTENT_CONTENTENTTYPE_RSSURLLABEL', 'URL of RSS feed (include http://)');
define('_CONTENT_CONTENTENTTYPE_RSSINCLCONT', 'Include feed text in addition to the title');
define('_CONTENT_CONTENTENTTYPE_RSSREFRESH', 'Refresh time (in minutes)');

define('_CONTENT_CONTENTENTTYPE_SLIDESHARETITLE', 'Slideshare');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREDESCR', 'Display slides from slideshare.com');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREREFLABEL', 'Slideshare\'s Wordpress code');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREREFLABELHELP', 'Copy Slideshare\'s Wordpress embed code here (including brackets and all)');
define('_CONTENT_CONTENTENTTYPE_SLIDESHARETEXTLABEL', 'Slide description');
define('_CONTENT_CONTENTENTTYPE_SLIDESHARENOTVALIDREF', 'Not valid Slideshare Wordpress embed code');

define('_CONTENT_CONTENTENTTYPE_YOUTUBETITLE', 'YouTube embedded video clip');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDESCR', 'Display YouTube video clip.');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDISPINLINELABEL', 'Show video inline');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDISPPOPUPLABEL', 'Show video in popup window');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEURLLABEL', 'URL to the video clip');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEURLLABELHELP', 'Something like "http://www.youtube.com/watch?v=ABcDEFgHij&feature=dir".');
define('_CONTENT_CONTENTENTTYPE_YOUTUBETEXTLABEL', 'Video description');
define('_CONTENT_CONTENTENTTYPE_YOUTUBENOTVALIDURL', 'Unrecognized YouTube URL');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEPLAYME', 'Play Video');