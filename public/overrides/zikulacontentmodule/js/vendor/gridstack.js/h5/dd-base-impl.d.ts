/**
 * https://gridstackjs.com/
 * (c) 2020 rhlin, Alain Dumesny
 * gridstack.js may be freely distributed under the MIT license.
*/
export declare type EventCallback = (event: Event) => boolean | void;
export declare abstract class DDBaseImplement {
    /** returns the enable state, but you have to call enable()/disable() to change (as other things need to happen) */
    readonly disabled: boolean;
    on(event: string, callback: EventCallback): void;
    off(event: string): void;
    enable(): void;
    disable(): void;
    destroy(): void;
    triggerEvent(eventName: string, event: Event): boolean | void;
}
export interface HTMLElementExtendOpt<T> {
    el: HTMLElement;
    option: T;
    updateOption(T: any): DDBaseImplement;
}
