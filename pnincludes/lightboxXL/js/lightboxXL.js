/*
	LightboxXL v1.0.0 (Initial Release)
	Modification by Antonio Ramirez Cobos (http://www.antcut.com)
	Wednesday, February 27th, 2008
	
	Original Lightbox++ Script:
	http://www.codefidelity.com/blog/?page_id=7
	
	Which was a modification of original Lightbox Script by Lokesh Dhakar
	http://www.huddletogether.com/projects/lightbox2/
	
	Still licensed under the Creative Commons Attribution 2.5 License - http://creativecommons.org/licenses/by/2.5/
	
	This script allows you to use the same Lightbox functionality that you're used to as well as 
	embed Flash movies.  

*/

/*

	Table of Contents
	-----------------
	Configuration
	Global Variables

	Extending Built-in Objects	
	- Object.extend(Element)
	- Array.prototype.removeDuplicates()
	- Array.prototype.empty()

	Lightbox Class Declaration
	- initialize()
	- updateElementList()
	- start()
	- changeElement()
	- resizeElementContainer()
	- showElement()
	- updateDetails()
	- updateNav()
	- enableKeyboardNav()
	- disableKeyboardNav()
	- keyboardAction()
	- preloadNeighborImages()
	- end()
	
	Miscellaneous Functions
	- getPageScroll()
	- getPageSize()
	- getKey()
	- listenKey()
	- showSelectBoxes()
	- hideSelectBoxes()
	- pause()
	- initLightbox()
	
	Function Calls
	- addLoadEvent(initLightbox)
	
*/


//
//	Configuration
//
var fileLoadingImage = "modules/content/pnincludes/lightboxXL/images/loading.gif";				// Loading image		
var fileBottomNavCloseImage = "modules/content/pnincludes/lightboxXL/images/closelabel.gif";		// Close image
var fallbackOverlayImage = "modules/content/pnincludes/lightboxXL/images/overlay.png";			// Fallback overlay used with browsers that have opacity problems
var overlayOpacity = 0.8;									// Controls transparency of shadow overlay
var animate = true;											// Toggles resizing animations
var resizeSpeed = 7;										// Controls the speed of the image resizing animations (1=slowest and 10=fastest)
var borderSize = 10;										// If you adjust the padding in the CSS, you will need to update this variable

//
//	Global Variables
//
var elementArray = new Array;
var activeElement;
var userAgent = navigator.userAgent.toLowerCase();

if(animate == true){
	overlayDuration = 0.2;	
	if(resizeSpeed > 10){ resizeSpeed = 10;}
	if(resizeSpeed < 1){ resizeSpeed = 1;}
	resizeDuration = (11 - resizeSpeed) * 0.15;
} else { 
	overlayDuration = 0;
	resizeDuration = 0;
}

//
//	Additional methods for Element added by SU, Couloir
//	- Further additions by Lokesh Dhakar (huddletogether.com)
//
Object.extend(Element, {
	getWidth: function(element) {
	   	element = $(element);
	   	return element.offsetWidth; 
	},
	setWidth: function(element,w) {
	   	element = $(element);
    	element.style.width = w +"px";
	},
	setHeight: function(element,h) {
   		element = $(element);
    	element.style.height = h +"px";
	},
	setTop: function(element,t) {
	   	element = $(element);
    	element.style.top = t +"px";
	},
	setLeft: function(element,l) {
	   	element = $(element);
    	element.style.left = l +"px";
	},
	setSrc: function(element,src) {
    	element = $(element);
    	element.src = src; 
	},
	setHref: function(element,href) {
    	element = $(element);
    	element.href = href; 
	},
	setInnerHTML: function(element,content) {
		element = $(element);
		element.innerHTML = content;
	}
});

