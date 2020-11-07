"use strict";
// gridstack-dd-jqueryui.ts 2.2.0 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
exports.GridStackDDJQueryUI = exports.$ = void 0;
const gridstack_dd_1 = require("../gridstack-dd");
// export jq symbols see
// https://stackoverflow.com/questions/35345760/importing-jqueryui-with-typescript-and-requirejs
// https://stackoverflow.com/questions/33998262/jquery-ui-and-webpack-how-to-manage-it-into-module
// TODO: let user bring their own jq or jq-ui version
const $ = require("./jquery"); // compile this in... having issues TS/ES6 app would include instead
exports.$ = $;
require("./jquery-ui");
/**
 * legacy Jquery-ui based drag'n'drop plugin.
 */
class GridStackDDJQueryUI extends gridstack_dd_1.GridStackDD {
    constructor(grid) {
        super(grid);
    }
    resizable(el, opts, key, value) {
        let $el = $(el);
        if (opts === 'enable') {
            $el.resizable().resizable(opts);
        }
        else if (opts === 'disable' || opts === 'destroy') {
            if ($el.data('ui-resizable')) { // error to call destroy if not there
                $el.resizable(opts);
            }
        }
        else if (opts === 'option') {
            $el.resizable(opts, key, value);
        }
        else {
            let handles = $el.data('gs-resize-handles') ? $el.data('gs-resize-handles') : this.grid.opts.resizable.handles;
            $el.resizable(Object.assign(Object.assign(Object.assign({}, this.grid.opts.resizable), { handles: handles }), {
                start: opts.start,
                stop: opts.stop,
                resize: opts.resize // || function() {}
            }));
        }
        return this;
    }
    draggable(el, opts, key, value) {
        let $el = $(el);
        if (opts === 'enable') {
            $el.draggable().draggable('enable');
        }
        else if (opts === 'disable' || opts === 'destroy') {
            if ($el.data('ui-draggable')) { // error to call destroy if not there
                $el.draggable(opts);
            }
        }
        else if (opts === 'option') {
            $el.draggable(opts, key, value);
        }
        else {
            $el.draggable(Object.assign(Object.assign({}, this.grid.opts.draggable), {
                containment: (this.grid.opts._isNested && !this.grid.opts.dragOut) ?
                    $(this.grid.el).parent() : (this.grid.opts.draggable.containment || null),
                start: opts.start,
                stop: opts.stop,
                drag: opts.drag // || function() {}
            }));
        }
        return this;
    }
    dragIn(el, opts) {
        let $el = $(el); // workaround Type 'string' is not assignable to type 'PlainObject<any>' - see https://github.com/DefinitelyTyped/DefinitelyTyped/issues/29312
        $el.draggable(opts);
        return this;
    }
    droppable(el, opts, key, value) {
        let $el = $(el);
        if (typeof opts.accept === 'function' && !opts._accept) {
            // convert jquery event to generic element
            opts._accept = opts.accept;
            opts.accept = ($el) => opts._accept($el.get(0));
        }
        $el.droppable(opts, key, value);
        return this;
    }
    isDroppable(el) {
        let $el = $(el);
        return Boolean($el.data('ui-droppable'));
    }
    isDraggable(el) {
        let $el = $(el); // workaround Type 'string' is not assignable to type 'PlainObject<any>' - see https://github.com/DefinitelyTyped/DefinitelyTyped/issues/29312
        return Boolean($el.data('ui-draggable'));
    }
    on(el, name, callback) {
        let $el = $(el);
        $el.on(name, (event, ui) => { callback(event, ui.draggable ? ui.draggable[0] : event.target, ui.helper ? ui.helper[0] : null); });
        return this;
    }
    off(el, name) {
        let $el = $(el);
        $el.off(name);
        return this;
    }
}
exports.GridStackDDJQueryUI = GridStackDDJQueryUI;
// finally register ourself
gridstack_dd_1.GridStackDD.registerPlugin(GridStackDDJQueryUI);
//# sourceMappingURL=gridstack-dd-jqueryui.js.map