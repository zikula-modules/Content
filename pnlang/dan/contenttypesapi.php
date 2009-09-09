<?php
/**
 * Content Danish translation
 *
 * @copyright (C) 2007, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id$
 * @license See license.txt
 */

define('_CONTENT_CONTENTENTTYPE_AUTHORTITLE', 'Forfatterinformation');
define('_CONTENT_CONTENTENTTYPE_AUTHORDESCR', 'Viser diverse informationer om forfatteren til siden.');
define('_CONTENT_CONTENTENTTYPE_AUTHORLABEL', 'Forfatter');

define('_CONTENT_CONTENTTYPE_BLOCKTITLE','Zikula block');
define('_CONTENT_CONTENTTYPE_BLOCKDESCR','Vis indhold af Zikula-block');
define('_CONTENT_CONTENTTYPE_BLOCKEDIT','Indtast block-ID');

define('_CONTENT_CONTENTENTTYPE_CIRCAVIETITLE', 'Circavie tidslinie');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEDESCR', 'Vis en tidslinie fra Circavie.');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEURLLABEL', 'URL til tidslinie');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEURLLABELHELP', 'Noget i stil med "http://www.circavie.com/timelines/a0db6c8c-b8d7-5960-9ebb-a6150f0c564c".');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIETEXTLABEL', 'Tekst');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIENOTVALIDURL', 'Ugyldig Circavie URL');

define('_CONTENT_CONTENTENTTYPE_COMPUTERCODETITLE', 'Computer programtekst');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODEDESCR', 'Teksteditor for computer programtekster. Teksten vises i en monospaced font og med linienumre.');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODENOBBCODE', 'Hvis du registrerer modulet BBCode som "hook" for Content, så kan du få bedre formatering af programteksten.');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODETEXTLABEL', 'Programtekst');

define('_CONTENT_CONTENTENTTYPE_DIRECTORYTITLE', 'Indholdsfortegnelse');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYDESCR', "Indholdsfortegnelse over undersider (baseret på sider i dette modul).");
define('_CONTENT_CONTENTENTTYPE_DIRECTORYITEMTITLE', 'Indholdsfortegnelse for %title%');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYPAGEID', 'Side ID');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYINCLUDEHEADING', 'Medtag underoverskrifter');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYINCLUDESUBPAGE', 'Medtag undersider');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYSELECTROOT', 'Alle sider');

define('_CONTENT_CONTENTENTTYPE_FLICKRTITLE', 'Billeder fra Flickr.com');
define('_CONTENT_CONTENTENTTYPE_FLICKRDESCR', 'Vis frimærkebilleder fra Flickr-bruger eller via nøgleord (tags).');
define('_CONTENT_CONTENTENTTYPE_FLICKRUSERNAME', 'Vis billeder fra denne bruger');
define('_CONTENT_CONTENTENTTYPE_FLICKRTAGS', 'Vis billeder der er markeret med disse nøgleord (kommasepareret)');
define('_CONTENT_CONTENTENTTYPE_FLICKRPHOTOCOUNT', 'Antal billeder der skal vises');
define('_CONTENT_CONTENTENTTYPE_FLICKREDITVIEW', 'Flickr-billeder fra brugeren \'%user%\' og med nøgleord(ene) \'%tags%\'');
define('_CONTENT_CONTENTENTTYPE_FLICKRNOKEY', 'Der er ikke registreret nogen Flickr-nøgle! Du skal registrere en Flickr API nøgle for at anvende denne feature. Du kan hente en nøgle fra <a href="http://www.flickr.com/api">flickr.com</a>.');

define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPLONGITUDELABEL', 'Længdegrad');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPLATITUDELABEL', 'Højdegrad');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPZOOMLABEL', 'Zoom');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPTEXTLABEL', 'Beskrivelse');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPHELP', 'Vælg geografisk position ved enten at pege-og-klikke eller ved at skrive positionen direkte. Du kan trække i kortet med musen og dobbeltklikke for at zoome og vælge position.');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPTITLE', 'Google map');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPDESCR', 'Vis Google map position.');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPNOKEY', 'Der er ikke indtastet nogen Google maps API nøgle! Du skal indtaste en Google maps API nøgle under administrationen for at kunne anvende denne feature. Du kan få en nøgle fra <a href="http://www.google.com/apis/maps/signup.html">google.com</a>.');

define('_CONTENT_CONTENTENTTYPE_HEADINGTITLE', 'Overskrift');
define('_CONTENT_CONTENTENTTYPE_HEADINGDESCR', 'Overskrift til enkelte afsnit af siden.');
define('_CONTENT_CONTENTENTTYPE_HEADINGTEXTLABEL', 'Overskrift');
define('_CONTENT_CONTENTENTTYPE_HEADINGTEXTDEFAULT', 'overskrift');

