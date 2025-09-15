(function (window, Element, undefined) {
    'use strict';

    var prefix = '',
        eventPrefix,
        vendors = { Webkit: 'webkit', Moz: '', O: 'o', ms: 'MS' },
        document = window.document,
        testEl = document.createElement('div'),
        supportedTransforms = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
        transform,
        transitionProperty, transitionDuration, transitionTiming,
        animationName,
        animationDuration,
        animationTiming,
        cssReset = {};

    /**
     * @param {string} str
     * @returns {string}
     */
    function dasherize (str) {
        return downcase(str.replace(/([a-z])([A-Z])/, '$1-$2'));
    }

    /**
     * @param {string} str
     * @returns {string}
     */
    function downcase (str) {
        return str.toLowerCase();
    }

    /**
     * @param {string} name
     * @returns {string}
     */
    function normalizeEvent (name) {
        return eventPrefix ? eventPrefix + name : downcase(name);
    }

    /**
     * @param {Element} element
     * @param {object} props
     */
    function css (element, props) {
        var style = element['style'];
        if (props) {
            for (var prop in props) {
                if (props.hasOwnProperty(prop)) {
                    style[prop] = props[prop];
                }
            }
        }
    }

    for (var vendor in vendors) {
        if (vendors.hasOwnProperty(vendor)) {
            if (testEl.style[vendor + 'TransitionProperty'] !== undefined) {
                prefix = "-" + downcase(vendor) + "-";
                eventPrefix = vendors[vendor];
                break;
            }
        }
    }

    transform = prefix + "transform";
        cssReset[transitionProperty = prefix + "transition-property"] =
        cssReset[transitionDuration = prefix + "transition-duration"] =
        cssReset[transitionTiming   = prefix + "transition-timing-function"] =
        cssReset[animationName      = prefix + "animation-name"] =
        cssReset[animationDuration  = prefix + "animation-duration"] =
        cssReset[animationTiming    = prefix + "animation-timing-function"] = "";

    window.fx = {
        off: (eventPrefix === undefined && testEl.style[transitionProperty] === undefined),
        speeds: { _default: 400, fast: 200, slow: 600 },
        cssPrefix: prefix,
        transitionEnd: normalizeEvent('TransitionEnd'),
        animationEnd: normalizeEvent('AnimationEnd')
    };

    function animate (element, properties, duration, ease, callback) {
        var key,
            cssValues = {},
            cssProperties,
            transforms = '',
            wrappedCallback,
            endEvent = window.fx.transitionEnd;

        if (duration === undefined) {
            duration = 0.4;
        }

        if (window.fx.off) {
            duration = 0;
        }

        if (typeof properties == 'string') {
            // keyframe animation
            cssValues[animationName] = properties;
            cssValues[animationDuration] = duration + "s";
            cssValues[animationTiming] = (ease || "linear");
            endEvent = window.fx.animationEnd;
        } else {
            cssProperties = [];
            // CSS transitions
            for (key in properties) {
                if (properties.hasOwnProperty(key)) {
                    if (supportedTransforms.test(key)) {
                        transforms += key + "(" + properties[key] + ") ";
                    } else {
                        cssValues[key] = properties[key];
                        cssProperties.push(dasherize(key));
                    }
                }
            }
        }

        if (transforms) {
            cssValues[transform] = transforms;
            cssProperties.push(transform);
        }
        if (duration > 0 && typeof properties === "object") {
            cssValues[transitionProperty] = cssProperties.join(", ");
            cssValues[transitionDuration] = duration + "s";
            cssValues[transitionTiming] = (ease || "linear");
        }

        wrappedCallback = function (event) {
            if (typeof event !== "undefined") {
                if (event.target !== event.currentTarget) {
                    return; // makes sure the event didn't bubble from "below"
                }
                event = new CustomEvent('fxEnd', {
                    detail: {},
                    bubbles: true,
                    cancelable: true
                });
                element.dispatchEvent(event);
            }
            css(element, cssReset);
            callback && callback.call(element)
        };

        if (duration > 0 && !element.__fxEndWrapper) {
            element.addEventListener(endEvent, wrappedCallback, false);
            element.__fxEndWrapper = true;
        }

        // trigger page reflow so new elements can animate
        element.clientLeft = element.clientLeft;
        css(element, cssValues);

        if (duration <= 0) {
            setTimeout(function () {
                wrappedCallback.call(element);
            }, 0);
        }

        return element;
    }

    Element.prototype.fx = function (properties, duration, ease, callback) {
        if (duration !== null && typeof duration === 'object') {
            ease = duration.easing;
            callback = duration.complete;
            duration = duration.duration;
        }
        if (duration) {
            duration = (typeof duration == "number" ? duration : (window.fx.speeds[duration] || window.fx.speeds._default)) / 1000;
        }
        return animate(this, properties, duration, ease, callback);
    };

    testEl = null;
}(window, Element));