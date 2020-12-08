"use strict";
// dd-resizable-handle.ts 3.1.2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
class DDResizableHandle {
    constructor(host, direction, option) {
        /** @internal */
        this.mouseMoving = false;
        /** @internal */
        this.started = false;
        this.host = host;
        this.dir = direction;
        this.option = option;
        // create var event binding so we can easily remove and still look like TS methods (unlike anonymous functions)
        this._mouseDown = this._mouseDown.bind(this);
        this._mouseMove = this._mouseMove.bind(this);
        this._mouseUp = this._mouseUp.bind(this);
        this.init();
    }
    init() {
        const el = document.createElement('div');
        el.classList.add('ui-resizable-handle');
        el.classList.add(`${DDResizableHandle.prefix}${this.dir}`);
        el.style.zIndex = '100';
        el.style.userSelect = 'none';
        this.el = el;
        this.host.appendChild(this.el);
        this.el.addEventListener('mousedown', this._mouseDown);
        return this;
    }
    destroy() {
        this.host.removeChild(this.el);
        return this;
    }
    /** @internal */
    _mouseDown(event) {
        this.mouseDownEvent = event;
        setTimeout(() => {
            document.addEventListener('mousemove', this._mouseMove, true);
            document.addEventListener('mouseup', this._mouseUp);
            setTimeout(() => {
                if (!this.mouseMoving) {
                    document.removeEventListener('mousemove', this._mouseMove, true);
                    document.removeEventListener('mouseup', this._mouseUp);
                    delete this.mouseDownEvent;
                }
            }, 300);
        }, 100);
    }
    /** @internal */
    _mouseMove(event) {
        if (!this.started && !this.mouseMoving) {
            if (this._hasMoved(event, this.mouseDownEvent)) {
                this.mouseMoving = true;
                this._triggerEvent('start', this.mouseDownEvent);
                this.started = true;
            }
        }
        if (this.started) {
            this._triggerEvent('move', event);
        }
    }
    /** @internal */
    _mouseUp(event) {
        if (this.mouseMoving) {
            this._triggerEvent('stop', event);
        }
        document.removeEventListener('mousemove', this._mouseMove, true);
        document.removeEventListener('mouseup', this._mouseUp);
        this.mouseMoving = false;
        this.started = false;
        delete this.mouseDownEvent;
    }
    /** @internal */
    _hasMoved(event, oEvent) {
        const { clientX, clientY } = event;
        const { clientX: oClientX, clientY: oClientY } = oEvent;
        return (Math.abs(clientX - oClientX) > 1
            || Math.abs(clientY - oClientY) > 1);
    }
    /** @internal */
    _triggerEvent(name, event) {
        if (this.option[name]) {
            this.option[name](event);
        }
        return this;
    }
}
exports.DDResizableHandle = DDResizableHandle;
/** @internal */
DDResizableHandle.prefix = 'ui-resizable-';
//# sourceMappingURL=dd-resizable-handle.js.map