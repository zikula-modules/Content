/** JQuery UI Drag&Drop plugin
 * https://gridstackjs.com/
 * (c) 2014-2020 Alain Dumesny, Dylan Weiss, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
import { GridStack, GridStackElement } from '../gridstack';
import { GridStackDD, DDOpts, DDKey, DDDropOpt, DDCallback, DDValue } from '../gridstack-dd';
import { GridItemHTMLElement, DDDragInOpt } from '../types';
import * as $ from './jquery';
export { $ };
import './jquery-ui';
/**
 * legacy Jquery-ui based drag'n'drop plugin.
 */
export declare class GridStackDDJQueryUI extends GridStackDD {
    constructor(grid: GridStack);
    resizable(el: GridItemHTMLElement, opts: DDOpts, key?: DDKey, value?: DDValue): GridStackDD;
    draggable(el: GridItemHTMLElement, opts: DDOpts, key?: DDKey, value?: DDValue): GridStackDD;
    dragIn(el: GridStackElement, opts: DDDragInOpt): GridStackDD;
    droppable(el: GridItemHTMLElement, opts: DDOpts | DDDropOpt, key?: DDKey, value?: DDValue): GridStackDD;
    isDroppable(el: GridItemHTMLElement): boolean;
    isDraggable(el: GridStackElement): boolean;
    on(el: GridItemHTMLElement, name: string, callback: DDCallback): GridStackDD;
    off(el: GridItemHTMLElement, name: string): GridStackDD;
}