//
//	Extending built-in Array object
//	- array.removeDuplicates()
//	- array.empty()
//
Array.prototype.removeDuplicates = function () {
    for(i = 0; i < this.length; i++){
        for(j = this.length-1; j>i; j--){        
            if(this[i][0] == this[j][0]){
                this.splice(j,1);
            }
        }
    }
}

Array.prototype.empty = function () {
	for(i = 0; i <= this.length; i++){
		this.shift();
	}
}

//
//	Lightbox Class Declaration
//	- initialize()
//	- start()
//	- changeElement()
//	- resizeElementContainer()
//	- showElement()
//	- updateDetails()
//	- updateNav()
//	- enableKeyboardNav()
//	- disableKeyboardNav()
//	- keyboardNavAction()
//	- preloadNeighborImages()
//	- end()
//
//	Structuring of code inspired by Scott Upton (http://www.uptonic.com/)
//
var Lightbox = Class.create();

Lightbox.prototype = {
	
	// initialize()
	// Constructor runs on completion of the DOM loading. Calls updateElementList and then
	// the function inserts html at the bottom of the page which is used to display the shadow 
	// overlay and the image container.
	//
	initialize: function() {	
		
		this.updateElementList();

		// Code inserts html at the bottom of the page that looks similar to this:
		//
		//	<div id="overlay"></div>
		//	<div id="lightbox">
		//		<div id="outerImageContainer">
		//			<div id="imageContainer">
		//				<div id="lightboxcontent">
		//					<img id="lightboxmage">
		//					<div id="lightboxXL">
		//					</div>
		//				</div>
		//				<div style="" id="hoverNav">
		//					<a href="#" id="prevLink"></a>
		//					<a href="#" id="nextLink"></a>
		//				</div>
		//				<div id="loading">
		//					<a href="#" id="loadingLink">
		//						<img src="images/loading.gif">
		//					</a>
		//				</div>
		//			</div>
		//		</div>
		//		<div id="imageDataContainer">
		//			<div id="imageData">
		//				<div id="imageDetails">
		//					<span id="caption"></span>
		//					<span id="numberDisplay"></span>
		//				</div>
		//				<div id="bottomNav">
		//					<a href="#" id="bottomNavClose">
		//						<img src="images/close.gif">
		//					</a>
		//				</div>
		//			</div>
		//		</div>
		//	</div>


		var objBody = document.getElementsByTagName("body").item(0);
		
		var objOverlay = document.createElement("div");
		objOverlay.setAttribute('id','overlay');
		
		// The mozilla flavors on the mac, specifically Firefox and Camino have difficulties displaying flash over anything with dynamically generated opacity.
		// Here we're using the fallBackOverlayImage to alleviate this problem.    This is a semi-transparent png which is included with the script.
		if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1 || userAgent.indexOf('camino')!=-1) {
			objOverlay.style.backgroundImage = 'url('+fallbackOverlayImage+')';
			objOverlay.style.backgroundRepeat = 'repeat';
		}else{
			objOverlay.style.backgroundColor = '#000000';
		}
		objOverlay.style.display = 'none';
		objOverlay.onclick = function() { myLightbox.end(); }
		objBody.appendChild(objOverlay);
		
		var objLightbox = document.createElement("div");
		objLightbox.setAttribute('id','lightbox');
		objLightbox.style.display = 'none';
		objLightbox.onclick = function(e) {
			if (!e) var e = window.event;
			var clickObj = Event.element(e).id;
			if ( clickObj == 'lightbox') {
				myLightbox.end();
			}
		};
		objBody.appendChild(objLightbox);
			
		var objOuterImageContainer = document.createElement("div");
		objOuterImageContainer.setAttribute('id','outerImageContainer');
		if (Prototype.Browser.IE)objOuterImageContainer.style.marginLeft='0';
		objLightbox.appendChild(objOuterImageContainer);

		// When Lightbox starts it will resize itself from 250 by 250 to the current image dimension.
		// If animations are turned off, it will be hidden as to prevent a flicker of a
		// white 250 by 250 box.
		if(animate){
			Element.setWidth('outerImageContainer', 250);
			Element.setHeight('outerImageContainer', 250);			
		} else {
			Element.setWidth('outerImageContainer', 1);
			Element.setHeight('outerImageContainer', 1);			
		}

		var objImageContainer = document.createElement("div");
		objImageContainer.setAttribute('id','imageContainer');
		objOuterImageContainer.appendChild(objImageContainer);
		
		var objContent = document.createElement("div");
		objContent.setAttribute('id','lightboxcontent');
		objImageContainer.appendChild(objContent);
	
		var objLightboxImage = document.createElement("img");
		objLightboxImage.setAttribute('id','lightboximage');
		objLightboxImage.style.display = 'none';
		objContent.appendChild(objLightboxImage);
		
		objlightboxXL = document.createElement("div");
		objlightboxXL.setAttribute('id','lightboxXL');
		objlightboxXL.style.position = 'relative';
		//objlightboxXL.style.position = '0px';
		objlightboxXL.style.zIndex = 101; // Safari
		objlightboxXL.style.display = 'none';
		objContent.appendChild(objlightboxXL);
		
		var objHoverNav = document.createElement("div");
		objHoverNav.setAttribute('id','hoverNav');
		objHoverNav.style.zIndex = 999;
		objHoverNav.style.height = '60px';
		objImageContainer.appendChild(objHoverNav);
	
		var objPrevLink = document.createElement("a");
		objPrevLink.setAttribute('id','prevLink');
		objPrevLink.setAttribute('href','#');
		objHoverNav.appendChild(objPrevLink);
		
		var objNextLink = document.createElement("a");
		objNextLink.setAttribute('id','nextLink');
		objNextLink.setAttribute('href','#');
		objHoverNav.appendChild(objNextLink);
	
		var objLoading = document.createElement("div");
		objLoading.setAttribute('id','loading');
		objLoading.style.zIndex = 998;
		objImageContainer.appendChild(objLoading);
	
		var objLoadingLink = document.createElement("a");
		objLoadingLink.setAttribute('id','loadingLink');
		objLoadingLink.setAttribute('href','#');
		objLoadingLink.onclick = function() { myLightbox.end(); return false; }
		objLoading.appendChild(objLoadingLink);
	
		var objLoadingImage = document.createElement("img");
		objLoadingImage.setAttribute('src', fileLoadingImage);
		objLoadingLink.appendChild(objLoadingImage);

		var objImageDataContainer = document.createElement("div");
		objImageDataContainer.setAttribute('id','imageDataContainer');
		objLightbox.appendChild(objImageDataContainer);

		var objImageData = document.createElement("div");
		objImageData.setAttribute('id','imageData');
		objImageDataContainer.appendChild(objImageData);
	
		var objImageDetails = document.createElement("div");
		objImageDetails.setAttribute('id','imageDetails');
		objImageData.appendChild(objImageDetails);
	
		var objCaption = document.createElement("span");
		objCaption.setAttribute('id','caption');
		objImageDetails.appendChild(objCaption);
	
		var objNumberDisplay = document.createElement("span");
		objNumberDisplay.setAttribute('id','numberDisplay');
		objImageDetails.appendChild(objNumberDisplay);
		
		var objBottomNav = document.createElement("div");
		objBottomNav.setAttribute('id','bottomNav');
		objBottomNav.style.overflow = 'hidden';
		objImageData.appendChild(objBottomNav);
	
		var objBottomNavCloseLink = document.createElement("a");
		objBottomNavCloseLink.setAttribute('id','bottomNavClose');
		objBottomNavCloseLink.setAttribute('href','#');
		objBottomNavCloseLink.onclick = function() { myLightbox.end(); return false; }
		objBottomNav.appendChild(objBottomNavCloseLink);
	
		var objBottomNavCloseImage = document.createElement("img");
		objBottomNavCloseImage.setAttribute('src', fileBottomNavCloseImage);
		objBottomNavCloseLink.appendChild(objBottomNavCloseImage);
	},


	//
	// updateElementList()
	// Loops through anchor tags looking for 'lightbox' references and applies onclick
	// events to appropriate links. You can rerun after dynamically adding images w/ajax.
	//
	updateElementList: function() {	
		if (!document.getElementsByTagName){ return; }
		var anchors = document.getElementsByTagName('a');
		var areas = document.getElementsByTagName('area');

		// Loop through all anchor tags...
		for (var i=0; i<anchors.length; i++){
			var anchor = $(anchors[i]);
			
			var relAttribute = String(anchor.readAttribute('rel'));
			
			// Use the string.match() method to catch 'lightbox' references in the rel attribute
			if (anchor.readAttribute('href') && (relAttribute.toLowerCase().match('lightbox'))){
				anchor.onclick = function () {	myLightbox.start($(this)); return false;}
			}
		}

		// Loop through all area tags...
		// ToDo: Combine anchor & area tag loops
		for (var i=0; i< areas.length; i++){
			var area = $(areas[i]);
			
			var relAttribute = String(area.readAttribute('rel'));
			
			// Use the string.match() method to catch 'lightbox' references in the rel attribute...
			if (area.readAttribute('href') && (relAttribute.toLowerCase().match('lightbox'))){
				
				area.onclick = function () {myLightbox.start($(this)); return false;}
			}
		}
	},
	
	
	//
	//	start()
	//	Display overlay and lightbox. If element is part of a set, add siblings to elementArray.
	//
	start: function(elementLink) {	

		hideSelectBoxes();		
		hideFlash();
		
		// stretch overlay to fill page and fade in
		var arrayPageSize = getPageSize();
		Element.setWidth('overlay', arrayPageSize[0]);
		Element.setHeight('overlay', arrayPageSize[1]);
		
		// Again, if the user is on a Mac and has a Mozilla flavor browser, we can't use dynamically generated opacity or you will experience problems with the Flash movies.
		if (userAgent.indexOf('mac') != -1 && userAgent.indexOf('firefox')!=-1 || userAgent.indexOf('camino')!=-1) {
			Element.show('overlay');
		}else{ // Non-Mac, carry on as usual.
			new Effect.Appear('overlay', { duration: overlayDuration, from: 0.0, to: overlayOpacity });
		}

		elementArray = [];
		elementNum = 0;		

		if (!document.getElementsByTagName){ return; }
		var anchors = document.getElementsByTagName(elementLink.tagName);
		
				
		// If the image or SWF is not a part of the set...
		if((elementLink.readAttribute('rel') == 'lightbox')){
			// Add single image or SWF to elementArray
			
			if(elementLink.readAttribute('href').endsWith('swf') || ((elementLink.readAttribute('tag')!=null) && (elementLink.readAttribute('tag').toUpperCase() == 'SWF'))) { // SWF
				elementArray.push(new Array(elementLink.readAttribute('href'), elementLink.readAttribute('title'), elementLink.readAttribute('width'), elementLink.readAttribute('height')));
			} else { // Image
				elementArray.push(new Array(elementLink.readAttribute('href'), elementLink.readAttribute('title'),'IMG'));
			}				
		} else {
		// Image or SWF is not part of a set...
				
			// Loop through anchors, find other images/SWF's in the set, and add them to elementArray
			for (var i=0; i<anchors.length; i++){
				var anchor = $(anchors[i]);
			
				if (anchor.readAttribute('href') && (anchor.readAttribute('rel') == elementLink.readAttribute('rel'))){
					if(anchor.readAttribute('href').endsWith('swf') || ((elementLink.readAttribute('tag')!=null) && (elementLink.readAttribute('tag').toUpperCase() == 'SWF'))) { // SWF
					
						elementArray.push(new Array(anchor.readAttribute('href'), anchor.readAttribute('title'), anchor.readAttribute('width'), anchor.readAttribute('height')));
					} else {
						elementArray.push(new Array(anchor.readAttribute('href'), anchor.readAttribute('title'),'IMG'));
					}
				}
			}
			elementArray.removeDuplicates();
			while(elementArray[elementNum][0] != elementLink.readAttribute('href')) { elementNum++;}
		}

		// Calculate top and left offset for the lightbox 
		var arrayPageScroll = getPageScroll();
		var lightboxTop = arrayPageScroll[1] + (arrayPageSize[3] / 10);
		var lightboxLeft = arrayPageScroll[0];
		Element.setTop('lightbox', lightboxTop);
		Element.setLeft('lightbox', lightboxLeft);
	
		
		Element.show('lightbox');
		
		this.changeElement(elementNum);
	},

	//
	//	changeElement()
	//	Hide most elements and preload image in preparation for resizing the element container.
	//
	changeElement: function(elementNum) {	
		
		activeElement = elementNum;	// Update global var

		// Hide elements during transition
		if(animate){ Element.show('loading');}
		Element.hide('lightboximage');
		Element.hide('lightboxXL');
		Element.hide('hoverNav');
		Element.hide('prevLink');
		Element.hide('nextLink');
		Element.hide('imageDataContainer');
		Element.hide('numberDisplay');		
		
		if (elementArray[activeElement][0].endsWith('swf') || (elementArray[activeElement][2]!='IMG') ){ // SWF

			// No preloading needed here, if a preloader is needed for the Flash movie, it should be built into said Flash movie.
			myLightbox.resizeElementContainer(parseInt(elementArray[activeElement][2]), parseInt(elementArray[activeElement][3]))
		}else{

			imgPreloader = new Image();
		
			// Once image is preloaded, resize image container
			imgPreloader.onload=function(){
				Element.setSrc('lightboximage', elementArray[activeElement][0]);
				myLightbox.resizeElementContainer(imgPreloader.width, imgPreloader.height);
			
				imgPreloader.onload=function(){};	//	Clear onLoad, IE behaves irratically with animated gifs otherwise 
			}
			imgPreloader.src = elementArray[activeElement][0];
		}
	},

	//
	//	resizeElementContainer()
	//
	resizeElementContainer: function(elementWidth, elementHeight) {

		// Get current width and height
		this.widthCurrent = Element.getWidth('outerImageContainer');
		this.heightCurrent = Element.getHeight('outerImageContainer');

		// Get new width and height
		var widthNew = (elementWidth  + (borderSize * 2));
		var heightNew = (elementHeight  + (borderSize * 2));

		// Scalars based on change from old to new
		this.xScale = ( widthNew / this.widthCurrent) * 100;
		this.yScale = ( heightNew / this.heightCurrent) * 100;

		// Calculate size difference between new and old image, and resize if necessary
		wDiff = this.widthCurrent - widthNew;
		hDiff = this.heightCurrent - heightNew;

		if(!( hDiff == 0)){ new Effect.Scale('outerImageContainer', this.yScale, {scaleX: false, duration: resizeDuration, queue: 'front'}); }
		if(!( wDiff == 0)){ new Effect.Scale('outerImageContainer', this.xScale, {scaleY: false, delay: resizeDuration, duration: resizeDuration}); }

		// If new and old image/SWF are same size and no scaling transition is necessary, 
		// Do a quick pause to prevent image flicker.
		if((hDiff == 0) && (wDiff == 0)){
			if (navigator.appVersion.indexOf("MSIE")!=-1){ pause(250); } else { pause(100);} 
		}

		//Element.setHeight('prevLink', elementHeight);
		//Element.setHeight('nextLink', elementHeight);
		//Element.setWidth('imageDataContainer', widthNew);
		
		Element.setWidth('imageDataContainer', widthNew);
		
		this.showElement();
	},
	
	//
	//	showElement()
	//	Display image or SWF and begin preloading neighbors (if image).
	//
	showElement: function(){
		
		if(elementArray[activeElement][0].endsWith('swf') || (elementArray[activeElement][2]!='IMG')) { // SWF
			
			// Since the activeElement is a SWF, hide the image element.
			$('lightboximage').style.display = 'none';
			// Setup the proper object and embed tags in the SWF div.  The following is a basic set of object and embed tags, edit as you see fit.
			var obj = $('lightboxXL');
			
			obj.innerHTML = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\" width=\""+elementArray[activeElement][2]+"\" height=\""+elementArray[activeElement][3]+"\">" +
				"<param name=\"movie\" value=\""+elementArray[activeElement][0]+"\"/>" +
				"<param name=\"quality\" value=\"high\" />" +
				"<param name=\"allowscriptaccess\" value=\"always\" />" +
				"<param name=\"wmode\" value=\"transparent\" />" +
				"<embed wmode=\"transparent\" allowscriptaccess=\"always\" src=\""+elementArray[activeElement][0]+"\" quality=\"high\"  pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\""+elementArray[activeElement][2]+"\" height=\""+elementArray[activeElement][3]+"\">" +
				"</embed>" +
				"</object>";
			
			new Effect.Appear('lightboxXL', {  duration: resizeDuration, queue: 'end', from: 0.0, to: 100, afterFinish: function(){Element.hide('loading'); myLightbox.updateDetails(); } });
			
		} else { // Image
			Element.hide('loading');
			// Since the activeElement is an image, hide the SWF element.
			$('lightboxXL').style.display = 'none';
			new Effect.Appear('lightboximage', { duration: resizeDuration, queue: 'end', afterFinish: function(){ myLightbox.updateDetails(); } });
			this.preloadNeighborImages();
		}
	},

	//
	//	updateDetails()
	//	Display caption, image/SWF number, and bottom nav.
	//
	updateDetails: function() {
		// if caption is not null
		if(elementArray[activeElement][1]){
			Element.show('caption');
			Element.setInnerHTML( 'caption', elementArray[activeElement][1]);
		}
		
		// if image/SWF is part of set display 'Image/Movie x of x' 
		if(elementArray.length > 1){
			Element.show('numberDisplay');
			if(elementArray[activeElement][0].endsWith('swf') || (elementArray[activeElement][2]!='IMG')) { // SWF
				Element.setInnerHTML( 'numberDisplay', "Movie " + eval(activeElement + 1) + " of " + elementArray.length);
			}else{ // Image
				Element.setInnerHTML( 'numberDisplay', "Image " + eval(activeElement + 1) + " of " + elementArray.length);			
			}
		}

		new Effect.Parallel(
			[ new Effect.SlideDown( 'imageDataContainer', { sync: true, duration: resizeDuration, from: 0.0, to: 1.0 }), 
			  new Effect.Appear('imageDataContainer', { sync: true, duration: resizeDuration }) ], 
			{ duration: resizeDuration, afterFinish: function() {
				// Update overlay size and update nav
				var arrayPageSize = getPageSize();
				Element.setHeight('overlay', arrayPageSize[1]);
				myLightbox.updateNav();
				}
			} 
		);
	},

	//
	//	updateNav()
	//	Display appropriate previous and next hover navigation.
	//
	updateNav: function() {

		Element.show('hoverNav');				

		// If not first image in set, display prev image button
		if(activeElement != 0){
			Element.show('prevLink');
			document.getElementById('prevLink').onclick = function() {
				myLightbox.changeElement(activeElement - 1); return false;
			}
		}

		// If not last image in set, display next image button
		if(activeElement != (elementArray.length - 1)){
			Element.show('nextLink');
			document.getElementById('nextLink').onclick = function() {
				myLightbox.changeElement(activeElement + 1); return false;
			}
		}
		
		this.enableKeyboardNav();
	},

	//
	//	enableKeyboardNav()
	//
	enableKeyboardNav: function() {
		document.onkeydown = this.keyboardAction; 
	},

	//
	//	disableKeyboardNav()
	//
	disableKeyboardNav: function() {
		document.onkeydown = '';
	},

	//
	//	keyboardAction()
	//
	keyboardAction: function(e) {
		if (e == null) { // ie
			keycode = event.keyCode;
			escapeKey = 27;
		} else { // Mozilla
			keycode = e.keyCode;
			escapeKey = e.DOM_VK_ESCAPE;
		}

		key = String.fromCharCode(keycode).toLowerCase();
		
		if((key == 'x') || (key == 'o') || (key == 'c') || (keycode == escapeKey)){	// Close lightbox
			myLightbox.end();
		} else if((key == 'p') || (keycode == 37)){	// Display previous image/SWF
			if(activeElement != 0){
				myLightbox.disableKeyboardNav();
				myLightbox.changeElement(activeElement - 1);
			}
		} else if((key == 'n') || (keycode == 39)){	// Display next image/SWF
			if(activeElement != (elementArray.length - 1)){
				myLightbox.disableKeyboardNav();
				myLightbox.changeElement(activeElement + 1);
			}
		}

	},

	//
	//	preloadNeighborImages()
	//	Preload previous and next images.
	//
	preloadNeighborImages: function(){

		if((elementArray.length - 1) > activeElement){
			preloadNextImage = new Image();
			preloadNextImage.src = elementArray[activeElement + 1][0];
		}
		if(activeElement > 0){
			preloadPrevImage = new Image();
			preloadPrevImage.src = elementArray[activeElement - 1][0];
		}
	
	},

	//
	//	end()
	//
	end: function() {
		this.disableKeyboardNav();
		var obj = $('lightboxXL');
		obj.innerHTML = "";
		Element.hide('lightbox');
		new Effect.Fade('overlay', { duration: overlayDuration});
		showSelectBoxes();
		showFlash();
	}
}

