/**
 * https://gridstackjs.com/
 * (c) 2014-2020 Alain Dumesny, Dylan Weiss, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
import { GridStack, GridStackElement } from './gridstack';
import { GridItemHTMLElement, DDDragInOpt } from './types';
/** Drag&Drop drop options */
export declare type DDDropOpt = {
    /** function or class type that this grid will accept as dropped items (see GridStackOptions.acceptWidgets) */
    accept?: (el: GridItemHTMLElement) => boolean;
};
/** drag&drop options currently called from the main code, but others can be passed in grid options */
export declare type DDOpts = 'enable' | 'disable' | 'destroy' | 'option' | string | {} | any;
export declare type DDKey = 'minWidth' | 'minHeight' | string;
export declare type DDValue = number | string;
/** drag&drop events callbacks */
export declare type DDCallback = (event: Event, arg2: GridItemHTMLElement, helper?: GridItemHTMLElement) => void;
/**
 * Base class for drag'n'drop plugin.
 */
export declare class GridStackDD {
    protected grid: GridStack;
    static registeredPlugins: typeof GridStackDD;
    /** call this method to register your plugin instead of the default no-op one */
    static registerPlugin(pluginClass: typeof GridStackDD): void;
    /** get the current registered plugin to use */
    static get(): typeof GridStackDD;
    constructor(grid: GridStack);
    /** removes any drag&drop present (called during destroy) */
    remove(el: GridItemHTMLElement): GridStackDD;
    resizable(el: GridItemHTMLElement, opts: DDOpts, key?: DDKey, value?: DDValue): GridStackDD;
    draggable(el: GridItemHTMLElement, opts: DDOpts, key?: DDKey, value?: DDValue): GridStackDD;
    dragIn(el: GridStackElement, opts: DDDragInOpt): GridStackDD;
    isDraggable(el: GridStackElement): boolean;
    droppable(el: GridItemHTMLElement, opts: DDOpts | DDDropOpt, key?: DDKey, value?: DDValue): GridStackDD;
    isDroppable(el: GridItemHTMLElement): boolean;
    on(el: GridItemHTMLElement, eventName: string, callback: DDCallback): GridStackDD;
    off(el: GridItemHTMLElement, eventName: string): GridStackDD;
}
