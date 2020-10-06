/**
 * https://gridstackjs.com/
 * (c) 2020 Alain Dumesny, rhlin
 * gridstack.js may be freely distributed under the MIT license.
*/
export declare class DDUtils {
    static clone(el: HTMLElement): HTMLElement;
    static appendTo(el: HTMLElement, parent: string | HTMLElement | Node): void;
    static setPositionRelative(el: any): void;
    static throttle(callback: (...args: any[]) => void, delay: number): (...args: any[]) => void;
    static addElStyles(el: HTMLElement, styles: {
        [prop: string]: string | string[];
    }): void;
    static copyProps(dst: any, src: any, props: any): void;
    static initEvent<T>(e: DragEvent | MouseEvent, info: {
        type: string;
        target?: EventTarget;
    }): T;
}
