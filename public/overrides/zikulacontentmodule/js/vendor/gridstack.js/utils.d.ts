/**
 * https://gridstackjs.com/
 * (c) 2014-2020 Alain Dumesny, Dylan Weiss, Pavel Reznikov
 * gridstack.js may be freely distributed under the MIT license.
*/
import { GridStackElement, GridStackWidget, GridStackNode, GridStackOptions, numberOrString } from './types';
export interface HeightData {
    h: number;
    unit: string;
}
/** checks for obsolete method names */
export declare function obsolete(self: any, f: any, oldName: string, newName: string, rev: string): (...args: any[]) => any;
/** checks for obsolete grid options (can be used for any fields, but msg is about options) */
export declare function obsoleteOpts(opts: GridStackOptions, oldName: string, newName: string, rev: string): void;
/** checks for obsolete grid options which are gone */
export declare function obsoleteOptsDel(opts: GridStackOptions, oldName: string, rev: string, info: string): void;
/** checks for obsolete Jquery element attributes */
export declare function obsoleteAttr(el: HTMLElement, oldName: string, newName: string, rev: string): void;
/**
 * Utility methods
 */
export declare class Utils {
    /** convert a potential selector into actual list of html elements */
    static getElements(els: GridStackElement): HTMLElement[];
    /** convert a potential selector into actual single element */
    static getElement(els: GridStackElement): HTMLElement;
    /** returns true if a and b overlap */
    static isIntercepted(a: GridStackWidget, b: GridStackWidget): boolean;
    /**
     * Sorts array of nodes
     * @param nodes array to sort
     * @param dir 1 for asc, -1 for desc (optional)
     * @param width width of the grid. If undefined the width will be calculated automatically (optional).
     **/
    static sort(nodes: GridStackNode[], dir?: -1 | 1, column?: number): GridStackNode[];
    /**
     * creates a style sheet with style id under given parent
     * @param id will set the 'gs-style-id' attribute to that id
     * @param parent to insert the stylesheet as first child,
     * if none supplied it will be appended to the document head instead.
     */
    static createStylesheet(id: string, parent?: HTMLElement): CSSStyleSheet;
    /** removed the given stylesheet id */
    static removeStylesheet(id: string): void;
    /** inserts a CSS rule */
    static addCSSRule(sheet: CSSStyleSheet, selector: string, rules: string): void;
    static toBool(v: unknown): boolean;
    static toNumber(value: null | string): number;
    static parseHeight(val: numberOrString): HeightData;
    /** copies unset fields in target to use the given default sources values */
    static defaults(target: any, ...sources: any[]): {};
    /** given 2 objects return true if they have the same values. Checks for Object {} having same fields and values (just 1 level down) */
    static same(a: unknown, b: unknown): boolean;
    /** makes a shallow copy of the passed json struct */
    static clone(target: {}): {};
    /** return the closest parent matching the given class */
    static closestByClass(el: HTMLElement, name: string): HTMLElement;
    /** delay calling the given function by certain amount of time */
    static throttle(callback: () => void, delay: number): () => void;
    static removePositioningStyles(el: HTMLElement): void;
}
