/**
 * https://gridstackjs.com/
 * (c) 2014-2020 Alain Dumesny, Dylan Weiss, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
import './gridstack-poly.js';
import { GridStackEngine } from './gridstack-engine';
import { Utils } from './utils';
import { GridItemHTMLElement, GridStackWidget, GridStackNode, GridstackOptions, numberOrString } from './types';
import { GridStackDD } from './gridstack-dd';
export * from './types';
export * from './utils';
export * from './gridstack-engine';
export * from './gridstack-dd';
import './jq/gridstack-dd-jqueryui';
export * from './jq/gridstack-dd-jqueryui';
export declare type GridStackElement = string | HTMLElement | GridItemHTMLElement;
export interface GridHTMLElement extends HTMLElement {
    gridstack?: GridStack;
}
export declare type GridStackEvent = 'added' | 'change' | 'disable' | 'dragstart' | 'dragstop' | 'dropped' | 'enable' | 'removed' | 'resizestart' | 'resizestop';
/** Defines the coordinates of an object */
export interface MousePosition {
    top: number;
    left: number;
}
/** Defines the position of a cell inside the grid*/
export interface CellPosition {
    x: number;
    y: number;
}
/**
 * Main gridstack class - you will need to call `GridStack.init()` first to initialize your grid.
 * Note: your grid elements MUST have the following classes for the CSS layout to work:
 * @example
 * <div class="grid-stack">
 *   <div class="grid-stack-item">
 *     <div class="grid-stack-item-content">Item 1</div>
 *   </div>
 * </div>
 */
