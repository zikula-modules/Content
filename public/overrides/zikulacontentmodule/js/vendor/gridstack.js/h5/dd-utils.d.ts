/**
 * https://gridstackjs.com/
 * (c) 2020 rhlin, Alain Dumesny
 * gridstack.js may be freely distributed under the MIT license.
*/
export declare class DDUtils {
    static isEventSupportPassiveOption: boolean;
    static clone(el: HTMLElement): HTMLElement;
    static appendTo(el: HTMLElement, parent: string | HTMLElement | Node): void;
    static setPositionRelative(el: any): void;
    static addElStyles(el: HTMLElement, styles: {
        [prop: string]: string | string[];
    }): void;
    static initEvent<T>(e: DragEvent | MouseEvent, info: {
        type: string;
        target?: EventTarget;
    }): T;
}
