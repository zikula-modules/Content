"use strict";
// dd-utils.ts 2.0.2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * https://gridstackjs.com/
 * (c) 2020 Alain Dumesny, rhlin
 * gridstack.js may be freely distributed under the MIT license.
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
            parentNode.append(el);
        }
    }
    static setPositionRelative(el) {
        if (!(/^(?:r|a|f)/).test(window.getComputedStyle(el).position)) {
            el.style.position = "relative";
        }
    }
    static throttle(callback, delay) {
        let isWaiting = false;
        return (...args) => {
            if (!isWaiting) {
                callback(...args);
                isWaiting = true;
                setTimeout(() => isWaiting = false, delay);
            }
        };
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
    static copyProps(dst, src, props) {
        for (let i = 0; i < props.length; i++) {
            const p = props[i];
            dst[p] = src[p];
        }
    }
    static initEvent(e, info) {
        const kbdProps = 'altKey,ctrlKey,metaKey,shiftKey'.split(',');
        const ptProps = 'pageX,pageY,clientX,clientY,screenX,screenY'.split(',');
        const evt = { type: info.type };
        const obj = {
            button: 0,
            which: 0,
            buttons: 1,
            bubbles: true,
            cancelable: true,
            originEvent: e,
            target: info.target ? info.target : e.target
        };
        if (e instanceof DragEvent) {
            Object.assign(obj, { dataTransfer: e.dataTransfer });
        }
        DDUtils.copyProps(evt, e, kbdProps);
        DDUtils.copyProps(evt, e, ptProps);
        DDUtils.copyProps(evt, obj, Object.keys(obj));
        return evt;
    }
}
exports.DDUtils = DDUtils;
//# sourceMappingURL=dd-utils.js.map