//
// getPageScroll()
// Returns array with x,y page scroll values.
// Core code from - quirksmode.com
//
function getPageScroll(){

	var xScroll, yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
		xScroll = self.pageXOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
		xScroll = document.documentElement.scrollLeft;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
		xScroll = document.body.scrollLeft;	
	}

	arrayPageScroll = new Array(xScroll,yScroll) 
	return arrayPageScroll;
}

//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.com
// Edit for Firefox by pHaez
//
function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = window.innerWidth + window.scrollMaxX;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	
//	console.log(self.innerWidth);
//	console.log(document.documentElement.clientWidth);

	if (self.innerHeight) {	// all except Explorer
		if(document.documentElement.clientWidth){
			windowWidth = document.documentElement.clientWidth; 
		} else {
			windowWidth = self.innerWidth;
		}
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// For small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

//	console.log("xScroll " + xScroll)
//	console.log("windowWidth " + windowWidth)

	// For small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = xScroll;		
	} else {
		pageWidth = windowWidth;
	}
//	console.log("pageWidth " + pageWidth)

	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}

//
// getKey(key)
// Gets keycode. If 'x' is pressed then it hides the lightbox.
//
function getKey(e){
	if (e == null) { // ie
		keycode = event.keyCode;
	} else { // mozilla
		keycode = e.which;
	}
	key = String.fromCharCode(keycode).toLowerCase();
	
	if(key == 'x'){
	}
}

