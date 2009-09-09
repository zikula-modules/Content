<?php
/**
 * Content
 *
 * @copyright (C) 2007-2009, Jorn Wildt
 * @link http://www.elfisk.dk
 * @version $Id: common.php,v 1.4 2007/05/23 18:57:43 jornlind Exp $
 * @license See license.txt
 */

/**
 * translated by
 * @author Mateo Tibaquira [mateo]
 */

define('_CONTENT_CONTENTENTTYPE_AUTHORTITLE', 'Info. del Autor');
define('_CONTENT_CONTENTENTTYPE_AUTHORDESCR', 'Varia información acerca del autor');
define('_CONTENT_CONTENTENTTYPE_AUTHORLABEL', 'Autor');

define('_CONTENT_CONTENTTYPE_BLOCKTITLE','Bloque de Zikula');
define('_CONTENT_CONTENTTYPE_BLOCKDESCR','Muestra un bloque de Zikula en Content');
define('_CONTENT_CONTENTTYPE_BLOCKEDIT','Por favor proporciona el ID del bloque');

define('_CONTENT_CONTENTENTTYPE_CIRCAVIETITLE', 'Cronograma Circavie');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEDESCR', 'Embebe un cronograma desde Circavie.');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEURLLABEL', 'URL del cronograma');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIEURLLABELHELP', 'Algo como like "http://www.circavie.com/timelines/a0db6c8c-b8d7-5960-9ebb-a6150f0c564c".');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIETEXTLABEL', 'Texto');
define('_CONTENT_CONTENTENTTYPE_CIRCAVIENOTVALIDURL', 'URL de Circavie inválida');

define('_CONTENT_CONTENTENTTYPE_COMPUTERCODETITLE', 'Código de computador');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODEDESCR', 'Un editor de texto para código de computadora. Números de linea son añadidos al texto y es mostrado con la fuente monospace.');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODENOBBCODE', 'Si registras el hook de BBCode para Content podrás tener un código mejor formateado.');
define('_CONTENT_CONTENTENTTYPE_COMPUTERCODETEXTLABEL', 'Líneas de código');

define('_CONTENT_CONTENTENTTYPE_DIRECTORYTITLE', 'Tabla de contenidos');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYDESCR', 'Una tabla de contenidos de títulos y subpáginas');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYITEMTITLE', 'Tabla de contenidos de %title%');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYPAGEID', 'ID Pag.');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYINCLUDEHEADING', 'Incluir títulos en la tabla');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYINCLUDESUBPAGE', 'Incluir subpáginas en la tabla');
define('_CONTENT_CONTENTENTTYPE_DIRECTORYSELECTROOT', 'Todas las páginas');

define('_CONTENT_CONTENTENTTYPE_FLICKRTITLE', 'Fotos desde Flickr.com');
define('_CONTENT_CONTENTENTTYPE_FLICKRDESCR', 'Muestra imágenes miniatura desde una etiqueta o usuario Flickr específico.');
define('_CONTENT_CONTENTENTTYPE_FLICKRUSERNAME', 'Mostrar fotos de este usuario');
define('_CONTENT_CONTENTENTTYPE_FLICKRTAGS', 'Mostrar fotos marcadas con estas etiquetas (separadas por comas)');
define('_CONTENT_CONTENTENTTYPE_FLICKRPHOTOCOUNT', 'Número de fotos a mostrar');
define('_CONTENT_CONTENTENTTYPE_FLICKREDITVIEW', 'Fotos Flickr del usuario \'%user%\' asociadas a las etiquetas \'%tags%\'');
define('_CONTENT_CONTENTENTTYPE_FLICKRNOKEY', 'No está disponible la llave del API de Flickr! Debes especificar una llave para poder usar ésta funcionalidad. Puedes obtener una llave desde <a href="http://www.flickr.com/api">flickr.com</a>.');

define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPLONGITUDELABEL', 'Longitud');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPLATITUDELABEL', 'Latitud');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPZOOMLABEL', 'Nivel de zoom');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPTEXTLABEL', 'Descripción');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPHELP', 'Selecciona una posición geográfica con el ratón o escríbela directamente. Puedes arrastrar el mapa con el mouse, y aumentar el zoom con doble-click y seleccionar una posición.');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPTITLE', 'Mapa Google');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPDESCR', 'Muestra una posición con los Mapas de Google.');
define('_CONTENT_CONTENTENTTYPE_GOOGLEMAPNOKEY', 'No está disponible la llave del API de Google! Debes especificar una llave para poder usar esta funcionalidad. Obten una llave desde <a href="http://www.google.com/apis/maps/signup.html">google.com</a> y digítala en la sección de administración.');

define('_CONTENT_CONTENTENTTYPE_HEADINGTITLE', 'Título');
define('_CONTENT_CONTENTENTTYPE_HEADINGDESCR', 'Estructura de sub título para textos largos');
define('_CONTENT_CONTENTENTTYPE_HEADINGTEXTLABEL', 'Título');
define('_CONTENT_CONTENTENTTYPE_HEADINGTEXTDEFAULT', 'Sub-título');

