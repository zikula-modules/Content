"use strict";
/**
 * dd-droppable.ts 5.0
 * Copyright (c) 2021 Alain Dumesny - see GridStack root license
 */
Object.defineProperty(exports, "__esModule", { value: true });
exports.DDDroppable = void 0;
const dd_manager_1 = require("./dd-manager");
const dd_base_impl_1 = require("./dd-base-impl");
const dd_utils_1 = require("./dd-utils");
// TEST let count = 0;
class DDDroppable extends dd_base_impl_1.DDBaseImplement {
    constructor(el, opts = {}) {
        super();
        this.el = el;
        this.option = opts;
        // create var event binding so we can easily remove and still look like TS methods (unlike anonymous functions)
        this._dragEnter = this._dragEnter.bind(this);
        this._dragOver = this._dragOver.bind(this);
        this._dragLeave = this._dragLeave.bind(this);
        this._drop = this._drop.bind(this);
        this.el.classList.add('ui-droppable');
        this.el.addEventListener('dragenter', this._dragEnter);
        this._setupAccept();
    }
    on(event, callback) {
        super.on(event, callback);
    }
    off(event) {
        super.off(event);
    }
    enable() {
        if (!this.disabled)
            return;
        super.enable();
        this.el.classList.remove('ui-droppable-disabled');
        this.el.addEventListener('dragenter', this._dragEnter);
    }
    disable(forDestroy = false) {
        if (this.disabled)
            return;
        super.disable();
        if (!forDestroy)
            this.el.classList.add('ui-droppable-disabled');
        this.el.removeEventListener('dragenter', this._dragEnter);
    }
    destroy() {
        this._removeLeaveCallbacks();
        this.disable(true);
        this.el.classList.remove('ui-droppable');
        this.el.classList.remove('ui-droppable-disabled');
        super.destroy();
    }
    updateOption(opts) {
        Object.keys(opts).forEach(key => this.option[key] = opts[key]);
        this._setupAccept();
        return this;
    }
    /** @internal called when the cursor enters our area - prepare for a possible drop and track leaving */
    _dragEnter(event) {
        // TEST console.log(`${count++} Enter ${(this.el as GridHTMLElement).gridstack.opts.id}`);
        if (!this._canDrop())
            return;
        event.preventDefault();
        event.stopPropagation();
        // ignore multiple 'dragenter' as we go over existing items
        if (this.moving)
            return;
        this.moving = true;
        const ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dropover' });
        if (this.option.over) {
            this.option.over(ev, this._ui(dd_manager_1.DDManager.dragElement));
        }
        this.triggerEvent('dropover', ev);
        this.el.addEventListener('dragover', this._dragOver);
        this.el.addEventListener('drop', this._drop);
        this.el.addEventListener('dragleave', this._dragLeave);
        // Update: removed that as it causes nested grids to no receive dragenter events when parent drags and sets this for #992. not seeing cursor flicker (chrome).
        // this.el.classList.add('ui-droppable-over');
        // make sure when we enter this, that the last one gets a leave to correctly cleanup as we don't always do
        if (DDDroppable.lastActive && DDDroppable.lastActive !== this) {
            DDDroppable.lastActive._dragLeave(event, true);
        }
        DDDroppable.lastActive = this;
    }
    /** @internal called when an moving to drop item is being dragged over - do nothing but eat the event */
    _dragOver(event) {
        event.preventDefault();
        event.stopPropagation();
    }
    /** @internal called when the item is leaving our area, stop tracking if we had moving item */
    _dragLeave(event, forceLeave) {
        var _a;
        // TEST console.log(`${count++} Leave ${(this.el as GridHTMLElement).gridstack.opts.id}`);
        event.preventDefault();
        event.stopPropagation();
        // ignore leave events on our children (we get them when starting to drag our items)
        // but exclude nested grids since we would still be leaving ourself, 
        // but don't handle leave if we're dragging a nested grid around
        if (!forceLeave) {
            let onChild = dd_utils_1.DDUtils.inside(event, this.el);
            let drag = dd_manager_1.DDManager.dragElement.el;
            if (onChild && !((_a = drag.gridstackNode) === null || _a === void 0 ? void 0 : _a.subGrid)) { // dragging a nested grid ?
                let nestedEl = this.el.gridstack.engine.nodes.filter(n => n.subGrid).map(n => n.subGrid.el);
                onChild = !nestedEl.some(el => dd_utils_1.DDUtils.inside(event, el));
            }
            if (onChild)
                return;
        }
        if (this.moving) {
            const ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dropout' });
            if (this.option.out) {
                this.option.out(ev, this._ui(dd_manager_1.DDManager.dragElement));
            }
            this.triggerEvent('dropout', ev);
        }
        this._removeLeaveCallbacks();
        if (DDDroppable.lastActive === this) {
            delete DDDroppable.lastActive;
        }
    }
    /** @internal item is being dropped on us - call the client drop event */
    _drop(event) {
        if (!this.moving)
            return; // should not have received event...
        event.preventDefault();
        const ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'drop' });
        if (this.option.drop) {
            this.option.drop(ev, this._ui(dd_manager_1.DDManager.dragElement));
        }
        this.triggerEvent('drop', ev);
        this._removeLeaveCallbacks();
    }
    /** @internal called to remove callbacks when leaving or dropping */
    _removeLeaveCallbacks() {
        if (!this.moving) {
            return;
        }
        delete this.moving;
        this.el.removeEventListener('dragover', this._dragOver);
        this.el.removeEventListener('drop', this._drop);
        this.el.removeEventListener('dragleave', this._dragLeave);
        // Update: removed that as it causes nested grids to no receive dragenter events when parent drags and sets this for #992. not seeing cursor flicker (chrome).
        // this.el.classList.remove('ui-droppable-over');
    }
    /** @internal */
    _canDrop() {
        return dd_manager_1.DDManager.dragElement && (!this.accept || this.accept(dd_manager_1.DDManager.dragElement.el));
    }
    /** @internal */
    _setupAccept() {
        if (this.option.accept && typeof this.option.accept === 'string') {
            this.accept = (el) => {
                return el.matches(this.option.accept);
            };
        }
        else {
            this.accept = this.option.accept;
        }
        return this;
    }
    /** @internal */
    _ui(drag) {
        return Object.assign({ draggable: drag.el }, drag.ui());
    }
}
exports.DDDroppable = DDDroppable;
//# sourceMappingURL=dd-droppable.js.map