// -----------------------------------------------------------------------------------

//
// listenKey()
//
function listenKey () {	document.onkeypress = getKey; }
	
// ---------------------------------------------------

function showSelectBoxes(){
	var selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
		selects[i].style.visibility = "visible";
	}
}

// ---------------------------------------------------

function hideSelectBoxes(){
	var selects = document.getElementsByTagName("select");
	for (i = 0; i != selects.length; i++) {
		selects[i].style.visibility = "hidden";
	}
}

// ---------------------------------------------------

function showFlash(){
	var flashObjects = document.getElementsByTagName("object");
	for (i = 0; i < flashObjects.length; i++) {
		flashObjects[i].style.visibility = "visible";
	}

	var flashEmbeds = document.getElementsByTagName("embed");
	for (i = 0; i < flashEmbeds.length; i++) {
		flashEmbeds[i].style.visibility = "visible";
	}
}

// ---------------------------------------------------

function hideFlash(){
	var flashObjects = document.getElementsByTagName("object");
	for (i = 0; i < flashObjects.length; i++) {
		flashObjects[i].style.visibility = "hidden";
	}

	var flashEmbeds = document.getElementsByTagName("embed");
	for (i = 0; i < flashEmbeds.length; i++) {
		flashEmbeds[i].style.visibility = "hidden";
	}

}