define('_CONTENT_CONTENTENTTYPE_HTMLTITLE', 'Texto HTML');
define('_CONTENT_CONTENTENTTYPE_HTMLDESCR', 'Un editor HTML enriquecido para añadir texto a tu tus páginas.');
define('_CONTENT_CONTENTENTTYPE_HTMLTEXTLABEL', 'Texto');
define('_CONTENT_CONTENTENTTYPE_HTMLTEXTDEFAULT', '... añade texto aquí ...');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPEHTMLLABEL', 'HTML');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPETEXTLABEL', 'Texto');
define('_CONTENT_CONTENTENTTYPE_HTMLTYPERAWLABEL', 'Sin formato');

define('_CONTENT_CONTENTENTTYPE_MODULEFUNCMODULELABEL', 'Nombre del módulo');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCTYPELABEL', 'Tipo de función');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCFUNCTIONLABEL', 'Nombre de la función');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCQUERYLABEL', 'Parámetros de la función');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCQUERYLABELHELP', 'Separados por comas, ej. x=5,y=2');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCTITLE', 'Llamada a un módulo');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCDESCR', 'Muestra la salida de una llamada a cualquier módulo instalado.');
define('_CONTENT_CONTENTENTTYPE_MODULEFUNCHELP', 'Usar con cuidado! Algunos módulos no funcionan cuando son mostrados a la vez. NO crees referencias circulares de módulos, ej. añadiendo una página de Content que se muestre a si misma ...');

define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTITLE', 'Publicación de Pagesetter');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBDESCR', 'Muestra una publicación de Pagesetter.');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PID', 'PID de la publicación');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTPL', 'Plantilla para mostrar la publicación');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBLISTTITLE', 'Lista de publicaciones');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBLISTDESCR', 'Lista de publicaciones filtradas, ordenadas y/o formateadas de Pagesetter.');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_PUBTYPE', 'Tipo de publicación');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_NUMPUS', 'Número de publicaciones (dejalo vacío por defecto)');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_OFFSET', 'Primer número de publicación a mostrar (dejalo vacío para tomar el primero en la lista)');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_FILTER', 'Lista de filtros como los usados en la URL, separados por "&", pero sin "filter=" (ej. "pais:eq:CO")');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_ORDER', 'Ordenadas por, así como en la URL. Puede ser una lista de campos separados por comas, sin "orderby=" (ej. "core.lastUpdated:desc,title")');
define('_CONTENT_CONTENTENTTYPE_PAGESETTER_TPL', 'Plantilla con el formato para la lista de elementos');

define('_CONTENT_CONTENTENTTYPE_QUOTETITLE', 'Cita');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCR', 'Una cita en negrilla con fuente');
define('_CONTENT_CONTENTENTTYPE_QUOTETEXTLABEL', 'Texto');
define('_CONTENT_CONTENTENTTYPE_QUOTETEXTDEFAULT', '... añade texto aquí ...');
define('_CONTENT_CONTENTENTTYPE_QUOTESOURCELABEL', 'Fuente');
define('_CONTENT_CONTENTENTTYPE_QUOTESOURCEDEFAULT', 'http://');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCLABEL', 'Descripción');
define('_CONTENT_CONTENTENTTYPE_QUOTEDESCDEFAULT', 'Nombre de la fuente');

define('_CONTENT_CONTENTENTTYPE_RSSTITLE', 'Canal RSS');
define('_CONTENT_CONTENTENTTYPE_RSSDESCR', 'Muestra una lista de elementos en un canal RSS.');
define('_CONTENT_CONTENTENTTYPE_RSSMAXNOOFITEMS', 'Número max. de elementos a mostrar');
ddefine('_CONTENT_CONTENTENTTYPE_RSSURLLABEL', 'URL del canal RSS (incluir el http://)');
define('_CONTENT_CONTENTENTTYPE_RSSINCLCONT', 'Incluir texto del canal adicionalmente a el título');
define('_CONTENT_CONTENTENTTYPE_RSSREFRESH', 'Tiempo de actualización (en minutos)');

define('_CONTENT_CONTENTENTTYPE_SLIDESHARETITLE', 'Slideshare');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREDESCR', 'Muestra presentaciones desde slideshare.com');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREREFLABEL', 'Códigode Slideshare para WordPress');
define('_CONTENT_CONTENTENTTYPE_SLIDESHAREREFLABELHELP', 'Copia el código proporcionado para WordPress aqui (incluyendo las llaves y todo)');
define('_CONTENT_CONTENTENTTYPE_SLIDESHARETEXTLABEL', 'Descripción de la presentación');
define('_CONTENT_CONTENTENTTYPE_SLIDESHARENOTVALIDREF', 'Código de Slideshare para WordPress no válido');

define('_CONTENT_CONTENTENTTYPE_YOUTUBETITLE', 'Video clip embebido de YouTube');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDESCR', 'Muestra un video de YouTube.');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDISPINLINELABEL', 'Mostrar video en línea');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEDISPPOPUPLABEL', 'Mostrar video en una ventana emergente');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEURLLABEL', 'URL del video clip');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEURLLABELHELP', 'Algo como "http://www.youtube.com/watch?v=ABcDEFgHij&feature=dir".');
define('_CONTENT_CONTENTENTTYPE_YOUTUBETEXTLABEL', 'Texto');
define('_CONTENT_CONTENTENTTYPE_YOUTUBENOTVALIDURL', 'URL de YouTube no reconocida');
define('_CONTENT_CONTENTENTTYPE_YOUTUBEPLAYME', 'Reproducir');