export declare class GridStack {
    /**
     * initializing the HTML element, or selector string, into a grid will return the grid. Calling it again will
     * simply return the existing instance (ignore any passed options). There is also an initAll() version that support
     * multiple grids initialization at once.
     * @param options grid options (optional)
     * @param elOrString element or CSS selector (first one used) to convert to a grid (default to '.grid-stack' class selector)
     *
     * @example
     * let grid = GridStack.init();
     *
     * Note: the HTMLElement (of type GridHTMLElement) will store a `gridstack: GridStack` value that can be retrieve later
     * let grid = document.querySelector('.grid-stack').gridstack;
     */
    static init(options?: GridstackOptions, elOrString?: GridStackElement): GridStack;
    /**
     * Will initialize a list of elements (given a selector) and return an array of grids.
     * @param options grid options (optional)
     * @param selector elements selector to convert to grids (default to '.grid-stack' class selector)
     *
     * @example
     * let grids = GridStack.initAll();
     * grids.forEach(...)
     */
    static initAll(options?: GridstackOptions, selector?: string): GridStack[];
    /** scoping so users can call GridStack.Utils.sort() for example */
    static Utils: typeof Utils;
    /** scoping so users can call new GridStack.Engine(12) for example */
    static Engine: typeof GridStackEngine;
    /** the HTML element tied to this grid after it's been initialized */
    el: GridHTMLElement;
    /** engine used to implement non DOM grid functionality */
    engine: GridStackEngine;
    /** grid options - public for classes to access, but use methods to modify! */
    opts: GridstackOptions;
    /** current drag&drop plugin being used */
    dd: GridStackDD;
    /**
     * Construct a grid item from the given element and options
     * @param el
     * @param opts
     */
    constructor(el: GridHTMLElement, opts?: GridstackOptions);
    /**
     * add a new widget and returns it.
     *
     * Widget will be always placed even if result height is more than actual grid height.
     * You need to use willItFit method before calling addWidget for additional check.
     * See also `makeWidget()`.
     *
     * @example
     * let grid = GridStack.init();
     * grid.addWidget('<div><div class="grid-stack-item-content">hello</div></div>', {width: 3});
     *
     * @param el html element or string definition to add
     * @param options widget position/size options (optional) - see GridStackWidget
     */
    addWidget(el: GridStackElement, options?: GridStackWidget): GridItemHTMLElement;
    /** saves the current layout returning a list of widgets for serialization */
    save(): GridStackWidget[];
    /**
     * load the widgets from a list. This will call update() on each (matching by id) or add/remove widgets that are not there.
     *
     * @param layout list of widgets definition to update/create
     * @param addAndRemove boolean (default true) or callback method can be passed to control if and how missing widgets can be added/removed, giving
     * the user control of insertion.
     *
     * @example
     * see http://gridstackjs.com/demo/serialization.html
     **/
    load(layout: GridStackWidget[], addAndRemove?: boolean | ((w: GridStackWidget, add: boolean) => void)): void;
    /**
     * Initializes batch updates. You will see no changes until `commit()` method is called.
     */
    batchUpdate(): GridStack;
    /**
     * Gets current cell height.
     */
    getCellHeight(): number;
    /**
     * Update current cell height - see `GridstackOptions.cellHeight` for format.
     * This method rebuilds an internal CSS style sheet.
     * Note: You can expect performance issues if call this method too often.
     *
     * @param val the cell height
     * @param update (Optional) if false, styles will not be updated
     *
     * @example
     * grid.cellHeight(grid.cellWidth() * 1.2);
     */
    cellHeight(val: numberOrString, update?: boolean): GridStack;
    /**
     * Gets current cell width.
     */
    cellWidth(): number;
    /**
     * Finishes batch updates. Updates DOM nodes. You must call it after batchUpdate.
     */
    commit(): GridStack;
    /** re-layout grid items to reclaim any empty space */
    compact(): GridStack;
    /**
     * set the number of columns in the grid. Will update existing widgets to conform to new number of columns,
     * as well as cache the original layout so you can revert back to previous positions without loss.
     * Requires `gridstack-extra.css` or `gridstack-extra.min.css` for [1-11],
     * else you will need to generate correct CSS (see https://github.com/gridstack/gridstack.js#change-grid-columns)
     * @param column - Integer > 0 (default 12).
     * @param doNotPropagate if true existing widgets will not be updated (optional)
     */
    column(column: number, doNotPropagate?: boolean): GridStack;
    /**
     * get the number of columns in the grid (default 12)
     */
    getColumn(): number;
    /** returns an array of grid HTML elements (no placeholder) - used to iterate through our children */
    getGridItems(): GridItemHTMLElement[];
    /**
     * Destroys a grid instance.
     * @param removeDOM if `false` grid and items elements will not be removed from the DOM (Optional. Default `true`).
     */
    destroy(removeDOM?: boolean): GridStack;
    /**
     * Disables widgets moving/resizing. This is a shortcut for:
     * @example
     *  grid.enableMove(false);
     *  grid.enableResize(false);
     */
    disable(): GridStack;
    /**
     * Enables widgets moving/resizing. This is a shortcut for:
     * @example
     *  grid.enableMove(true);
     *  grid.enableResize(true);
     */
    enable(): GridStack;
    /**
     * Enables/disables widget moving.
     *
     * @param doEnable
     * @param includeNewWidgets will force new widgets to be draggable as per
     * doEnable`s value by changing the disableDrag grid option (default: true).
     */
    enableMove(doEnable: boolean, includeNewWidgets?: boolean): GridStack;
    /**
     * Enables/disables widget resizing
     * @param doEnable
     * @param includeNewWidgets will force new widgets to be draggable as per
     * doEnable`s value by changing the disableResize grid option (default: true).
     */
    enableResize(doEnable: boolean, includeNewWidgets?: boolean): GridStack;
    /**
     * enable/disable floating widgets (default: `false`) See [example](http://gridstackjs.com/demo/float.html)
     */
    float(val: boolean): GridStack;
    /**
     * get the current float mode
     */
    getFloat(): boolean;
    /**
     * Get the position of the cell under a pixel on screen.
     * @param position the position of the pixel to resolve in
     * absolute coordinates, as an object with top and left properties
     * @param useDocRelative if true, value will be based on document position vs parent position (Optional. Default false).
     * Useful when grid is within `position: relative` element
     *
     * Returns an object with properties `x` and `y` i.e. the column and row in the grid.
     */
    getCellFromPixel(position: MousePosition, useDocRelative?: boolean): CellPosition;
    /** returns the current number of rows, which will be at least `minRow` if set */
    getRow(): number;
    /**
     * Checks if specified area is empty.
     * @param x the position x.
     * @param y the position y.
     * @param width the width of to check
     * @param height the height of to check
     */
    isAreaEmpty(x: number, y: number, width: number, height: number): boolean;
    /**
     * Locks/unlocks widget.
     * @param el element or selector to modify.
     * @param val if true widget will be locked.
     */
    locked(els: GridStackElement, val: boolean): GridStack;
    /**
     * If you add elements to your grid by hand, you have to tell gridstack afterwards to make them widgets.
     * If you want gridstack to add the elements for you, use `addWidget()` instead.
     * Makes the given element a widget and returns it.
     * @param els widget or single selector to convert.
     *
     * @example
     * let grid = GridStack.init();
     * grid.el.appendChild('<div id="gsi-1" data-gs-width="3"></div>');
     * grid.makeWidget('gsi-1');
     */
    makeWidget(els: GridStackElement): GridItemHTMLElement;
    /**
     * Set the maxWidth for a widget.
     * @param els widget or selector to modify.
     * @param val A numeric value of the number of columns
     */
    maxWidth(els: GridStackElement, val: number): GridStack;
    /**
     * Set the minWidth for a widget.
     * @param els widget or selector to modify.
     * @param val A numeric value of the number of columns
     */
    minWidth(els: GridStackElement, val: number): GridStack;
    /**
     * Set the maxHeight for a widget.
     * @param els widget or selector to modify.
     * @param val A numeric value of the number of rows
     */
    maxHeight(els: GridStackElement, val: number): GridStack;
    /**
     * Set the minHeight for a widget.
     * @param els widget or selector to modify.
     * @param val A numeric value of the number of rows
     */
    minHeight(els: GridStackElement, val: number): GridStack;
    /**
     * Enables/Disables moving.
     * @param els widget or selector to modify.
     * @param val if true widget will be draggable.
     */
    movable(els: GridStackElement, val: boolean): GridStack;
    /**
     * Changes widget position
     * @param els  widget or singular selector to modify
     * @param x new position x. If value is null or undefined it will be ignored.
     * @param y new position y. If value is null or undefined it will be ignored.
     */
    move(els: GridStackElement, x?: number, y?: number): GridStack;
    /**
     * Event handler that extracts our CustomEvent data out automatically for receiving custom
     * notifications (see doc for supported events)
     * @param name of the event (see possible values) or list of names space separated
     * @param callback function called with event and optional second/third param
     * (see README documentation for each signature).
     *
     * @example
     * grid.on('added', function(e, items) { log('added ', items)} );
     * or
     * grid.on('added removed change', function(e, items) { log(e.type, items)} );
     *
     * Note: in some cases it is the same as calling native handler and parsing the event.
     * grid.el.addEventListener('added', function(event) { log('added ', event.detail)} );
     *
     */
    on(name: GridStackEvent, callback: (event: Event, arg2?: GridItemHTMLElement | GridStackNode[]) => void): GridStack;
    /**
     * unsubscribe from the 'on' event below
     * @param name of the event (see possible values)
     */
    off(name: GridStackEvent): GridStack;
    /**
     * Removes widget from the grid.
     * @param el  widget or selector to modify
     * @param removeDOM if `false` DOM element won't be removed from the tree (Default? true).
     * @param triggerEvent if `false` (quiet mode) element will not be added to removed list and no 'removed' callbacks will be called (Default? true).
     */
    removeWidget(els: GridStackElement, removeDOM?: boolean, triggerEvent?: boolean): GridStack;
    /**
     * Removes all widgets from the grid.
     * @param removeDOM if `false` DOM elements won't be removed from the tree (Default? `true`).
     */
    removeAll(removeDOM?: boolean): GridStack;
    /**
     * Changes widget size
     * @param els  widget or singular selector to modify
     * @param width new dimensions width. If value is null or undefined it will be ignored.
     * @param height  new dimensions height. If value is null or undefined it will be ignored.
     */
    resize(els: GridStackElement, width?: number, height?: number): GridStack;
    /**
     * Enables/Disables resizing.
     * @param els  widget or selector to modify
     * @param val  if true widget will be resizable.
     */
    resizable(els: GridStackElement, val: boolean): GridStack;
    /**
     * Toggle the grid animation state.  Toggles the `grid-stack-animate` class.
     * @param doAnimate if true the grid will animate.
     */
    setAnimation(doAnimate: boolean): GridStack;
    /**
     * Toggle the grid static state. Also toggle the grid-stack-static class.
     * @param staticValue if true the grid become static.
     */
    setStatic(staticValue: boolean): GridStack;
    /**
     * Updates widget position/size.
     * @param els  widget or singular selector to modify
     * @param x new position x. If value is null or undefined it will be ignored.
     * @param y new position y. If value is null or undefined it will be ignored.
     * @param width new dimensions width. If value is null or undefined it will be ignored.
     * @param height  new dimensions height. If value is null or undefined it will be ignored.
     */
    update(els: GridStackElement, x?: number, y?: number, width?: number, height?: number): GridStack;
    /**
     * Updates the margins which will set all 4 sides at once - see `GridstackOptions.margin` for format options.
     * @param value new vertical margin value
     * Note: you can instead use `marginTop | marginBottom | marginLeft | marginRight` GridstackOptions to set the sides separately.
     */
    margin(value: numberOrString): GridStack;
    /** returns current vertical margin value */
    getMargin(): number;
    /**
     * Returns true if the height of the grid will be less the vertical
     * constraint. Always returns true if grid doesn't have height constraint.
     * @param x new position x. If value is null or undefined it will be ignored.
     * @param y new position y. If value is null or undefined it will be ignored.
     * @param width new dimensions width. If value is null or undefined it will be ignored.
     * @param height new dimensions height. If value is null or undefined it will be ignored.
     * @param autoPosition if true then x, y parameters will be ignored and widget
     * will be places on the first available position
     *
     * @example
     * if (grid.willItFit(newNode.x, newNode.y, newNode.width, newNode.height, newNode.autoPosition)) {
     *   grid.addWidget(newNode.el, newNode);
     * } else {
     *   alert('Not enough free space to place the widget');
     * }
     */
    willItFit(x: number, y: number, width: number, height: number, autoPosition: boolean): boolean;
}
