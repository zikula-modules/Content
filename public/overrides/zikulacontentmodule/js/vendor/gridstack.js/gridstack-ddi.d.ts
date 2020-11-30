/**
 * https://gridstackjs.com/
 * (c) 2014-2020 Alain Dumesny, Dylan Weiss, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
import { GridItemHTMLElement } from './types';
/**
 * Abstract Partial Interface API for drag'n'drop plugin - look at GridStackDD and HTML5 / Jquery implementation versions
 */
export declare class GridStackDDI {
    protected static ddi: GridStackDDI;
    /** call this method to register your plugin instead of the default no-op one */
    static registerPlugin(pluginClass: typeof GridStackDDI): void;
    /** get the current registered plugin to use */
    static get(): GridStackDDI;
    /** removes any drag&drop present (called during destroy) */
    remove(el: GridItemHTMLElement): GridStackDDI;
}
