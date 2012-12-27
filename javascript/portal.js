// Copyright (c) 2006 Sébastien Gruhier (http://xilinus.com, http://itseb.com)
// 
// Permission is hereby granted, free of charge, to any person obtaining
// a copy of this software and associated documentation files (the
// "Software"), to deal in the Software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of the Software, and to
// permit persons to whom the Software is furnished to do so, subject to
// the following conditions:
// 
// The above copyright notice and this permission notice shall be
// included in all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
// MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
// LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
// WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//
// VERSION 1.1-trunk
//
// http://blog.xilinus.com/2007/8/26/prototype-portal-class
// http://blog.xilinus.com/2007/9/4/prototype-portal-class-2
//

if(typeof Draggable == 'undefined')
  throw("widget.js requires including script.aculo.us' dragdrop.js library");

if(typeof Builder == 'undefined')
  throw("widget.js requires including script.aculo.us' builder.js library");

// Xilinus namespace
if(typeof Xilinus == 'undefined')
  Xilinus = {}
  
Builder.dump();


Xilinus.Widget = Class.create();  
Xilinus.Widget.lastId = 0;

Object.extend(Xilinus.Widget.prototype, {
  initialize: function(className, id) {
    className = className || "widget";  
    this._id   = id || ("widget_" + Xilinus.Widget.lastId++);
    
    this._titleDiv   = DIV({className: className + '_title',     id: this._getId("header")},  "");  
    this._contentDiv = DIV({className: className + '_content',   id: this._getId("content")}, "");
    this._footerDiv  = DIV({className: className + '_statusbar', id: this._getId("footer")},  "");
    
    var divHeader  = DIV({className: className + '_nw' }, this._titleDiv);
    var divContent = DIV({className: className + '_w' },  this._contentDiv); 
    var divFooter  = DIV({className: className + '_sw' }, this._footerDiv);  
    
    this._div = DIV({className: className + (className != "widget" ? " widget" : ""), id: this._getId()}, [divHeader, divContent, divFooter]);     
    this._div.widget = this;
        
    return this;
  },
  
  getElement: function() {
    return $(this._getId()) || $(this._div);
  },
  
  setTitle: function(title) {
    $(this._titleDiv).update(title);
    return this;
  },

  getTitle: function(title) {
    return $(this._titleDiv)
  },
  
  setFooter: function(title) {
    $(this._footerDiv).update(title);
    return this;
  },
  
  getFooter: function(title) {
    return $(this._footerDiv)
  },
  
  // setActive function added to original portal.js
  setActive: function(active) {
    if (!active) {
      $(this._contentDiv).addClassName('widget_inactive');
    } else {
      $(this._contentDiv).removeClassName('widget_inactive');
    }
    return this;
  },
  // setVisibleFor function added to original portal.js
  setVisibleFor: function(visiblefor) {
    if (visiblefor == 2) {
      $(this._contentDiv).addClassName('widget_onlynonmembers');
      $(this._contentDiv).removeClassName('widget_onlymembers');
    } else if (visiblefor == 0) {
      $(this._contentDiv).addClassName('widget_onlymembers');
      $(this._contentDiv).removeClassName('widget_onlynonmembers');
    }
    return this;
  },
  setContent: function(title) {
    $(this._contentDiv).update(title);  
    return this;
  },
   
  getContent: function(title) {
    return $(this._contentDiv)
  },
  
  updateHeight: function() {       
    $(this._contentDiv).setStyle({height: null})
      
    var h = $(this._contentDiv).getHeight();
    $(this._contentDiv).setStyle({height: h + "px"})
  },
  
  // PRIVATE FUNCTIONS
  _getId: function(prefix) { 
      return (prefix ? prefix + "_" : "") + this._id;
  }
});


