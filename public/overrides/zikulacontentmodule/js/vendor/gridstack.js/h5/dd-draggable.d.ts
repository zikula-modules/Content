/**
 * dd-draggable.ts 5.1.1
 * Copyright (c) 2021-2022 Alain Dumesny - see GridStack root license
 */
import { DDBaseImplement, HTMLElementExtendOpt } from './dd-base-impl';
import { DDUIData } from '../types';
export interface DDDraggableOpt {
    appendTo?: string | HTMLElement;
    containment?: string | HTMLElement;
    handle?: string;
    revert?: string | boolean | unknown;
    scroll?: boolean;
    helper?: string | HTMLElement | ((event: Event) => HTMLElement);
    start?: (event: Event, ui: DDUIData) => void;
    stop?: (event: Event) => void;
    drag?: (event: Event, ui: DDUIData) => void;
}
export declare class DDDraggable extends DDBaseImplement implements HTMLElementExtendOpt<DDDraggableOpt> {
    el: HTMLElement;
    option: DDDraggableOpt;
    helper: HTMLElement;
    constructor(el: HTMLElement, option?: DDDraggableOpt);
    on(event: 'drag' | 'dragstart' | 'dragstop', callback: (event: DragEvent) => void): void;
    off(event: 'drag' | 'dragstart' | 'dragstop'): void;
    enable(): void;
    disable(forDestroy?: boolean): void;
    destroy(): void;
    updateOption(opts: DDDraggableOpt): DDDraggable;
}
