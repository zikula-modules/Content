"use strict";
// gridstack-ddi.ts 3.1.2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * Abstract Partial Interface API for drag'n'drop plugin - look at GridStackDD and HTML5 / Jquery implementation versions
 */
class GridStackDDI {
    /** call this method to register your plugin instead of the default no-op one */
    static registerPlugin(pluginClass) {
        GridStackDDI.ddi = new pluginClass();
    }
    /** get the current registered plugin to use */
    static get() {
        if (!GridStackDDI.ddi) {
            GridStackDDI.registerPlugin(GridStackDDI);
        }
        return GridStackDDI.ddi;
    }
    /** removes any drag&drop present (called during destroy) */
    remove(el) {
        return this; // no-op for static grids
    }
}
exports.GridStackDDI = GridStackDDI;
//# sourceMappingURL=gridstack-ddi.js.map