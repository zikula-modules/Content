/*! For license information please see gridstack-static.js.LICENSE.txt */
!function(t,e){"object"==typeof exports&&"object"==typeof module?module.exports=e():"function"==typeof define&&define.amd?define([],e):"object"==typeof exports?exports.GridStack=e():t.GridStack=e()}(self,(function(){return(()=>{"use strict";var t={334:(t,e)=>{Object.defineProperty(e,"__esModule",{value:!0});class i{static registerPlugin(t){return i.ddi=new t,i.ddi}static get(){return i.ddi||i.registerPlugin(i)}remove(t){return this}}e.GridStackDDI=i},62:(t,e,i)=>{Object.defineProperty(e,"__esModule",{value:!0});const s=i(593);class o{constructor(t={}){this.addedNodes=[],this.removedNodes=[],this.getGridHeight=s.obsolete(this,o.prototype.getRow,"getGridHeight","getRow","v1.0.0"),this.column=t.column||12,this.onChange=t.onChange,this._float=t.float,this.maxRow=t.maxRow,this.nodes=t.nodes||[]}batchUpdate(){return this.batchMode||(this.batchMode=!0,this._prevFloat=this._float,this._float=!0),this}commit(){return this.batchMode?(this.batchMode=!1,this._float=this._prevFloat,delete this._prevFloat,this._packNodes(),this._notify(),this):this}_fixCollisions(t){this._sortNodes(-1);let e=t,i=Boolean(this.nodes.find((t=>t.locked)));for(this.float||i||(e={x:0,y:t.y,w:this.column,h:t.h});;){let i,o=this.nodes.find((i=>i!==t&&s.Utils.isIntercepted(i,e)),{node:t,nn:e});if(!o)return this;if(i=o.locked?this.moveNode(t,t.x,o.y+o.h,t.w,t.h,!0):this.moveNode(o,o.x,t.y+t.h,o.w,o.h,!0),!i)return this}}isAreaEmpty(t,e,i,o){let n={x:t||0,y:e||0,w:i||1,h:o||1};return!this.nodes.find((t=>s.Utils.isIntercepted(t,n)))}compact(){if(0===this.nodes.length)return this;this.batchUpdate(),this._sortNodes();let t=this.nodes;return this.nodes=[],t.forEach((t=>{t.noMove||t.locked||(t.autoPosition=!0),this.addNode(t,!1),t._dirty=!0})),this.commit(),this}set float(t){this._float!==t&&(this._float=t||!1,t||(this._packNodes(),this._notify()))}get float(){return this._float||!1}_sortNodes(t){return this.nodes=s.Utils.sort(this.nodes,t,this.column),this}_packNodes(){return this._sortNodes(),this.float?this.nodes.forEach(((t,e)=>{if(t._updating||void 0===t._packY||t.y===t._packY)return this;let i=t.y;for(;i>=t._packY;){let o={x:t.x,y:i,w:t.w,h:t.h};this.nodes.slice(0,e).find((t=>s.Utils.isIntercepted(o,t)),{n:t,newY:i})||(t._dirty=!0,t.y=i),--i}})):this.nodes.forEach(((t,e)=>{if(t.locked)return this;for(;t.y>0;){let i=t.y-1,o=0===e,n={x:t.x,y:i,w:t.w,h:t.h};if(e>0&&(o=void 0===this.nodes.slice(0,e).find((t=>s.Utils.isIntercepted(n,t)),{n:t,newY:i})),!o)break;t._dirty=t.y!==i,t.y=i}})),this}prepareNode(t,e){(t=t||{})._id=t._id||o._idSeq++,void 0!==t.x&&void 0!==t.y&&null!==t.x&&null!==t.y||(t.autoPosition=!0);let i={x:0,y:0,w:1,h:1};return s.Utils.defaults(t,i),t.autoPosition||delete t.autoPosition,t.noResize||delete t.noResize,t.noMove||delete t.noMove,"string"==typeof t.x&&(t.x=Number(t.x)),"string"==typeof t.y&&(t.y=Number(t.y)),"string"==typeof t.w&&(t.w=Number(t.w)),"string"==typeof t.h&&(t.h=Number(t.h)),isNaN(t.x)&&(t.x=i.x,t.autoPosition=!0),isNaN(t.y)&&(t.y=i.y,t.autoPosition=!0),isNaN(t.w)&&(t.w=i.w),isNaN(t.h)&&(t.h=i.h),t.maxW&&(t.w=Math.min(t.w,t.maxW)),t.maxH&&(t.h=Math.min(t.h,t.maxH)),t.minW&&(t.w=Math.max(t.w,t.minW)),t.minH&&(t.h=Math.max(t.h,t.minH)),t.w>this.column?t.w=this.column:t.w<1&&(t.w=1),this.maxRow&&t.h>this.maxRow?t.h=this.maxRow:t.h<1&&(t.h=1),t.x<0&&(t.x=0),t.y<0&&(t.y=0),t.x+t.w>this.column&&(e?t.w=this.column-t.x:t.x=this.column-t.w),this.maxRow&&t.y+t.h>this.maxRow&&(e?t.h=this.maxRow-t.y:t.y=this.maxRow-t.h),t}getDirtyNodes(t){if(t){let t=[];return this.nodes.forEach((e=>{e._dirty&&(e.y===e._origY&&e.x===e._origX&&e.w===e._origW&&e.h===e._origH?delete e._dirty:t.push(e))})),t}return this.nodes.filter((t=>t._dirty))}_notify(t,e=!0){if(this.batchMode)return this;let i=(t=void 0===t?[]:Array.isArray(t)?t:[t]).concat(this.getDirtyNodes());return this.onChange&&this.onChange(i,e),this}cleanNodes(){return this.batchMode||this.nodes.forEach((t=>{delete t._dirty})),this}addNode(t,e=!1){if((t=this.prepareNode(t)).autoPosition){this._sortNodes();for(let e=0;;++e){let i=e%this.column,o=Math.floor(e/this.column);if(i+t.w>this.column)continue;let n={x:i,y:o,w:t.w,h:t.h};if(!this.nodes.find((t=>s.Utils.isIntercepted(n,t)),{x:i,y:o,node:t})){t.x=i,t.y=o,delete t.autoPosition;break}}}return this.nodes.push(t),e&&this.addedNodes.push(t),this._fixCollisions(t),this._packNodes(),this._notify(),t}removeNode(t,e=!0,i=!1){return i&&this.removedNodes.push(t),t._id=null,this.nodes=this.nodes.filter((e=>e!==t)),this.float||this._packNodes(),this._notify(t,e),this}removeAll(t=!0){return delete this._layouts,0===this.nodes.length||(t&&this.nodes.forEach((t=>{t._id=null})),this.removedNodes=this.nodes,this.nodes=[],this._notify(this.removedNodes,t)),this}canMoveNode(t,e,i,s,n){if(!this.isNodeChangedPosition(t,e,i,s,n))return!1;let r,l=this.nodes.some((t=>t.locked));if(!this.maxRow&&!l)return!0;let h=new o({column:this.column,float:this.float,nodes:this.nodes.map((e=>e===t?(r=Object.assign({},e),r):Object.assign({},e)))});if(!r)return!0;h.moveNode(r,e,i,s,n);let a=!0;return l&&(a=!h.nodes.some((t=>t.locked&&t._dirty&&t!==r))),this.maxRow&&a&&(a=h.getRow()<=this.maxRow),a}willItFit(t){if(!this.maxRow)return!0;let e=new o({column:this.column,float:this.float,nodes:this.nodes.map((t=>Object.assign({},t)))});return e.addNode(t),e.getRow()<=this.maxRow}isNodeChangedPosition(t,e,i,s,o){return"number"!=typeof e&&(e=t.x),"number"!=typeof i&&(i=t.y),"number"!=typeof s&&(s=t.w),"number"!=typeof o&&(o=t.h),t.maxW&&(s=Math.min(s,t.maxW)),t.maxH&&(o=Math.min(o,t.maxH)),t.minW&&(s=Math.max(s,t.minW)),t.minH&&(o=Math.max(o,t.minH)),t.x!==e||t.y!==i||t.w!==s||t.h!==o}moveNode(t,e,i,s,o,n){if(t.locked)return null;"number"!=typeof e&&(e=t.x),"number"!=typeof i&&(i=t.y),"number"!=typeof s&&(s=t.w),"number"!=typeof o&&(o=t.h);let r=t.w!==s||t.h!==o,l={x:e,y:i,w:s,h:o,maxW:t.maxW,maxH:t.maxH,minW:t.minW,minH:t.minH};return l=this.prepareNode(l,r),t.x===l.x&&t.y===l.y&&t.w===l.w&&t.h===l.h?null:(t._dirty=!0,t.x=t._lastTriedX=l.x,t.y=t._lastTriedY=l.y,t.w=t._lastTriedW=l.w,t.h=t._lastTriedH=l.h,this._fixCollisions(t),n||(this._packNodes(),this._notify()),t)}getRow(){return this.nodes.reduce(((t,e)=>Math.max(t,e.y+e.h)),0)}beginUpdate(t){return t._updating||(t._updating=!0,this.nodes.forEach((t=>{t._packY=t.y}))),this}endUpdate(){let t=this.nodes.find((t=>t._updating));return t&&(delete t._updating,this.nodes.forEach((t=>{delete t._packY}))),this}save(t=!0){let e=[];return s.Utils.sort(this.nodes),this.nodes.forEach((i=>{let s={};for(let t in i)"_"!==t[0]&&null!==i[t]&&void 0!==i[t]&&(s[t]=i[t]);t||delete s.el,delete s.grid,s.autoPosition||delete s.autoPosition,s.noResize||delete s.noResize,s.noMove||delete s.noMove,s.locked||delete s.locked,e.push(s)})),e}layoutsNodesChange(t){return!this._layouts||this._ignoreLayoutsNodeChange||this._layouts.forEach(((e,i)=>{if(!e||i===this.column)return this;i<this.column?this._layouts[i]=void 0:t.forEach((t=>{let s=e.find((e=>e._id===t._id));if(!s)return this;let o=i/this.column;t.y!==t._origY&&(s.y+=t.y-t._origY),t.x!==t._origX&&(s.x=Math.round(t.x*o)),t.w!==t._origW&&(s.w=Math.round(t.w*o))}))})),this}updateNodeWidths(t,e,i,o="moveScale"){if(!this.nodes.length||t===e)return this;if(this.cacheLayout(this.nodes,t),1===e&&i&&i.length){let t=0;i.forEach((e=>{e.x=0,e.w=1,e.y=Math.max(e.y,t),t=e.y+e.h}))}else i=s.Utils.sort(this.nodes,-1,t);let n=this._layouts[e]||[],r=this._layouts.length-1;0===n.length&&e>t&&e<r&&(n=this._layouts[r]||[],n.length&&(t=r,n.forEach((t=>{let e=i.findIndex((e=>e._id===t._id));-1!==e&&(i[e].x=t.x,i[e].y=t.y,i[e].w=t.w)})),n=[]));let l=[];if(n.forEach((t=>{let e=i.findIndex((e=>e._id===t._id));-1!==e&&(i[e].x=t.x,i[e].y=t.y,i[e].w=t.w,l.push(i[e]),i.splice(e,1))})),i.length)if("function"==typeof o)o(e,t,l,i);else{let s=e/t,n="move"===o||"moveScale"===o,r="scale"===o||"moveScale"===o;i.forEach((i=>{i.x=1===e?0:n?Math.round(i.x*s):Math.min(i.x,e-1),i.w=1===e||1===t?1:r?Math.round(i.w*s)||1:Math.min(i.w,e),l.push(i)})),i=[]}return l=s.Utils.sort(l,-1,e),this._ignoreLayoutsNodeChange=!0,this.batchUpdate(),this.nodes=[],l.forEach((t=>{this.addNode(t,!1),t._dirty=!0}),this),this.commit(),delete this._ignoreLayoutsNodeChange,this}saveInitial(){return this.nodes.forEach((t=>{t._origX=t.x,t._origY=t.y,t._origW=t.w,t._origH=t.h,delete t._dirty})),this}cacheLayout(t,e,i=!1){let s=[];return t.forEach(((t,e)=>{t._id=t._id||o._idSeq++,s[e]={x:t.x,y:t.y,w:t.w,_id:t._id}})),this._layouts=i?[]:this._layouts||[],this._layouts[e]=s,this}cleanupNode(t){for(let e in t)"_"===e[0]&&delete t[e];return this}}e.GridStackEngine=o,o._idSeq=1},270:(t,e,i)=>{function s(t){for(var i in t)e.hasOwnProperty(i)||(e[i]=t[i])}Object.defineProperty(e,"__esModule",{value:!0});const o=i(62),n=i(593),r=i(334);s(i(593)),s(i(62)),s(i(334));const l={column:12,minRow:0,maxRow:0,itemClass:"grid-stack-item",placeholderClass:"grid-stack-placeholder",placeholderText:"",handle:".grid-stack-item-content",handleClass:null,styleInHead:!1,cellHeight:"auto",margin:10,auto:!0,minWidth:768,float:!1,staticGrid:!1,animate:!0,alwaysShowResizeHandle:!1,resizable:{autoHide:!0,handles:"se"},draggable:{handle:".grid-stack-item-content",scroll:!1,appendTo:"body"},dragIn:void 0,dragInOptions:{revert:"invalid",handle:".grid-stack-item-content",scroll:!1,appendTo:"body"},disableDrag:!1,disableResize:!1,rtl:"auto",removable:!1,removableOptions:{accept:".grid-stack-item"},removeTimeout:2e3,marginUnit:"px",cellHeightUnit:"px",disableOneColumnMode:!1,oneColumnModeDomSort:!1};class h{constructor(t,e={}){this._gsEventHandler={},this.el=t,e=e||{},n.obsoleteOpts(e,"verticalMargin","margin","v2.0"),n.obsoleteAttr(this.el,"data-gs-current-height","gs-current-row","v1.0.0"),e.row&&(e.minRow=e.maxRow=e.row,delete e.row);let i=n.Utils.toNumber(t.getAttribute("gs-row")),s=Object.assign(Object.assign({},l),{column:n.Utils.toNumber(t.getAttribute("gs-column"))||12,minRow:i||n.Utils.toNumber(t.getAttribute("gs-min-row"))||0,maxRow:i||n.Utils.toNumber(t.getAttribute("gs-max-row"))||0,staticGrid:n.Utils.toBool(t.getAttribute("gs-static"))||!1,_styleSheetClass:"grid-stack-instance-"+(1e4*Math.random()).toFixed(0),alwaysShowResizeHandle:e.alwaysShowResizeHandle||!1,resizable:{autoHide:!e.alwaysShowResizeHandle,handles:"se"},draggable:{handle:(e.handleClass?"."+e.handleClass:e.handle?e.handle:"")||".grid-stack-item-content",scroll:!1,appendTo:"body"},removableOptions:{accept:"."+(e.itemClass||"grid-stack-item")}});t.getAttribute("gs-animate")&&(s.animate=n.Utils.toBool(t.getAttribute("gs-animate"))),this.opts=n.Utils.defaults(e,s),e=null,this.initMargin(),"auto"===this.opts.rtl&&(this.opts.rtl="rtl"===t.style.direction),this.opts.rtl&&this.el.classList.add("grid-stack-rtl");let r=n.Utils.closestByClass(this.el,l.itemClass);if(r&&r.gridstackNode&&(this.opts._isNested=r.gridstackNode,this.opts._isNested.subGrid=this,this.el.classList.add("grid-stack-nested")),this._isAutoCellHeight="auto"===this.opts.cellHeight,this._isAutoCellHeight){let t=-this.opts.marginRight-this.opts.marginLeft+this.opts.marginTop+this.opts.marginBottom;this.cellHeight(this.cellWidth()+t,!1)}else this.cellHeight(this.opts.cellHeight,!1);if(this.el.classList.add(this.opts._styleSheetClass),this._setStaticClass(),this.engine=new o.GridStackEngine({column:this.opts.column,float:this.opts.float,maxRow:this.opts.maxRow,onChange:(t,e=!0)=>{let i=0;this.engine.nodes.forEach((t=>{i=Math.max(i,t.y+t.h)})),t.forEach((t=>{let i=t.el;e&&null===t._id?i&&i.parentNode&&i.parentNode.removeChild(i):this._writePosAttr(i,t.x,t.y,t.w,t.h)})),this._updateStyles(!1,i)}}),this.opts.auto){this.batchUpdate();let t=[];this.getGridItems().forEach((e=>{let i=parseInt(e.getAttribute("gs-x")),s=parseInt(e.getAttribute("gs-y"));t.push({el:e,i:(Number.isNaN(i)?1e3:i)+(Number.isNaN(s)?1e3:s)*this.opts.column})})),t.sort((t=>t.i)).forEach((t=>this._prepareElement(t.el))),this.commit()}this.engine.saveInitial(),this.setAnimation(this.opts.animate);let h=document.createElement("div");h.className="placeholder-content",h.innerHTML=this.opts.placeholderText,this.placeholder=document.createElement("div"),this.placeholder.classList.add(this.opts.placeholderClass,s.itemClass,this.opts.itemClass),this.placeholder.appendChild(h),this._updateStyles(),this._setupDragIn(),this._setupRemoveDrop(),this._setupAcceptWidget(),this._updateWindowResizeEvent()}static init(t={},e=".grid-stack"){let i=h.getGridElement(e);return i?(i.gridstack||(i.gridstack=new h(i,Object.assign({},t))),i.gridstack):("string"==typeof e?console.error('GridStack.initAll() no grid was found with selector "'+e+'" - element missing or wrong selector ?\nNote: ".grid-stack" is required for proper CSS styling and drag/drop, and is the default selector.'):console.error("GridStack.init() no grid element was passed."),null)}static initAll(t={},e=".grid-stack"){let i=[];return h.getGridElements(e).forEach((e=>{e.gridstack||(e.gridstack=new h(e,Object.assign({},t))),i.push(e.gridstack)})),0===i.length&&console.error('GridStack.initAll() no grid was found with selector "'+e+'" - element missing or wrong selector ?\nNote: ".grid-stack" is required for proper CSS styling and drag/drop, and is the default selector.'),i}static addGrid(t,e={}){if(!t)return null;let i=document.implementation.createHTMLDocument();i.body.innerHTML=`<div class="grid-stack ${e.class||""}"></div>`;let s=i.body.children[0];t.append(s);let o=h.init(e,s);return e.children&&o.load(e.children),o}addWidget(t,e){if(arguments.length>2){console.warn("gridstack.ts: `addWidget(el, x, y, width...)` is deprecated. Use `addWidget({x, y, w, content, ...})`. It will be removed soon");let e=arguments,i=1,s={x:e[i++],y:e[i++],w:e[i++],h:e[i++],autoPosition:e[i++],minW:e[i++],maxW:e[i++],minH:e[i++],maxH:e[i++],id:e[i++]};return this.addWidget(t,s)}let i;if("string"==typeof t){let e=document.implementation.createHTMLDocument();e.body.innerHTML=t,i=e.body.children[0]}else if(0===arguments.length||1===arguments.length&&(void 0!==(s=t).x||void 0!==s.y||void 0!==s.w||void 0!==s.h||void 0!==s.content)){let s=t&&t.content||"";e=t;let o=document.implementation.createHTMLDocument();o.body.innerHTML=`<div class="grid-stack-item ${this.opts.itemClass||""}"><div class="grid-stack-item-content">${s}</div></div>`,i=o.body.children[0]}else i=t;var s;let o=this._readAttr(i);return e=Object.assign({},e||{}),n.Utils.defaults(e,o),this.engine.prepareNode(e),this._writeAttr(i,e),this._insertNotAppend?this.el.prepend(i):this.el.appendChild(i),this._prepareElement(i,!0,e),this._updateContainerHeight(),this._triggerAddEvent(),this._triggerChangeEvent(),i}save(t=!0,e=!1){let i=this.engine.save(t);if(t&&i.forEach((t=>{if(t.el){let e=t.el.querySelector(".grid-stack-item-content");t.content=e?e.innerHTML:void 0,t.content||delete t.content,delete t.el}})),e){i.forEach((e=>{e.subGrid&&(e.subGrid=e.subGrid.save(t,!0))}));let e=Object.assign({},this.opts);return e.marginBottom===e.marginTop&&e.marginRight===e.marginLeft&&e.marginTop===e.marginRight&&(e.margin=e.marginTop,delete e.marginTop,delete e.marginRight,delete e.marginBottom,delete e.marginLeft),e.rtl===("rtl"===this.el.style.direction)&&(e.rtl="auto"),this._isAutoCellHeight&&(e.cellHeight="auto"),n.Utils.removeInternalAndSame(e,l),e.children=i,e}return i}load(t,e=!0){let i=h.Utils.sort(t,-1,this._prevColumn||this.opts.column);this._insertNotAppend=!0,this._prevColumn&&this._prevColumn!==this.opts.column&&i.some((t=>t.x+t.w>this.opts.column))&&(this._ignoreLayoutsNodeChange=!0,this.engine.cacheLayout(i,this._prevColumn,!0));let s=[];return this.batchUpdate(),e&&[...this.engine.nodes].forEach((t=>{i.find((e=>t.id===e.id))||("function"==typeof e?e(this,t,!1):(s.push(t),this.removeWidget(t.el,!0,!1)))})),i.forEach((t=>{let i=t.id||0===t.id?this.engine.nodes.find((e=>e.id===t.id)):void 0;if(i){if(this.update(i.el,t),t.subGrid&&t.subGrid.children){let e=i.el.querySelector(".grid-stack");e&&e.gridstack&&(e.gridstack.load(t.subGrid.children),this._insertNotAppend=!0)}}else if(e&&(t="function"==typeof e?e(this,t,!0).gridstackNode:this.addWidget(t).gridstackNode).subGrid){let e=t.el.querySelector(".grid-stack-item-content");t.subGrid=h.addGrid(e,t.subGrid)}})),this.engine.removedNodes=s,this.commit(),delete this._ignoreLayoutsNodeChange,delete this._insertNotAppend,this}batchUpdate(){return this.engine.batchUpdate(),this}getCellHeight(t=!1){if(this.opts.cellHeight&&"auto"!==this.opts.cellHeight&&(!t||!this.opts.cellHeightUnit||"px"===this.opts.cellHeightUnit))return this.opts.cellHeight;let e=this.el.querySelector("."+this.opts.itemClass),i=n.Utils.toNumber(e.getAttribute("gs-h"));return Math.round(e.offsetHeight/i)}cellHeight(t,e=!0){let i=n.Utils.parseHeight(t);return this.opts.cellHeightUnit===i.unit&&this.opts.cellHeight===i.h||(this.opts.cellHeightUnit=i.unit,this.opts.cellHeight=i.h,e&&this._updateStyles(!0),this._resizeNestedGrids(this.el)),this}cellWidth(){return(this.el.offsetWidth||this.el.parentElement.offsetWidth||window.innerWidth)/this.opts.column}commit(){return this.engine.commit(),this._triggerRemoveEvent(),this._triggerAddEvent(),this._triggerChangeEvent(),this}compact(){return this.engine.compact(),this._triggerChangeEvent(),this}column(t,e="moveScale"){if(this.opts.column===t)return this;let i,s=this.opts.column;return 1===t?this._prevColumn=s:delete this._prevColumn,this.el.classList.remove("grid-stack-"+s),this.el.classList.add("grid-stack-"+t),this.opts.column=this.engine.column=t,1===t&&this.opts.oneColumnModeDomSort&&(i=[],this.getGridItems().forEach((t=>{t.gridstackNode&&i.push(t.gridstackNode)})),i.length||(i=void 0)),this.engine.updateNodeWidths(s,t,i,e),this._ignoreLayoutsNodeChange=!0,this._triggerChangeEvent(),delete this._ignoreLayoutsNodeChange,this}getColumn(){return this.opts.column}getGridItems(){return Array.from(this.el.children).filter((t=>t.matches("."+this.opts.itemClass)&&!t.matches("."+this.opts.placeholderClass)))}destroy(t=!0){if(this.el)return this._updateWindowResizeEvent(!0),this.setStatic(!0),t?this.el.parentNode.removeChild(this.el):(this.removeAll(t),this.el.classList.remove(this.opts._styleSheetClass)),this._removeStylesheet(),delete this.opts._isNested,delete this.opts,delete this.placeholder,delete this.engine,delete this.el.gridstack,delete this.el,this}disable(){if(!this.opts.staticGrid)return this.enableMove(!1),this.enableResize(!1),this._triggerEvent("disable"),this}enable(){if(!this.opts.staticGrid)return this.enableMove(!0),this.enableResize(!0),this._triggerEvent("enable"),this}enableMove(t,e=!0){return this.opts.staticGrid||(this.getGridItems().forEach((e=>this.movable(e,t))),e&&(this.opts.disableDrag=!t)),this}enableResize(t,e=!0){return this.opts.staticGrid||(this.getGridItems().forEach((e=>this.resizable(e,t))),e&&(this.opts.disableResize=!t)),this}float(t){return this.engine.float=t,this._triggerChangeEvent(),this}getFloat(){return this.engine.float}getCellFromPixel(t,e=!1){let i,s=this.el.getBoundingClientRect();i=e?{top:s.top+document.documentElement.scrollTop,left:s.left}:{top:this.el.offsetTop,left:this.el.offsetLeft};let o=t.left-i.left,n=t.top-i.top,r=s.width/this.opts.column,l=s.height/parseInt(this.el.getAttribute("gs-current-row"));return{x:Math.floor(o/r),y:Math.floor(n/l)}}getRow(){return Math.max(this.engine.getRow(),this.opts.minRow)}isAreaEmpty(t,e,i,s){return this.engine.isAreaEmpty(t,e,i,s)}makeWidget(t){let e=h.getElement(t);return this._prepareElement(e,!0),this._updateContainerHeight(),this._triggerAddEvent(),this._triggerChangeEvent(),e}on(t,e){if(-1!==t.indexOf(" "))return t.split(" ").forEach((t=>this.on(t,e))),this;if("change"===t||"added"===t||"removed"===t||"enable"===t||"disable"===t){let i="enable"===t||"disable"===t;this._gsEventHandler[t]=i?t=>e(t):t=>e(t,t.detail),this.el.addEventListener(t,this._gsEventHandler[t])}else"dragstart"===t||"dragstop"===t||"resizestart"===t||"resizestop"===t||"dropped"===t?this._gsEventHandler[t]=e:console.log("GridStack.on("+t+') event not supported, but you can still use $(".grid-stack").on(...) while jquery-ui is still used internally.');return this}off(t){return-1!==t.indexOf(" ")?(t.split(" ").forEach((t=>this.off(t))),this):("change"!==t&&"added"!==t&&"removed"!==t&&"enable"!==t&&"disable"!==t||this._gsEventHandler[t]&&this.el.removeEventListener(t,this._gsEventHandler[t]),delete this._gsEventHandler[t],this)}removeWidget(t,e=!0,i=!0){return h.getElements(t).forEach((t=>{if(t.parentElement!==this.el)return;let s=t.gridstackNode;s||(s=this.engine.nodes.find((e=>t===e.el))),s&&(delete t.gridstackNode,r.GridStackDDI.get().remove(t),this.engine.removeNode(s,e,i),e&&t.parentElement&&t.remove())})),i&&(this._triggerRemoveEvent(),this._triggerChangeEvent()),this}removeAll(t=!0){return this.engine.nodes.forEach((t=>{delete t.el.gridstackNode,r.GridStackDDI.get().remove(t.el)})),this.engine.removeAll(t),this._triggerRemoveEvent(),this}setAnimation(t){return t?this.el.classList.add("grid-stack-animate"):this.el.classList.remove("grid-stack-animate"),this}setStatic(t){return this.opts.staticGrid===t||(this.opts.staticGrid=t,this.engine.nodes.forEach((t=>this._prepareDragDropByNode(t))),this._setStaticClass()),this}update(t,e){if(arguments.length>2){console.warn("gridstack.ts: `update(el, x, y, w, h)` is deprecated. Use `update({x, w, content, ...})`. It will be removed soon");let i=arguments,s=1;return e={x:i[s++],y:i[s++],w:i[s++],h:i[s++]},this.update(t,e)}return h.getElements(t).forEach((t=>{if(!t||!t.gridstackNode)return;let i=t.gridstackNode,s=Object.assign({},e);delete s.autoPosition;let o,n=["x","y","w","h"];if(n.some((t=>void 0!==s[t]&&s[t]!==i[t]))&&(o={},n.forEach((t=>{o[t]=void 0!==s[t]?s[t]:i[t],delete s[t]}))),!o&&(s.minW||s.minH||s.maxW||s.maxH)&&(o={}),s.content){let e=t.querySelector(".grid-stack-item-content");e&&e.innerHTML!==s.content&&(e.innerHTML=s.content),delete s.content}let r=!1,l=!1;for(const t in s)"_"!==t[0]&&i[t]!==s[t]&&(i[t]=s[t],r=!0,l=l||!this.opts.staticGrid&&("noResize"===t||"noMove"===t||"locked"===t));o&&(this.engine.cleanNodes(),this.engine.beginUpdate(i),this.engine.moveNode(i,o.x,o.y,o.w,o.h),this._updateContainerHeight(),this._triggerChangeEvent(),this.engine.endUpdate()),r&&this._writeAttr(t,i),l&&this._prepareDragDropByNode(i)})),this}margin(t){if(!("string"==typeof t&&t.split(" ").length>1)){let e=n.Utils.parseHeight(t);if(this.opts.marginUnit===e.unit&&this.opts.margin===e.h)return}return this.opts.margin=t,this.opts.marginTop=this.opts.marginBottom=this.opts.marginLeft=this.opts.marginRight=void 0,this.initMargin(),this._updateStyles(!0),this}getMargin(){return this.opts.margin}willItFit(t){if(arguments.length>1){console.warn("gridstack.ts: `willItFit(x,y,w,h,autoPosition)` is deprecated. Use `willItFit({x, y,...})`. It will be removed soon");let t=arguments,e=0,i={x:t[e++],y:t[e++],w:t[e++],h:t[e++],autoPosition:t[e++]};return this.willItFit(i)}return this.engine.willItFit(t)}_triggerChangeEvent(){if(this.engine.batchMode)return this;let t=this.engine.getDirtyNodes(!0);return t&&t.length&&(this._ignoreLayoutsNodeChange||this.engine.layoutsNodesChange(t),this._triggerEvent("change",t)),this.engine.saveInitial(),this}_triggerAddEvent(){return this.engine.batchMode||this.engine.addedNodes&&this.engine.addedNodes.length>0&&(this._ignoreLayoutsNodeChange||this.engine.layoutsNodesChange(this.engine.addedNodes),this.engine.addedNodes.forEach((t=>{delete t._dirty})),this._triggerEvent("added",this.engine.addedNodes),this.engine.addedNodes=[]),this}_triggerRemoveEvent(){return this.engine.batchMode||this.engine.removedNodes&&this.engine.removedNodes.length>0&&(this._triggerEvent("removed",this.engine.removedNodes),this.engine.removedNodes=[]),this}_triggerEvent(t,e){let i=e?new CustomEvent(t,{bubbles:!1,detail:e}):new Event(t);return this.el.dispatchEvent(i),this}_removeStylesheet(){return this._styles&&(n.Utils.removeStylesheet(this._styles._id),delete this._styles),this}_updateStyles(t=!1,e){if(t&&this._removeStylesheet(),this._updateContainerHeight(),0===this.opts.cellHeight)return this;let i=this.opts.cellHeight,s=this.opts.cellHeightUnit,o=`.${this.opts._styleSheetClass} > .${this.opts.itemClass}`;if(!this._styles){let t="gridstack-style-"+(1e5*Math.random()).toFixed(),e=this.opts.styleInHead?void 0:this.el.parentNode;if(this._styles=n.Utils.createStylesheet(t,e),!this._styles)return this;this._styles._id=t,this._styles._max=0,n.Utils.addCSSRule(this._styles,o,`min-height: ${i}${s}`);let r=this.opts.marginTop+this.opts.marginUnit,l=this.opts.marginBottom+this.opts.marginUnit,h=this.opts.marginRight+this.opts.marginUnit,a=this.opts.marginLeft+this.opts.marginUnit,d=`${o} > .grid-stack-item-content`,g=`.${this.opts._styleSheetClass} > .grid-stack-placeholder > .placeholder-content`;n.Utils.addCSSRule(this._styles,d,`top: ${r}; right: ${h}; bottom: ${l}; left: ${a};`),n.Utils.addCSSRule(this._styles,g,`top: ${r}; right: ${h}; bottom: ${l}; left: ${a};`),n.Utils.addCSSRule(this._styles,`${o} > .ui-resizable-ne`,`right: ${h}`),n.Utils.addCSSRule(this._styles,`${o} > .ui-resizable-e`,`right: ${h}`),n.Utils.addCSSRule(this._styles,`${o} > .ui-resizable-se`,`right: ${h}; bottom: ${l}`),n.Utils.addCSSRule(this._styles,`${o} > .ui-resizable-nw`,`left: ${a}`),n.Utils.addCSSRule(this._styles,`${o} > .ui-resizable-w`,`left: ${a}`),n.Utils.addCSSRule(this._styles,`${o} > .ui-resizable-sw`,`left: ${a}; bottom: ${l}`)}if((e=e||this._styles._max)>this._styles._max){let t=t=>i*t+s;for(let i=this._styles._max+1;i<=e;i++){let e=t(i);n.Utils.addCSSRule(this._styles,`${o}[gs-y="${i-1}"]`,`top: ${t(i-1)}`),n.Utils.addCSSRule(this._styles,`${o}[gs-h="${i}"]`,`height: ${e}`),n.Utils.addCSSRule(this._styles,`${o}[gs-min-h="${i}"]`,`min-height: ${e}`),n.Utils.addCSSRule(this._styles,`${o}[gs-max-h="${i}"]`,`max-height: ${e}`)}this._styles._max=e}return this}_updateContainerHeight(){if(!this.engine||this.engine.batchMode)return this;let t=this.getRow(),e=parseInt(getComputedStyle(this.el)["min-height"]);if(e>0){let i=Math.round(e/this.getCellHeight(!0));t<i&&(t=i)}if(this.el.setAttribute("gs-current-row",String(t)),0===t)return this.el.style.removeProperty("height"),this;let i=this.opts.cellHeight,s=this.opts.cellHeightUnit;return i?(this.el.style.height=t*i+s,this):this}_resizeNestedGrids(t){return t.querySelectorAll(".grid-stack").forEach((t=>{t.gridstack&&t.gridstack.onParentResize()})),this}_prepareElement(t,e=!1,i){i||(t.classList.add(this.opts.itemClass),i=this._readAttr(t)),t.gridstackNode=i,i.el=t,i.grid=this;let s=Object.assign({},i);return i=this.engine.addNode(i,e),n.Utils.same(i,s)||this._writeAttr(t,i),this._prepareDragDropByNode(i),this}_writePosAttr(t,e,i,s,o){return null!=e&&t.setAttribute("gs-x",String(e)),null!=i&&t.setAttribute("gs-y",String(i)),s&&t.setAttribute("gs-w",String(s)),o&&t.setAttribute("gs-h",String(o)),this}_writeAttr(t,e){if(!e)return this;this._writePosAttr(t,e.x,e.y,e.w,e.h);let i={autoPosition:"gs-auto-position",minW:"gs-min-w",minH:"gs-min-h",maxW:"gs-max-w",maxH:"gs-max-h",noResize:"gs-no-resize",noMove:"gs-no-move",locked:"gs-locked",id:"gs-id",resizeHandles:"gs-resize-handles"};for(const s in i)e[s]?t.setAttribute(i[s],String(e[s])):t.removeAttribute(i[s]);return this}_readAttr(t){let e={};e.x=n.Utils.toNumber(t.getAttribute("gs-x")),e.y=n.Utils.toNumber(t.getAttribute("gs-y")),e.w=n.Utils.toNumber(t.getAttribute("gs-w")),e.h=n.Utils.toNumber(t.getAttribute("gs-h")),e.maxW=n.Utils.toNumber(t.getAttribute("gs-max-w")),e.minW=n.Utils.toNumber(t.getAttribute("gs-min-w")),e.maxH=n.Utils.toNumber(t.getAttribute("gs-max-h")),e.minH=n.Utils.toNumber(t.getAttribute("gs-min-h")),e.autoPosition=n.Utils.toBool(t.getAttribute("gs-auto-position")),e.noResize=n.Utils.toBool(t.getAttribute("gs-no-resize")),e.noMove=n.Utils.toBool(t.getAttribute("gs-no-move")),e.locked=n.Utils.toBool(t.getAttribute("gs-locked")),e.resizeHandles=t.getAttribute("gs-resize-handles"),e.id=t.getAttribute("gs-id");for(const t in e){if(!e.hasOwnProperty(t))return;e[t]||0===e[t]||delete e[t]}return e}_setStaticClass(){let t=["grid-stack-static"];return this.opts.staticGrid?(this.el.classList.add(...t),this.el.setAttribute("gs-static","true")):(this.el.classList.remove(...t),this.el.removeAttribute("gs-static")),this}onParentResize(){if(this.el&&this.el.clientWidth){if(this._isAutoCellHeight&&n.Utils.throttle((()=>{let t=-this.opts.marginRight-this.opts.marginLeft+this.opts.marginTop+this.opts.marginBottom;this.cellHeight(this.cellWidth()+t)}),100),!this.opts.disableOneColumnMode&&this.el.clientWidth<=this.opts.minWidth){if(this._oneColumnMode)return this;this._oneColumnMode=!0,this.column(1),this._resizeNestedGrids(this.el)}else{if(!this._oneColumnMode)return this;delete this._oneColumnMode,this.column(this._prevColumn),this._resizeNestedGrids(this.el)}return this}}_updateWindowResizeEvent(t=!1){const e=this._isAutoCellHeight||!this.opts.disableOneColumnMode;return!e||t||this.opts._isNested||this._windowResizeBind?!t&&e||!this._windowResizeBind||(window.removeEventListener("resize",this._windowResizeBind),delete this._windowResizeBind):(this._windowResizeBind=this.onParentResize.bind(this),window.addEventListener("resize",this._windowResizeBind),this.onParentResize()),this}static getElement(t=".grid-stack-item"){return n.Utils.getElement(t)}static getElements(t=".grid-stack-item"){return n.Utils.getElements(t)}static getGridElement(t){return h.getElement(t)}static getGridElements(t){return n.Utils.getElements(t)}initMargin(){let t,e=0,i=[];return"string"==typeof this.opts.margin&&(i=this.opts.margin.split(" ")),2===i.length?(this.opts.marginTop=this.opts.marginBottom=i[0],this.opts.marginLeft=this.opts.marginRight=i[1]):4===i.length?(this.opts.marginTop=i[0],this.opts.marginRight=i[1],this.opts.marginBottom=i[2],this.opts.marginLeft=i[3]):(t=n.Utils.parseHeight(this.opts.margin),this.opts.marginUnit=t.unit,e=this.opts.margin=t.h),void 0===this.opts.marginTop?this.opts.marginTop=e:(t=n.Utils.parseHeight(this.opts.marginTop),this.opts.marginTop=t.h,delete this.opts.margin),void 0===this.opts.marginBottom?this.opts.marginBottom=e:(t=n.Utils.parseHeight(this.opts.marginBottom),this.opts.marginBottom=t.h,delete this.opts.margin),void 0===this.opts.marginRight?this.opts.marginRight=e:(t=n.Utils.parseHeight(this.opts.marginRight),this.opts.marginRight=t.h,delete this.opts.margin),void 0===this.opts.marginLeft?this.opts.marginLeft=e:(t=n.Utils.parseHeight(this.opts.marginLeft),this.opts.marginLeft=t.h,delete this.opts.margin),this.opts.marginUnit=t.unit,this.opts.marginTop===this.opts.marginBottom&&this.opts.marginLeft===this.opts.marginRight&&this.opts.marginTop===this.opts.marginRight&&(this.opts.margin=this.opts.marginTop),this}movable(t,e){return this}resizable(t,e){return this}_setupAcceptWidget(){return this}_setupRemoveDrop(){return this}_setupRemovingTimeout(t){return this}_clearRemovingTimeout(t){return this}_setupDragIn(){return this}_prepareDragDropByNode(t){return this}locked(t,e){return this.update(t,{locked:e})}maxWidth(t,e){return this.update(t,{maxW:e})}minWidth(t,e){return this.update(t,{minW:e})}maxHeight(t,e){return this.update(t,{maxH:e})}minHeight(t,e){return this.update(t,{minH:e})}move(t,e,i){return this.update(t,{x:e,y:i})}resize(t,e,i){return this.update(t,{w:e,h:i})}}e.GridStack=h,h.Utils=n.Utils,h.Engine=o.GridStackEngine},822:(t,e,i)=>{function s(t){for(var i in t)e.hasOwnProperty(i)||(e[i]=t[i])}Object.defineProperty(e,"__esModule",{value:!0}),s(i(593)),s(i(62)),s(i(334)),s(i(270))},593:(t,e)=>{Object.defineProperty(e,"__esModule",{value:!0}),e.obsolete=function(t,e,i,s,o){let n=(...n)=>(console.warn("gridstack.js: Function `"+i+"` is deprecated in "+o+" and has been replaced with `"+s+"`. It will be **completely** removed in v1.0"),e.apply(t,n));return n.prototype=e.prototype,n},e.obsoleteOpts=function(t,e,i,s){void 0!==t[e]&&(t[i]=t[e],console.warn("gridstack.js: Option `"+e+"` is deprecated in "+s+" and has been replaced with `"+i+"`. It will be **completely** removed in v1.0"))},e.obsoleteOptsDel=function(t,e,i,s){void 0!==t[e]&&console.warn("gridstack.js: Option `"+e+"` is deprecated in "+i+s)},e.obsoleteAttr=function(t,e,i,s){let o=t.getAttribute(e);null!==o&&(t.setAttribute(i,o),console.warn("gridstack.js: attribute `"+e+"`="+o+" is deprecated on this object in "+s+" and has been replaced with `"+i+"`. It will be **completely** removed in v1.0"))},e.Utils=class{static getElements(t){if("string"==typeof t){let e=document.querySelectorAll(t);return e.length||"."===t[0]||"#"===t[0]||(e=document.querySelectorAll("."+t),e.length||(e=document.querySelectorAll("#"+t))),Array.from(e)}return[t]}static getElement(t){if("string"==typeof t){if(!t.length)return null;if("#"===t[0])return document.getElementById(t.substring(1));if("."===t[0]||"["===t[0])return document.querySelector(t);if(!isNaN(+t[0]))return document.getElementById(t);let e=document.querySelector(t);return e||(e=document.getElementById(t)),e||(e=document.querySelector("."+t)),e}return t}static isIntercepted(t,e){return!(t.x+t.w<=e.x||e.x+e.w<=t.x||t.y+t.h<=e.y||e.y+e.h<=t.y)}static sort(t,e,i){if(!i){let e=t.map((t=>t.x+t.w));i=Math.max(...e)}return-1===e?t.sort(((t,e)=>e.x+e.y*i-(t.x+t.y*i))):t.sort(((t,e)=>t.x+t.y*i-(e.x+e.y*i)))}static createStylesheet(t,e){let i=document.createElement("style");return i.setAttribute("type","text/css"),i.setAttribute("gs-style-id",t),i.styleSheet?i.styleSheet.cssText="":i.appendChild(document.createTextNode("")),e?e.insertBefore(i,e.firstChild):(e=document.getElementsByTagName("head")[0]).appendChild(i),i.sheet}static removeStylesheet(t){let e=document.querySelector("STYLE[gs-style-id="+t+"]");e&&e.parentNode&&e.parentNode.removeChild(e)}static addCSSRule(t,e,i){"function"==typeof t.addRule?t.addRule(e,i):"function"==typeof t.insertRule&&t.insertRule(`${e}{${i}}`)}static toBool(t){return"boolean"==typeof t?t:"string"==typeof t?!(""===(t=t.toLowerCase())||"no"===t||"false"===t||"0"===t):Boolean(t)}static toNumber(t){return null===t||0===t.length?void 0:Number(t)}static parseHeight(t){let e,i="px";if("string"==typeof t){let s=t.match(/^(-[0-9]+\.[0-9]+|[0-9]*\.[0-9]+|-[0-9]+|[0-9]+)(px|em|rem|vh|vw|%)?$/);if(!s)throw new Error("Invalid height");i=s[2]||"px",e=parseFloat(s[1])}else e=t;return{h:e,unit:i}}static defaults(t,...e){return e.forEach((e=>{for(const i in e){if(!e.hasOwnProperty(i))return;null===t[i]||void 0===t[i]?t[i]=e[i]:"object"==typeof e[i]&&"object"==typeof t[i]&&this.defaults(t[i],e[i])}})),t}static same(t,e){if("object"!=typeof t)return t==e;if(typeof t!=typeof e)return!1;if(Object.keys(t).length!==Object.keys(e).length)return!1;for(const i in t)if(t[i]!==e[i])return!1;return!0}static removeInternalAndSame(t,e){if("object"==typeof t&&"object"==typeof e)for(let i in t){let s=t[i];if(s&&"object"==typeof s){for(let t in s)s[t]!==e[i][t]&&"_"!==t[0]||delete s[t];Object.keys(s).length||delete t[i]}else s!==e[i]&&"_"!==i[0]||delete t[i]}}static closestByClass(t,e){for(;t=t.parentElement;)if(t.classList.contains(e))return t;return null}static throttle(t,e){let i=!1;return(...s)=>{i||(t.apply(this,s),i=!0,setTimeout((()=>i=!1),e))}}static removePositioningStyles(t){let e=t.style;e.position&&e.removeProperty("position"),e.left&&e.removeProperty("left"),e.top&&e.removeProperty("top"),e.width&&e.removeProperty("width"),e.height&&e.removeProperty("height")}static getScrollParent(t){if(null===t)return document.documentElement;const e=getComputedStyle(t);return/(auto|scroll)/.test(e.overflow+e.overflowY)?t:this.getScrollParent(t.parentElement)}static updateScrollPosition(t,e,i){let s=t.getBoundingClientRect(),o=window.innerHeight||document.documentElement.clientHeight;if(s.top<0||s.bottom>o){let n=s.bottom-o,r=s.top,l=this.getScrollParent(t);if(null!==l){let h=l.scrollTop;s.top<0&&i<0?t.offsetHeight>o?l.scrollTop+=i:l.scrollTop+=Math.abs(r)>Math.abs(i)?i:r:i>0&&(t.offsetHeight>o?l.scrollTop+=i:l.scrollTop+=n>i?i:n),e.top+=l.scrollTop-h}}}static updateScrollResize(t,e,i){const s=this.getScrollParent(e),o=s.clientHeight,n=t.clientY<i,r=t.clientY>o-i;n?s.scrollBy({behavior:"smooth",top:t.clientY-i}):r&&s.scrollBy({behavior:"smooth",top:i-(o-t.clientY)})}}}},e={};return function i(s){if(e[s])return e[s].exports;var o=e[s]={exports:{}};return t[s](o,o.exports,i),o.exports}(822)})().GridStack}));
//# sourceMappingURL=gridstack-static.js.map