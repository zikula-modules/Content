/**
 * dd-utils.ts 5.1.1
 * Copyright (c) 2021 Alain Dumesny - see GridStack root license
 */
export declare class DDUtils {
    static isEventSupportPassiveOption: boolean;
    static clone(el: HTMLElement): HTMLElement;
    static appendTo(el: HTMLElement, parent: string | HTMLElement | Node): void;
    static setPositionRelative(el: HTMLElement): void;
    static addElStyles(el: HTMLElement, styles: {
        [prop: string]: string | string[];
    }): void;
    static initEvent<T>(e: DragEvent | MouseEvent, info: {
        type: string;
        target?: EventTarget;
    }): T;
    /** returns true if event is inside the given element rectangle */
    static inside(e: MouseEvent, el: HTMLElement): boolean;
}
