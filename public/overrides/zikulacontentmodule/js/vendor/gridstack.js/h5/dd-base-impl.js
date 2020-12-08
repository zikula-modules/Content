"use strict";
// dd-base-impl.ts 3.1.2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
class DDBaseImplement {
    constructor() {
        /** @internal */
        this._disabled = false;
        /** @internal */
        this._eventRegister = {};
    }
    /** returns the enable state, but you have to call enable()/disable() to change (as other things need to happen) */
    get disabled() { return this._disabled; }
    on(event, callback) {
        this._eventRegister[event] = callback;
    }
    off(event) {
        delete this._eventRegister[event];
    }
    enable() {
        this._disabled = false;
    }
    disable() {
        this._disabled = true;
    }
    destroy() {
        delete this._eventRegister;
    }
    triggerEvent(eventName, event) {
        if (this.disabled) {
            return;
        }
        if (!this._eventRegister) {
            return;
        } // used when destroy before triggerEvent fire
        if (this._eventRegister[eventName]) {
            return this._eventRegister[eventName](event);
        }
    }
}
exports.DDBaseImplement = DDBaseImplement;
//# sourceMappingURL=dd-base-impl.js.map