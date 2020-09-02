"use strict";
// gridstack-dd.ts 2.0.0-rc2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * Base class for drag'n'drop plugin.
 */
class GridStackDD {
    constructor(grid) {
        this.grid = grid;
    }
    /** call this method to register your plugin instead of the default no-op one */
    static registerPlugin(pluginClass) {
        GridStackDD.registeredPlugins.push(pluginClass);
    }
    /** get the current registered plugin to use */
    static get() {
        return GridStackDD.registeredPlugins[0] || GridStackDD;
    }
    resizable(el, opts, key, value) {
        return this;
    }
    draggable(el, opts, key, value) {
        return this;
    }
    dragIn(el, opts) {
        return this;
    }
    isDraggable(el) {
        return false;
    }
    droppable(el, opts, key, value) {
        return this;
    }
    isDroppable(el) {
        return false;
    }
    on(el, eventName, callback) {
        return this;
    }
    off(el, eventName) {
        return this;
    }
}
GridStackDD.registeredPlugins = [];
exports.GridStackDD = GridStackDD;
//# sourceMappingURL=gridstack-dd.js.map