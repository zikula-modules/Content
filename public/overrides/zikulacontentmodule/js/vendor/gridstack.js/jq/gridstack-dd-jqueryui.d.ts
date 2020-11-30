/** JQuery UI Drag&Drop plugin
 * https://gridstackjs.com/
 * (c) 2014-2020 Alain Dumesny, Dylan Weiss, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
import { GridStackElement } from '../gridstack';
import { GridStackDD, DDOpts, DDKey, DDDropOpt, DDCallback, DDValue } from '../gridstack-dd';
import { GridItemHTMLElement, DDDragInOpt } from '../types';
import * as $ from './jquery';
export { $ };
import './jquery-ui';
export * from '../gridstack-dd';
/**
 * legacy Jquery-ui based drag'n'drop plugin.
 */
export declare class GridStackDDJQueryUI extends GridStackDD {
    resizable(el: GridItemHTMLElement, opts: DDOpts, key?: DDKey, value?: DDValue): GridStackDD;
    draggable(el: GridItemHTMLElement, opts: DDOpts, key?: DDKey, value?: DDValue): GridStackDD;
    dragIn(el: GridStackElement, opts: DDDragInOpt): GridStackDD;
    droppable(el: GridItemHTMLElement, opts: DDOpts | DDDropOpt, key?: DDKey, value?: DDValue): GridStackDD;
    isDroppable(el: HTMLElement): boolean;
    isDraggable(el: HTMLElement): boolean;
    isResizable(el: HTMLElement): boolean;
    on(el: GridItemHTMLElement, name: string, callback: DDCallback): GridStackDD;
    off(el: GridItemHTMLElement, name: string): GridStackDD;
}
