import { GridStackNode } from './types';
export declare type onChangeCB = (nodes: GridStackNode[], removeDOM?: boolean) => void;
/** options used for creations - similar to GridStackOptions */
export interface GridStackEngineOptions {
    column?: number;
    maxRow?: number;
    float?: boolean;
    nodes?: GridStackNode[];
    onChange?: onChangeCB;
}
/**
 * Defines the GridStack engine that does most no DOM grid manipulation.
 * See GridStack methods and vars for descriptions.
 *
 * NOTE: values should not be modified directly - call the main GridStack API instead
 */
export declare class GridStackEngine {
    column: number;
    maxRow: number;
    nodes: GridStackNode[];
    onChange: onChangeCB;
    addedNodes: GridStackNode[];
    removedNodes: GridStackNode[];
    batchMode: boolean;
    constructor(opts?: GridStackEngineOptions);
    batchUpdate(): GridStackEngine;
    commit(): GridStackEngine;
    isAreaEmpty(x: number, y: number, w: number, h: number): boolean;
    /** re-layout grid items to reclaim any empty space */
    compact(): GridStackEngine;
    /** enable/disable floating widgets (default: `false`) See [example](http://gridstackjs.com/demo/float.html) */
    /** float getter method */
    float: boolean;
    /**
     * given a random node, makes sure it's coordinates/values are valid in the current grid
     * @param node to adjust
     * @param resizing if out of bound, resize down or move into the grid to fit ?
     */
    prepareNode(node: GridStackNode, resizing?: boolean): GridStackNode;
    getDirtyNodes(verify?: boolean): GridStackNode[];
    cleanNodes(): GridStackEngine;
    addNode(node: GridStackNode, triggerAddEvent?: boolean): GridStackNode;
    removeNode(node: GridStackNode, removeDOM?: boolean, triggerEvent?: boolean): GridStackEngine;
    removeAll(removeDOM?: boolean): GridStackEngine;
    canMoveNode(node: GridStackNode, x: number, y: number, w?: number, h?: number): boolean;
    /** return true if can fit in grid height constrain only (always true if no maxRow) */
    willItFit(node: GridStackNode): boolean;
    isNodeChangedPosition(node: GridStackNode, x: number, y: number, w?: number, h?: number): boolean;
    moveNode(node: GridStackNode, x: number, y: number, w?: number, h?: number, noPack?: boolean): GridStackNode;
    getRow(): number;
    beginUpdate(node: GridStackNode): GridStackEngine;
    endUpdate(): GridStackEngine;
    /** saves the current layout returning a list of widgets for serialization */
    save(saveElement?: boolean): GridStackNode[];
    /**
     * call to cache the given layout internally to the given location so we can restore back when column changes size
     * @param nodes list of nodes
     * @param column corresponding column index to save it under
     * @param clear if true, will force other caches to be removed (default false)
     */
    cacheLayout(nodes: GridStackNode[], column: number, clear?: boolean): GridStackEngine;
    /** called to remove all internal values */
    cleanupNode(node: GridStackNode): GridStackEngine;
}
