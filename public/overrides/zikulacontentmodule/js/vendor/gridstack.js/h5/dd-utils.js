"use strict";
// dd-utils.ts 3.1.2 @preserve
Object.defineProperty(exports, "__esModule", { value: true });
/**
 * https://gridstackjs.com/
 * (c) 2020 rhlin, Alain Dumesny
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
        DDUtils._copyProps(evt, e, kbdProps);
        DDUtils._copyProps(evt, e, ptProps);
        DDUtils._copyProps(evt, obj, Object.keys(obj));
        return evt;
    }
    /** @internal */
    static _copyProps(dst, src, props) {
        for (let i = 0; i < props.length; i++) {
            const p = props[i];
            dst[p] = src[p];
        }
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