//
// pause(numberMillis)
// Pauses code execution for specified time. Uses busy code, not good.
// Help from Ran Bar-On [ran2103@gmail.com]
//

function pause(ms){
	var date = new Date();
	curDate = null;
	do{var curDate = new Date();}
	while( curDate - date < ms);
}
/*
function pause(numberMillis) {
	var curently = new Date().getTime() + sender;
	while (new Date().getTime();	
}
*/
// ---------------------------------------------------
function getWindowWidth(){
		return (window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth || 0);
	}
function getWindowHeight(){
		return (window.innerHeight ||  document.documentElement.clientHeight || document.body.clientHeight || 0);
	}
function getDocumentWidth(){
		return Math.min(document.body.scrollWidth,this.getWindowWidth());
	}
function getDocumentHeight(){
		return Math.max(document.body.scrollHeight,this.getWindowHeight());
	}
function fixPng() {
	if (Prototype.Browser.IE){
		var images = document.getElementsByTagName("img");
		if (images){

			for (var i = 0; i < images.length; i++){
				if ((images[i].src.indexOf(".png")) != -1){
					var srcname = images[i].src.replace(new RegExp('(.*)\/(.*)?\.png'),"$2");
					images[i].parentNode.style.display = "inline-block";
					images[i].style.visibility = "hidden";
					images[i].style.marginTop = "0";
					images[i].parentNode.style.filter = "progid:dximagetransform.microsoft.alphaimageloader(src='images/"+ srcname +".png',sizingmethod='crop');";
				}
			}
		}
	}
}
function center (element,centerX,centerY,toParent){
		var self = this;
		if(!element.getStyle('position')!='absolute'){
			element.setStyle({
				position: 'absolute'
			}); 
		}
		var dimensions,windowWidth,windowHeight;
		if (toParent)
		{
			dimensions		= element.getDimensions();
			windowWidth		= $(element.parentNode).getDimensions().width;
			windowHeight	= $(element.parentNode).getDimensions().height;
		}
		else
		{
			dimensions 	= element.getDimensions();
			windowWidth	= getWindowWidth();
			windowHeight= getWindowHeight();
		}
		var docWidth	= getDocumentWidth();
		var docHeight	= getDocumentHeight();
		Position.prepare();
		var offset_left = (Position.deltaX + Math.floor((windowWidth - dimensions.width) / 2));
		var offset_top = (Position.deltaY + ((windowHeight > dimensions.height) ? Math.floor((windowHeight - dimensions.height) / 2) : 0));
		if (centerX)
			element.setStyle({left: ((dimensions.width <= docWidth) ? ((offset_left != null && offset_left > 0) ? offset_left : '0') + 'px' : 0),position:'absolute'});
		if (centerY)
			element.setStyle({top: ((dimensions.height <= docHeight) ? ((offset_top != null && offset_top > 0) ? offset_top : '0') + 'px' : 0),position:'absolute'});
		
	}
// ---------------------------------------------------
function initLightbox() { myLightbox = new Lightbox(); }
Event.observe(window, 'load', initLightbox, false);