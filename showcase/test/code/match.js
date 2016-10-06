// Match two javascript data structures, including wildcards for property values
// only tested for primitive data structures created from JSON files
//
// wildcard property value format:
//  "**any**" : property must exist, but can have any value
//  "**typeof <type>**" : property values must be of the specified <type>
//                    example: {"amount": "**typeof number**"}
//  "**eval (<fn>)**" : property value evaluation for x must be truthy
//                      example: {"amount": "**eval (x>0)**"}
"use strict";
var safeEval = require("safe-eval");
function match(actual, expected, opts) {
    "use strict";
    if (opts === void 0) { opts = { strict: true, wildcard: true }; }
    // All identical values are equivalent, as determined by ===.
    if (actual === expected) {
        return true;
    }
    else if (actual instanceof Date && expected instanceof Date) {
        return actual.getTime() === expected.getTime();
    }
    else if (!actual || !expected || typeof actual !== "object" && typeof expected !== "object") {
        return opts.strict ? false : actual == expected;
    }
    else {
        return objMatch(actual, expected, opts);
    }
}
Object.defineProperty(exports, "__esModule", { value: true });
exports.default = match;
function objMatch(a, b, opts) {
    "use strict";
    if (opts === void 0) { opts = { strict: true, wildcard: true }; }
    // check strict identity
    if (a === b) {
        return true;
    }
    // discard empty objects
    if (a === null || a === undefined || b === null || b === undefined) {
        return false;
    }
    // check array
    if (b.constructor === Array) {
        if (a.constructor !== Array || a.length !== b.length) {
            return false;
        }
        for (var i = 0, len = a.length; i < len; i++) {
            if (!objMatch(a[i], b[i], opts)) {
                return false;
            }
        }
        return true;
    }
    // check object
    if (typeof b === "object") {
        if (typeof a !== "object" || Object.keys(a).length !== Object.keys(b).length) {
            return false;
        }
        for (var key in a) {
            if (!objMatch(a[key], b[key], opts)) {
                return false;
            }
        }
        return true;
    }
    // check wildcards
    if (opts.wildcard && b.constructor === String && b.slice(0, 2) === "**") {
        if (b === "**any**") {
            return true;
        }
        if (b.slice(0, 9) === "**typeof " && b.slice(-2) === "**") {
            return typeof a === b.slice(9, b.length - 2);
        }
        if (b.slice(0, 8) === "**eval (" && b.slice(-3) === ")**") {
            return safeEval(b.slice(8, b.length - 3), { x: a });
        }
    }
    // no strict match
    return opts.strict ? false : a == b;
}

//# sourceMappingURL=match.js.map