Xilinus.Portal = Class.create()
Object.extend(Xilinus.Portal.prototype, {
  lastEvent: null,   
  widgets:   null,
  columns:   null, 
  
  initialize: function(columns, options) {   
    this.options = Object.extend({
                     url:          null,          // Url called by Ajax.Request after a drop
                     onOverWidget: null,          // Called when the mouse goes over a widget
                     onOutWidget:  null,          // Called when the mouse goes out of a widget                                           
                     onChange:     null,          // Called a widget has been move during drag and drop 
                     onUpdate:     null,          // Called a widget has been move after drag and drop
                     removeEffect: Element.remove,// Remove effect (by default no effect), you can set it to Effect.SwitchOff for example
                     accept: null
                   }, options)
    this._columns = (typeof columns == "string") ? $$(columns) : columns;
    this._widgets = new Array();          
    this._columns.each(function(element) {Droppables.add(element, {onHover: this.onHover.bind(this), 
                                                                   overlap: "vertical", 
                                                                   accept: this.options.accept})}.bind(this));  
    this._outTimer  = null;
    
    // Draggable calls makePositioned for IE fix (??), I had to remove it for all browsers fix :) to handle properly zIndex
    this._columns.invoke("undoPositioned");    
    
    this._currentOverWidget = null; 
    this._widgetMouseOver = this.widgetMouseOver.bindAsEventListener(this);
    this._widgetMouseOut  = this.widgetMouseOut.bindAsEventListener(this);
    
    Draggables.addObserver({ onEnd: this.endDrag.bind(this), onStart: this.startDrag.bind(this) }); 
  },
  
  add: function(widget, columnIndex, draggable) {   
    draggable = typeof draggable == "undefined" ? true : draggable
    // Add to widgets list
    this._widgets.push(widget);
    if (this.options.accept)
      widget.getElement().addClassName(this.options.accept)
    // Add element to column
    this._columns[columnIndex].appendChild(widget.getElement());
    widget.updateHeight();
    
    // Make header draggable   
    if (draggable) {
      widget.draggable = new Draggable(widget.getElement(),{ handle: widget._titleDiv, revert: false});     
      widget.getTitle().addClassName("widget_draggable");   
    }
    
    // Update columns heights
    this._updateColumnsHeight();  

    // Add mouse observers  
    if (this.options.onOverWidget)
      widget.getElement().immediateDescendants().invoke("observe", "mouseover", this._widgetMouseOver);
    if (this.options.onOutWidget)
      widget.getElement().immediateDescendants().invoke("observe", "mouseout",  this._widgetMouseOut);
  },  
  
  remove: function(widget) {
    // Remove from the list
    this._widgets.reject(function(w) { return w == widget});

    // Remove observers
    if (this.options.onOverWidget)
      widget.getElement().immediateDescendants().invoke("stopObserving", "mouseover", this._widgetMouseOver);
    if (this.options.onOutWidget)
      widget.getElement().immediateDescendants().invoke("stopObserving", "mouseout",  this._widgetMouseOut);

    // Remove draggable
    if (widget.draggable)
      widget.draggable.destroy();
      
    // Remove from the dom
    this.options.removeEffect(widget.getElement());
    
    // Update columns heights
    this._updateColumnsHeight();  
  },
    
  serialize: function() {
    parameters = ""
    this._columns.each(function(column) {   
      var p = column.immediateDescendants().collect(function(element) {
        return column.id + "[]=" + element.id
      }).join("&") 
      parameters += p + "&"
    });               
    
    return parameters;                      
  },     
  
  addWidgetControls: function(element) {
    $(element).observe("mouseover", this._widgetMouseOver); 
    $(element).observe("mouseout", this._widgetMouseOut); 
  },
  
  // EVENTS CALLBACKS
  widgetMouseOver: function(event) {   
    this._clearTimer();
      
    var element =  Event.element(event).up(".widget");
    if (this._currentOverWidget == null || this._currentOverWidget != element) {
      if (this._currentOverWidget && this._currentOverWidget != element)
        this.options.onOutWidget(this, this._currentOverWidget.widget)    
        
      this._currentOverWidget = element;
      this.options.onOverWidget(this, element.widget)
    }
  },

  widgetMouseOut: function(event) {    
    this._clearTimer();
    var element =  Event.element(event).up(".widget"); 
    this._outTimer = setTimeout(this._doWidgetMouseOut.bind(this, element), 100);
  },
  
  _doWidgetMouseOut: function(element) {
    this._currentOverWidget = null;
    this.options.onOutWidget(this, element.widget)    
  },                                               
  
  // DRAGGABLE OBSERVER CALLBACKS
  startDrag: function(eventName, draggable) { 
    var widget = draggable.element;
    
    if (!this._widgets.find(function(w) {return w == widget.widget}))
      return;

    var column = widget.parentNode;
    
    // Create and insert ghost widget
    var ghost = DIV({className: 'widget_ghost'}, ""); 
    $(ghost).setStyle({height: widget.getHeight()  + 'px'})

    column.insertBefore(ghost, widget);  

    // IE Does not absolutize properly the widget, needs to set width before
    widget.setStyle({width: widget.getWidth() + "px"});
    
    // Absolutize and move widget on body
    Position.absolutize(widget);  
    document.body.appendChild(widget);   
    
    // Store ghost to drag widget for later use
    draggable.element.ghost = ghost; 
    
    // Store current position
    this._savePosition = this.serialize();    
  },   

  endDrag: function(eventName, draggable) {
    var widget = draggable.element;      
    if (!this._widgets.find(function(w) {return w == widget.widget}))
      return;
    
    var column = widget.ghost.parentNode;
    
    column.insertBefore(draggable.element, widget.ghost); 
    widget.ghost.remove();   
    
    if (Prototype.Browser.Opera)     
      widget.setStyle({top: 0, left: 0, width: "100%", height: widget._originalHeight, zIndex: null, opacity: null, position: "relative"})
    else
      widget.setStyle({top: null, left: null, width: null, height: widget._originalHeight, zIndex: 'auto', opacity: null, position: "relative"})
// In original portal.js      
// widget.setStyle({top: null, left: null, width: null, height: widget._originalHeight, zIndex: null, opacity: null, position: "relative"})
    
    widget.ghost = null;    
    widget.widget.updateHeight();
    this._updateColumnsHeight();
    
    // Fire events if changed
    if (this._savePosition != this.serialize()) {
      if (this.options.url)  
        new Ajax.Request(this.options.url, {parameters: this.serialize()});
      
      if (this.options.onUpdate)
        this.options.onUpdate(this, widget);
// In original portal.js      
// this.options.onUpdate(this, widget);
    }
  },

  onHover: function(dragWidget, dropon, overlap) {  
    var offset = Position.cumulativeOffset(dropon);
    var x = offset[0] + 10;
    var y = offset[1] + (1 - overlap) * dropon.getHeight();

    // Check over ghost widget
    if (Position.within(dragWidget.ghost, x, y))
      return;
      
    // Find if it's overlapping a widget
    var found = false;
    var moved = false;
    for (var index = 0, len = this._widgets.length; index < len; ++index) {
      var w = this._widgets[index].getElement();
      if (w ==  dragWidget || w.parentNode != dropon)   
        continue;        

      if (Position.within(w, x, y)) {    
        var overlap = Position.overlap( 'vertical', w);     
        // Bottom of the widget
        if (overlap < 0.5) {         
          // Check if the ghost widget is not already below this widget
          if (w.next() != dragWidget.ghost) {
            w.parentNode.insertBefore(dragWidget.ghost, w.next());   
            moved = true;
          }
        } 
        // Top of the widget
        else {       
          // Check if the ghost widget is not already above this widget
          if (w.previous() != dragWidget.ghost) {      
            w.parentNode.insertBefore(dragWidget.ghost, w);   
            moved = true;
          }
        }
        found = true;
        break;
      }
    }
    // Not found a widget
    if (! found) {        
      // Check if dropon has ghost widget
      if (dragWidget.ghost.parentNode != dropon) {
        // Get last widget bottom value
        var last = dropon.immediateDescendants().last();
        var yLast = last ? Position.cumulativeOffset(last)[1] + last.getHeight() : 0; 
        if (y > yLast && last != dragWidget.ghost) {
          dropon.appendChild(dragWidget.ghost);
          moved = true;
        }
      }
    }  
    if (moved && this.options.onChange) 
      this.options.onChange(this)            

    this._updateColumnsHeight();
  },                
  
  // PRIVATE FUNCTIONS
  // return; is added in this version, was not in original portal.js
  _updateColumnsHeight: function() {      return;
    var h = 0;
    this._columns.each(function(col) {
      h = Math.max(h, col.immediateDescendants().inject(0, function(sum, element) { 
        return sum + element.getHeight(); 
      }));
    })
    this._columns.invoke("setStyle", {height: h + 'px'})
  },
  
  _clearTimer: function() {
    if (this._outTimer) {
      clearTimeout(this._outTimer);
      this._outTimer = null;
    }                        
  }
});

