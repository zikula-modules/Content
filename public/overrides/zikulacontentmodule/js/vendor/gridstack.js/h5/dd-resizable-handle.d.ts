/**
 * https://gridstackjs.com/
 * (c) 2020 rhlin, Alain Dumesny
 * gridstack.js may be freely distributed under the MIT license.
*/
export interface DDResizableHandleOpt {
    start?: (event: any) => void;
    move?: (event: any) => void;
    stop?: (event: any) => void;
}
export declare class DDResizableHandle {
    constructor(host: HTMLElement, direction: string, option: DDResizableHandleOpt);
    init(): DDResizableHandle;
    destroy(): DDResizableHandle;
}
