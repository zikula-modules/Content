"use strict";
// dd-draggable.ts 3.1.2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * https://gridstackjs.com/
 * (c) 2020 rhlin, Alain Dumesny
 * gridstack.js may be freely distributed under the MIT license.
*/
const dd_manager_1 = require("./dd-manager");
const dd_utils_1 = require("./dd-utils");
const dd_base_impl_1 = require("./dd-base-impl");
class DDDraggable extends dd_base_impl_1.DDBaseImplement {
    constructor(el, option = {}) {
        super();
        /** @internal */
        this.dragging = false;
        /** @internal TODO: set to public as called by DDDroppable! */
        this.ui = () => {
            const containmentEl = this.el.parentElement;
            const containmentRect = containmentEl.getBoundingClientRect();
            const offset = this.helper.getBoundingClientRect();
            return {
                position: {
                    top: offset.top - containmentRect.top,
                    left: offset.left - containmentRect.left
                }
                /* not used by GridStack for now...
                helper: [this.helper], //The object arr representing the helper that's being dragged.
                offset: { top: offset.top, left: offset.left } // Current offset position of the helper as { top, left } object.
                */
            };
        };
        this.el = el;
        this.option = option;
        // create var event binding so we can easily remove and still look like TS methods (unlike anonymous functions)
        this._mouseDown = this._mouseDown.bind(this);
        this._dragStart = this._dragStart.bind(this);
        this._drag = this._drag.bind(this);
        this._dragEnd = this._dragEnd.bind(this);
        this._dragFollow = this._dragFollow.bind(this);
        this.el.draggable = true;
        this.el.classList.add('ui-draggable');
        this.el.addEventListener('mousedown', this._mouseDown);
        this.el.addEventListener('dragstart', this._dragStart);
    }
    on(event, callback) {
        super.on(event, callback);
    }
    off(event) {
        super.off(event);
    }
    enable() {
        super.enable();
        this.el.draggable = true;
        this.el.classList.remove('ui-draggable-disabled');
    }
    disable() {
        super.disable();
        this.el.draggable = false;
        this.el.classList.add('ui-draggable-disabled');
    }
    destroy() {
        if (this.dragging) {
            // Destroy while dragging should remove dragend listener and manually trigger
            // dragend, otherwise dragEnd can't perform dragstop because eventRegistry is
            // destroyed.
            this._dragEnd({});
        }
        this.el.draggable = false;
        this.el.classList.remove('ui-draggable');
        this.el.removeEventListener('mousedown', this._mouseDown);
        this.el.removeEventListener('dragstart', this._dragStart);
        delete this.el;
        delete this.helper;
        delete this.option;
        super.destroy();
    }
    updateOption(opts) {
        Object.keys(opts).forEach(key => this.option[key] = opts[key]);
        return this;
    }
    /** @internal call when mouse goes down before a dragstart happens */
    _mouseDown(event) {
        // make sure we are clicking on a drag handle or child of it...
        let className = this.option.handle.substring(1);
        let el = event.target;
        while (el && !el.classList.contains(className)) {
            el = el.parentElement;
        }
        this.mouseDownElement = el;
    }
    /** @internal */
    _dragStart(event) {
        if (!this.mouseDownElement) {
            event.preventDefault();
            return;
        }
        dd_manager_1.DDManager.dragElement = this;
        this.helper = this._createHelper(event);
        this._setupHelperContainmentStyle();
        this.dragOffset = this._getDragOffset(event, this.el, this.helperContainment);
        const ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dragstart' });
        if (this.helper !== this.el) {
            this._setupDragFollowNodeNotifyStart(ev);
        }
        else {
            this.dragFollowTimer = window.setTimeout(() => {
                delete this.dragFollowTimer;
                this._setupDragFollowNodeNotifyStart(ev);
            }, 0);
        }
        this._cancelDragGhost(event);
    }
    /** @internal */
    _setupDragFollowNodeNotifyStart(ev) {
        this._setupHelperStyle();
        document.addEventListener('dragover', this._drag, DDDraggable.dragEventListenerOption);
        this.el.addEventListener('dragend', this._dragEnd);
        if (this.option.start) {
            this.option.start(ev, this.ui());
        }
        this.dragging = true;
        this.helper.classList.add('ui-draggable-dragging');
        this.triggerEvent('dragstart', ev);
        return this;
    }
    /** @internal */
    _drag(event) {
        this._dragFollow(event);
        const ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'drag' });
        if (this.option.drag) {
            this.option.drag(ev, this.ui());
        }
        this.triggerEvent('drag', ev);
    }
    /** @internal */
    _dragEnd(event) {
        if (this.dragFollowTimer) {
            clearTimeout(this.dragFollowTimer);
            delete this.dragFollowTimer;
            return;
        }
        else {
            if (this.paintTimer) {
                cancelAnimationFrame(this.paintTimer);
            }
            document.removeEventListener('dragover', this._drag, DDDraggable.dragEventListenerOption);
            this.el.removeEventListener('dragend', this._dragEnd);
        }
        this.dragging = false;
        this.helper.classList.remove('ui-draggable-dragging');
        this.helperContainment.style.position = this.parentOriginStylePosition || null;
        if (this.helper === this.el) {
            this._removeHelperStyle();
        }
        else {
            this.helper.remove();
        }
        const ev = dd_utils_1.DDUtils.initEvent(event, { target: this.el, type: 'dragstop' });
        if (this.option.stop) {
            this.option.stop(ev); // Note: ui() not used by gridstack so don't pass
        }
        this.triggerEvent('dragstop', ev);
        delete dd_manager_1.DDManager.dragElement;
        delete this.helper;
        delete this.mouseDownElement;
    }
    /** @internal */
    _createHelper(event) {
        const helperIsFunction = (typeof this.option.helper) === 'function';
        const helper = (helperIsFunction
            ? this.option.helper.apply(this.el, [event])
            : (this.option.helper === "clone" ? dd_utils_1.DDUtils.clone(this.el) : this.el));
        if (!document.body.contains(helper)) {
            dd_utils_1.DDUtils.appendTo(helper, (this.option.appendTo === "parent"
                ? this.el.parentNode
                : this.option.appendTo));
        }
        if (helper === this.el) {
            this.dragElementOriginStyle = DDDraggable.originStyleProp.map(prop => this.el.style[prop]);
        }
        return helper;
    }
    /** @internal */
    _setupHelperStyle() {
        this.helper.style.pointerEvents = 'none';
        this.helper.style.width = this.dragOffset.width + 'px';
        this.helper.style.height = this.dragOffset.height + 'px';
        this.helper.style['willChange'] = 'left, top';
        this.helper.style.transition = 'none'; // show up instantly
        this.helper.style.position = this.option.basePosition || DDDraggable.basePosition;
        this.helper.style.zIndex = '1000';
        setTimeout(() => {
            if (this.helper) {
                this.helper.style.transition = null; // recover animation
            }
        }, 0);
        return this;
    }
    /** @internal */
    _removeHelperStyle() {
        // don't bother restoring styles if we're gonna remove anyway...
        let node = this.helper ? this.helper.gridstackNode : undefined;
        if (!node || !node._isAboutToRemove) {
            DDDraggable.originStyleProp.forEach(prop => {
                this.helper.style[prop] = this.dragElementOriginStyle[prop] || null;
            });
        }
        delete this.dragElementOriginStyle;
        return this;
    }
    /** @internal */
    _dragFollow(event) {
        if (this.paintTimer) {
            cancelAnimationFrame(this.paintTimer);
        }
        this.paintTimer = requestAnimationFrame(() => {
            delete this.paintTimer;
            const offset = this.dragOffset;
            let containmentRect = { left: 0, top: 0 };
            if (this.helper.style.position === 'absolute') {
                const { left, top } = this.helperContainment.getBoundingClientRect();
                containmentRect = { left, top };
            }
            this.helper.style.left = event.clientX + offset.offsetLeft - containmentRect.left + 'px';
            this.helper.style.top = event.clientY + offset.offsetTop - containmentRect.top + 'px';
        });
    }
    /** @internal */
    _setupHelperContainmentStyle() {
        this.helperContainment = this.helper.parentElement;
        if (this.option.basePosition !== 'fixed') {
            this.parentOriginStylePosition = this.helperContainment.style.position;
            if (window.getComputedStyle(this.helperContainment).position.match(/static/)) {
                this.helperContainment.style.position = 'relative';
            }
        }
        return this;
    }
    /** @internal */
    _cancelDragGhost(e) {
        if (e.dataTransfer != null) {
            e.dataTransfer.setData('text', '');
        }
        e.dataTransfer.effectAllowed = 'move';
        if ('function' === typeof DataTransfer.prototype.setDragImage) {
            e.dataTransfer.setDragImage(new Image(), 0, 0);
        }
        else {
            // ie
            e.target.style.display = 'none';
            setTimeout(() => {
                e.target.style.display = '';
            });
            e.stopPropagation();
            return;
        }
        e.stopPropagation();
        return this;
    }
    /** @internal */
    _getDragOffset(event, el, parent) {
        // in case ancestor has transform/perspective css properties that change the viewpoint
        let xformOffsetX = 0;
        let xformOffsetY = 0;
        if (parent) {
            const testEl = document.createElement('div');
            dd_utils_1.DDUtils.addElStyles(testEl, {
                opacity: '0',
                position: 'fixed',
                top: 0 + 'px',
                left: 0 + 'px',
                width: '1px',
                height: '1px',
                zIndex: '-999999',
            });
            parent.appendChild(testEl);
            const testElPosition = testEl.getBoundingClientRect();
            parent.removeChild(testEl);
            xformOffsetX = testElPosition.left;
            xformOffsetY = testElPosition.top;
            // TODO: scale ?
        }
        const targetOffset = el.getBoundingClientRect();
        return {
            left: targetOffset.left,
            top: targetOffset.top,
            offsetLeft: -event.clientX + targetOffset.left - xformOffsetX,
            offsetTop: -event.clientY + targetOffset.top - xformOffsetY,
            width: targetOffset.width,
            height: targetOffset.height
        };
    }
}
exports.DDDraggable = DDDraggable;
/** @internal */
DDDraggable.basePosition = 'absolute';
/** @internal */
DDDraggable.dragEventListenerOption = dd_utils_1.DDUtils.isEventSupportPassiveOption ? { capture: true, passive: true } : true;
/** @internal */
DDDraggable.originStyleProp = ['transition', 'pointerEvents', 'position',
    'left', 'top', 'opacity', 'zIndex', 'width', 'height', 'willChange'];
//# sourceMappingURL=dd-draggable.js.map