define('_CONTENT_CONTENTENTTYPE_HTMLTITLE', 'HTML tekst');
define('_CONTENT_CONTENTENTTYPE_HTMLDESCR', 'En HTML tekst editor.');
define('_CONTENT_CONTENTENTTYPE_HTMLTEXTLABEL', 'Tekst');
define('_CONTENT_CONTENTENTTYPE_HTMLTEXTDEFAULT', '... indsæt tekst her ...');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPEHTMLLABEL', 'HTML');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPETEXTLABEL', 'Formateret tekst');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPERAWLABEL', 'Uformateret tekst');

define('_CONTENT_CONTENTENTTYPE_MODULEFUNCMODULELABEL', 'Modulnavn');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCTYPELABEL', 'Funktionstype');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCFUNCTIONLABEL', 'Funktionsnavn');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCQUERYLABEL', 'Funktionsparametre');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCQUERYLABELHELP', 'Adskil med komma, f.eks. x=5,y=2');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCTITLE', 'Modulvisning');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCDESCR', 'Vis output fra et vilkårligt modul.');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCHELP', 'Vær forsigtig med denne feature! Nogle moduler fungerer ikke særligt godt hvis de bliver vist sammen med andre moduler.');

define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTITLE', 'Pagesetter publikation');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBDESCR', 'Vis en Pagesetter publikation.');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PID', 'Publication PID');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTPL', 'Templateformat for visning af publikation');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBLISTTITLE', 'Pagesetter liste');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBLISTDESCR', 'Pagesetter liste med filtrerede, sorterede, og/eller formaterede publikationer.');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTYPE', 'Publikationstype');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_NUMPUS', 'Antal publikationer (tom = default)');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_OFFSET', 'Nummer på første publikation som skal vises (tom = første i listen)');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_FILTER', 'Filtre som anvendt i URL, adskilt med "&", men uden "filter=" (f.eks. "country:eq:DK")');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_ORDER', 'Orderby udtryk som i URL. Angiv kommasepareret liste af feltnavne uden "orderby=" (f.eks. "core.lastUpdated:desc,title")');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_TPL', 'Templateformat for visning af publikationer');

define('_CONTENT_CONTENTENTTYPE_QUOTETITLE', 'Citat');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCR', 'Et fremhævet citat med kildeangivelse.');
define('_CONTENT_CONTENTENTTYPE_QUOTETEXTLABEL', 'Tekst');
define('_CONTENT_CONTENTENTTYPE_QUOTETEXTDEFAULT', '... skriv citatet her ...');
define('_CONTENT_CONTENTENTTYPE_QUOTESOURCELABEL', 'Kildereference');
define('_CONTENT_CONTENTENTTYPE_QUOTESOURCEDEFAULT', '');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCLABEL', 'Kilde');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCDEFAULT', 'Navn på kilden');

define('_CONTENT_CONTENTENTTYPE_RSSTITLE', 'RSS-feed');
define('_CONTENT_CONTENTENTTYPE_RSSDESCR', 'Viser overskrifterne fra et RSS-feed.');
define('_CONTENT_CONTENTENTTYPE_RSSMAXNOOFITEMS', 'Maks. antal artikler der skal vises');
define('_CONTENT_CONTENTENTTYPE_RSSURLLABEL', 'URL for RSS-feed (inklusiv http://)');
define('_CONTENT_CONTENTENTTYPE_RSSINCLCONT', 'Medtag hele teksten fra hver RSS-artikel (og ikke kun overskriften)');
define('_CONTENT_CONTENTENTTYPE_RSSREFRESH', 'Opdateringsfrekvens (minutter)');

define('_CONTENT_CONTENTENTTYPE_SLIDESHARETITLE', 'Slideshare');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREDESCR', 'Viser slideshow fra slideshare.com');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREREFLABEL', 'Slideshare\'s Wordpress code');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREREFLABELHELP', 'Indsæt Slideshares Wordpress embed code her (inkl. firkantede klammer)');
define('_CONTENT_CONTENTENTTYPE_SLIDESHARETEXTLABEL', 'Beskrivelse');
define('_CONTENT_CONTENTENTTYPE_SLIDESHARENOTVALIDREF', 'Ugyldig Slideshare Wordpress embed code');

define('_CONTENT_CONTENTENTTYPE_YOUTUBETITLE', 'YouTube videofilm');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDESCR', 'Viser en YouTube videofilm.');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDISPINLINELABEL', 'Vis filmen inline');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDISPPOPUPLABEL', 'Vis filmen i et popup-vindue');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEURLLABEL', 'URL til videofilmen');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEURLLABELHELP', 'Noget i stil med "http://www.youtube.com/watch?v=ABcDEFgHij&feature=dir".');
define('_CONTENT_CONTENTENTTYPE_YOUTUBETEXTLABEL', 'Tekst');
define('_CONTENT_CONTENTENTTYPE_YOUTUBENOTVALIDURL', 'Ukendt YouTube URL');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEPLAYME', 'Afspil video');
