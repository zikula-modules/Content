"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.DDUtils = void 0;
/**
 * dd-utils.ts 5.1.1
 * Copyright (c) 2021 Alain Dumesny - see GridStack root license
 */
class DDUtils {
    static clone(el) {
        const node = el.cloneNode(true);
        node.removeAttribute('id');
        return node;
    }
    static appendTo(el, parent) {
        let parentNode;
        if (typeof parent === 'string') {
            parentNode = document.querySelector(parent);
        }
        else {
            parentNode = parent;
        }
        if (parentNode) {
            parentNode.appendChild(el);
        }
    }
    static setPositionRelative(el) {
        if (!(/^(?:r|a|f)/).test(window.getComputedStyle(el).position)) {
            el.style.position = "relative";
        }
    }
    static addElStyles(el, styles) {
        if (styles instanceof Object) {
            for (const s in styles) {
                if (styles.hasOwnProperty(s)) {
                    if (Array.isArray(styles[s])) {
                        // support fallback value
                        styles[s].forEach(val => {
                            el.style[s] = val;
                        });
                    }
                    else {
                        el.style[s] = styles[s];
                    }
                }
            }
        }
    }
    static initEvent(e, info) {
        const evt = { type: info.type };
        const obj = {
            button: 0,
            which: 0,
            buttons: 1,
            bubbles: true,
            cancelable: true,
            target: info.target ? info.target : e.target
        };
        // don't check for `instanceof DragEvent` as Safari use MouseEvent #1540
        if (e.dataTransfer) {
            evt['dataTransfer'] = e.dataTransfer; // workaround 'readonly' field.
        }
        ['altKey', 'ctrlKey', 'metaKey', 'shiftKey'].forEach(p => evt[p] = e[p]); // keys
        ['pageX', 'pageY', 'clientX', 'clientY', 'screenX', 'screenY'].forEach(p => evt[p] = e[p]); // point info
        return Object.assign(Object.assign({}, evt), obj);
    }
    /** returns true if event is inside the given element rectangle */
    // Note: Safari Mac has null event.relatedTarget which causes #1684 so check if DragEvent is inside the coordinates instead
    //    this.el.contains(event.relatedTarget as HTMLElement)
    static inside(e, el) {
        // srcElement, toElement, target: all set to placeholder when leaving simple grid, so we can't use that (Chrome)
        let target = e.relatedTarget || e.fromElement;
        if (!target) {
            const { bottom, left, right, top } = el.getBoundingClientRect();
            return (e.x < right && e.x > left && e.y < bottom && e.y > top);
        }
        return el.contains(target);
    }
}
exports.DDUtils = DDUtils;
DDUtils.isEventSupportPassiveOption = ((() => {
    let supportsPassive = false;
    let passiveTest = () => {
        // do nothing
    };
    document.addEventListener('test', passiveTest, {
        get passive() {
            supportsPassive = true;
            return true;
        }
    });
    document.removeEventListener('test', passiveTest);
    return supportsPassive;
})());
//# sourceMappingURL=dd-utils.js.map